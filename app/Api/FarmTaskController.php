<?php

namespace App\Api;

use App\Models\FarmHandbook;
use App\Models\FarmUserTask;
use Illuminate\Support\Facades\Auth;
use App\Services\FarmTaskService;
use App\Services\FarmUserService;
use App\Services\FarmWarehouseService;
use App\Services\WalletAssetService;
use App\Support\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Description of My
 *
 * @author Administrator
 */
class FarmTaskController extends Controller
{

    // 获取任务列表（永远保持任务数量，不足自动补齐）
    public function getList()
    {
        $user_id = Auth::id();
        return Response::success(FarmTaskService::replenishTasks($user_id));
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

        $detail = FarmUserTask::with('farmTask', 'farmTask.rewardAsset')
            ->where('user_id', $user->id)->where('id', $request->id)->where('status', 0)->first();
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
            if ($warehouseNum->where('handbook_id', $value['handbook_id'])->value('num') < $value['quantity']) {
                return Response::error(
                    '用户仓库资源不足,需要' . $value['quantity'] . '个' . FarmHandbook::where('id', $value['handbook_id'])->value('name'),
                    456346
                );
            }
        }

        try {

            DB::beginTransaction();
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


            DB::commit();
            // 补齐新任务并返回完整列表
            return Response::success(FarmTaskService::replenishTasks($user->id));
        } catch (\Throwable $th) {

            DB::rollBack();
            Log::error('异常：' . request()->route()->uri(), ['getMessage' => $th->getMessage(), 'getLine' => $th->getLine(), 'file' => $th->getFile()]);
            return Response::error();
            //throw $th;
        }
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

        // 补齐新任务并返回完整列表
        return Response::success(FarmTaskService::replenishTasks($user_id));
    }
}
