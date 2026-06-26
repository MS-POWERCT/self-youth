<?php

namespace App\Api;

use App\Models\FarmTask;
use App\Models\FarmUserTask;
use Illuminate\Support\Facades\Auth;
use App\Services\FarmTaskService;
use App\Services\FarmUserService;
use App\Services\FarmWarehouseService;
use App\Services\WalletAssetService;
use App\Support\Response;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

/**
 * Description of My
 *
 * @author Administrator
 */
class FarmTaskController extends Controller
{

    // 获取任务列表
    public function getList()
    {
        $user_id = Auth::id();
        $today = date('Ymd');
        $taskDateKey = 'farm_task_d:' . $today; // 检查用户是否已领取任务

        $userTaskList = FarmUserTask::with('farmTask', 'farmTask.rewardAsset')
            ->select('id', 'user_id', 'farm_task_id', 'status')
            ->where('user_id', $user_id)
            ->where('status', 0)
            ->get();

        if (Redis::sIsMember($taskDateKey, $user_id)) {
            return Response::success($userTaskList);
        }

        $farm_user_level = FarmUserService::getFarmUserLevel($user_id);
        $taskNumber = FarmTaskService::getTaskNumber($farm_user_level);

        // 获取所有符合条件的任务
        $availableTasks = FarmTask::where('level_id', '<=', $farm_user_level)->get();

        // 如果任务总数小于需要的数量，返回全部
        if ($availableTasks->count() < $taskNumber) {
            return Response::success($availableTasks);
        }

        // 检查还有几个任务，进行补齐
        $remainingTasks = $taskNumber - count($userTaskList);

        // 如果已有任务足够，直接返回
        if ($remainingTasks <= 0) {
            Redis::sAdd($taskDateKey, $user_id);
            return Response::success($userTaskList);
        }


        // 排除已领取的任务
        $excludeIds = $userTaskList->pluck('farm_task_id')->toArray();
        $taskPool = $availableTasks->reject(fn($task) => in_array($task->id, $excludeIds));

        // 抽取新任务
        $newTasks = FarmTaskService::weightedRandomSelection($taskPool, $remainingTasks);

        // 批量保存
        if ($newTasks->isNotEmpty()) {
            $insertData = $newTasks->map(fn($task) => [
                'user_id' => $user_id,
                'farm_task_id' => $task->id,
            ])->toArray();

            FarmUserTask::insert($insertData);
        }

        // 标记已领取
        Redis::sAdd($taskDateKey, $user_id);

        // 返回完整的任务列表
        $userTaskList = FarmUserTask::with('farmTask', 'farmTask.rewardAsset')
            ->select('id', 'user_id', 'farm_task_id', 'status')
            ->where('user_id', $user_id)
            ->where('status', 0)
            ->get();

        return Response::success($userTaskList);
    }

    // 交付任务
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return Response::error('参数错误', 1212);
        }

        $user = Auth::user();

        $detail = FarmUserTask::with('farmTask', 'farmTask.rewardAsset')->where('user_id', $user->id)->where('id', $request->id)->where('status', 0)->first();
        if (!$detail) {
            return Response::error('任务不存在', 456346);
        }

        // 得到任务要求
        $taskNeed = $detail->farmTask->task_need;
        // 得到handbook_id
        $handbookIds = array_column($taskNeed, 'handbook_id');

        // 检查用户仓库是否有足够的资源
        $warehouseNum = FarmWarehouseService::getWareHouseList($user, $handbookIds);
        if ($warehouseNum->isEmpty()) {
            return Response::error('用户仓库资源不足', 456346);
        }
        foreach ($taskNeed as $key => $value) {
            if ($warehouseNum->where('handbook_id', $value['handbook_id'])->where('type', 'fruit')->value('num') < $value['quantity']) {
                return Response::error('用户仓库资源不足,需要' . $value['quantity'] . '个', 456346);
            }
        }

        // 扣除用户仓库资源
        foreach ($taskNeed as $key => $value) {
            $warehouse = $warehouseNum->where('handbook_id', $value['handbook_id'])->first();
            $warehouse->num = $warehouse->num - $value['quantity'];
            $warehouse->save();
        }

        // 给用户奖励
        FarmUserService::farmAddExp($user->id, $detail->farmTask->reward_exp); // 增加经验
        $wallet_asset = WalletAssetService::getWalletAsset($user, $detail->farmTask->reward_asset_id);
        WalletAssetService::change($wallet_asset, $detail->farmTask->reward_gold, [
            'module_code' => 'FARM_TASK',
        ]);

        // 更新任务状态为已完成
        $detail->status = 1;
        $detail->ok_at = now();
        $detail->save();

        // 把剩下的任务返回
        $remainingTasks = FarmUserTask::with('farmTask', 'farmTask.rewardAsset')->where('user_id', $user->id)->where('status', 0)->get();

        return Response::success($remainingTasks);
    }

    // 放弃任务
    public function cancel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return Response::error('参数错误', 1212);
        }

        $user_id = Auth::id();

        $detail = FarmUserTask::where('user_id', $user_id)->where('id', $request->id)->where('status', 0)->first();
        if (!$detail) {
            return Response::error('任务不存在', 456346);
        }

        $detail->status = 2;
        $detail->save();

        // 把剩下的任务返回
        $remainingTasks = FarmUserTask::with('farmTask', 'farmTask.rewardAsset')->where('user_id', $user_id)->where('status', 0)->get();
        return Response::success($remainingTasks);
    }
}
