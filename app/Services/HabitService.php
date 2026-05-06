<?php

namespace App\Services;

use App\Models\HabitStat;
use App\Models\UserHabit;
use Illuminate\Support\Facades\DB;

class HabitService
{

    // 数据库配置了
    // // 1. 习惯打卡模块（打卡型）
    // // 适用场景：只区分「完成 / 未完成」的日常作息
    // // 例：吃早餐、早刷牙、晚刷牙，读书打卡、冥想打卡、洗脸护肤、泡脚洗脚等
    // // 特点：无数量、无时长，只有每日状态
    // const HABIT_TRACKER = [
    //     5 => '吃早餐',
    //     4 => '早刷牙洗脸',
    //     3 => '晚刷牙洗脸',
    //     2 => '泡脚洗脚',
    //     1 => '洗澡'
    // ];

    // // 2. 时长计数记录模块（数值型）
    // // 适用场景：需要记录「时长 / 次数」的自律行为
    // // 例：看书学习、锻炼身体、冥想时间
    // // 特点：支持多次记录、累加、单日总量、历史回溯
    // const HABIT_COUNTER = [
    //     3 => '看书学习',
    //     2 => '锻炼身体',
    //     1 => '冥想时间'
    // ];

    // 默认给新用户添加习惯方法
    public static function getDefaultHabit($user)
    {
        $data = [];
        $configs = DB::table('user_habit_configs')->where('status', 1)->get();
        foreach ($configs as $key => $value) {
            $data[] = [
                'user_id' => $user->id,
                'name' => $value->name,
                'type' => $value->type,
                'icon' => $value->icon,
                'sort' => $value->sort,
            ];
        }
        UserHabit::insert($data);
    }



    // 热力贡献值
    public static function setHabitState($user_id, $data)
    {
        $value = $data->status;
        $record_date = $data->record_date;
        // state统计
        // HabitStat::果然今天存在就total+1不让就创建
        $stat = HabitStat::where('user_id', $user_id)
            ->where('date', $record_date)->first();
        if (!$stat) {
            $stat = HabitStat::create([
                'user_id' => $user_id,
                'date' => $record_date,
                'total' => 1,
            ]);
        } else {
            $stat->total = $stat->total + ($value == 1 ? 1 : -1);
            $stat->save();
        }
    }
}
