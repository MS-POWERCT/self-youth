<?php

namespace App\Api;

use App\Models\HabitCheckLog;
use App\Models\UserHabit;
use App\Services\HabitService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Support\Response;

/**
 * Description of My
 *
 * @author Administrator
 */
class HabitCheckController extends Controller
{

    // 今日打卡/取消打卡 POST /api/habit/check/toggle
    public function toggle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return Response::error(trans('app-return.validator_fails'), 1212);
        }
        $id = $request->id;
        $user = Auth::user();
        $date = date('Y-m-d');

        // 检查这个习惯是否存在
        $habit = UserHabit::where('user_id', $user->id)->where('type', HabitService::HABITCHECK)
            ->where('is_show', 1)->where('id', $id)->first();
        if (!$habit) {
            return Response::error(trans('app-return.error_msg'));
        }

        try {
            DB::beginTransaction();
            $check = HabitCheckLog::where('user_id', $user->id)
                ->where('check_date', $date)->where('habit_id', $id)->first();
            if (!$check) {
                $check = HabitCheckLog::create([
                    'user_id' => $user->id,
                    'habit_id' => $id,
                    'check_time' => date('Y-m-d H:i:s'),
                    'check_date' => $date,
                    'status' => 1,
                ]);
            } else {
                $check->status = $check->status == 1 ? 0 : 1;
                $check->check_date = $date;
                $check->save();
            }

            // 热力贡献值
            HabitService::setHabitState($user->id, (object)[
                'status' => $check->status,
                'record_date' => $check->check_date,
            ]);


            // 连续打卡天数
            HabitService::setContinuousDays($user->id, HabitService::HABITCHECK);

            DB::commit();
            return Response::success();
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::error($e->getMessage());
        }
    }

    // 今日打卡记录 GET /api/habit/check/today
    public function today()
    {
        $user_id = Auth::id();
        $habit = UserHabit::where('user_id', $user_id)->where('type', HabitService::HABITCHECK)
            ->where('is_show', 1)->get();
        if (!$habit) {
            return Response::success([]);
        }



        $check = HabitCheckLog::where('user_id', $user_id)
            ->wherein('habit_id', $habit->pluck('id'))
            ->where('check_date', date('Y-m-d'))->where('status', 1)->get();

        return Response::success($check);
    }

    // 获取某月日历打卡数据 GET /api/habit/check/calendar

    // 获取打卡统计（周/月） GET /api/habit/check/stat

}
