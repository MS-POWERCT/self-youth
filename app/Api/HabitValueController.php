<?php

namespace App\Api;

use App\Models\HabitValueLog;
use App\Models\UserHabit;
use App\Services\HabitService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Support\Response;
use Illuminate\Support\Facades\Log;

/**
 * Description of My
 *
 * @author Administrator
 */
class HabitValueController extends Controller
{

    // 新增一条数值记录 POST /api/habit/value/add
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'habit_id' => 'required|integer',
            'value' => 'required|integer',
            'record_start_time' => 'required|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->first(), 1212);
        }

        $habit_id = $request->input('habit_id');
        $value = $request->input('value');
        $record_start_time = $request->input('record_start_time');
        $note = $request->input('note') ?? null;
        $note_image = $request->input('note_image') ?? null;
        $user_id = Auth::id();
        // 检查这个habit_id是否存在
        $habit = UserHabit::where('id', $habit_id)->where('type', HabitService::HABITVALUE)->where('user_id', $user_id)->first();
        if (!$habit) {
            return Response::error('习惯不存在', 1213);
        }


        try {

            $record_date =  date('Y-m-d', strtotime($record_start_time));
            HabitValueLog::create([
                'user_id' => $user_id,
                'habit_id' => $habit_id,
                'value' => $value,
                'record_start_time' => $record_start_time,
                'record_date' => $record_date,
                'note' => $note,
                'note_image' => $note_image,
            ]);

            // 热力贡献值
            HabitService::setHabitState($user_id, (object)[
                'status' => 1,
                'record_date' => $record_date,
            ]);

            // 连续打卡天数
            HabitService::setContinuousDays($user_id, HabitService::HABITVALUE);

            return Response::success();
        } catch (\Throwable $th) {
            Log::error('异常：' . request()->route()->uri(), ['getMessage' => $th->getMessage(), 'getLine' => $th->getLine(), 'file' => $th->getFile()]);
            return Response::error(trans('app-return.error_msg'));
        }
    }


    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'value' => 'required|integer',
            'record_start_time' => 'required|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return Response::error(trans('app-return.validator_fails'), 1212);
        }
        $id = $request->input('id');
        $value = $request->input('value');
        $record_start_time = $request->input('record_start_time');
        $note = $request->input('note') ?? null;
        $note_image = $request->input('note_image') ?? null;
        $user_id = Auth::id();
        // 检查这个记录是否存在
        $log = HabitValueLog::where('id', $id)->where('user_id', $user_id)->first();
        if (!$log) {
            return Response::error('记录不存在', 1214);
        }


        try {

            $log->update([
                'value' => $value,
                'record_start_time' => $record_start_time,
                'record_date' => date('Y-m-d', strtotime($record_start_time)),
                'note' => $note,
                'note_image' => $note_image,
            ]);

            return Response::success();
        } catch (\Throwable $th) {
            Log::error('异常：' . request()->route()->uri(), ['getMessage' => $th->getMessage(), 'getLine' => $th->getLine(), 'file' => $th->getFile()]);
            return Response::error(trans('app-return.error_msg'));
        }
    }



    // 获取数值记录列表 GET /api/habit/value/list
    // 需要做分页-按天来分页
    public function getList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'size' => 'integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
            return Response::error(trans('app-return.validator_fails'), 1212);
        }

        $last_date = $request->input('last_date');
        $size = $request->input('size', 10);
        $user_id = Auth::id();

        // 1. 分页获取本次需要加载的日期
        $dateQuery = HabitValueLog::query()
            ->where('user_id', $user_id)
            ->distinct()
            ->select('record_date');

        if (!empty($last_date)) {
            $dateQuery->where('record_date', '<', $last_date);
        }

        $dateList = $dateQuery
            ->orderBy('record_date', 'desc')
            ->limit($size)
            ->get()
            ->pluck('record_date');

        if ($dateList->isEmpty()) {
            return Response::success($dateList);
        }

        // 2. 查询这些日期下的所有记录
        $logs = HabitValueLog::query()->with('userHabit')
            ->where('user_id', $user_id)
            ->whereIn('record_date', $dateList)
            ->orderBy('record_date', 'desc')
            ->orderBy('record_start_time', 'desc')
            ->get();

        // 3. 按日期分组
        $groupData = [];
        foreach ($logs as $log) {
            $d = $log->record_date;
            if (!isset($groupData[$d])) {
                $groupData[$d] = [
                    'record_date' => $d,
                    'list' => []
                ];
            }
            $groupData[$d]['list'][] = $log;
        }

        return Response::success($groupData);
    }



    // 删除单条记录 POST /api/habit/value/del
    public function del(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return Response::error(trans('app-return.validator_fails'), 1212);
        }

        $id = $request->input('id');
        $user_id = Auth::id();
        // 检查这个记录是否存在
        $log = HabitValueLog::where('id', $id)->where('user_id', $user_id)->first();
        if (!$log) {
            return Response::error('记录不存在', 1214);
        }

        HabitValueLog::where('id', $id)->delete();

        // 热力贡献值
        HabitService::setHabitState($user_id, (object)[
            'status' => -1,
            'record_date' => $log->record_date,
        ]);

        return Response::success();
    }

    // 获取某日记录列表 GET /api/habit/value/day-list

    // 获取数值统计曲线 GET /api/habit/value/chart
}
