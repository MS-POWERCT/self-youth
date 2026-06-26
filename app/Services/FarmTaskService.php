<?php

namespace App\Services;

use App\Models\FarmTask;

class FarmTaskService
{

    /**
     * 获取用户的任务数量
     * 这里如果当天用户升级了如9 -> 10 是不会增加任务数量的是第二天生效
     */
    public static function getTaskNumber(int $level_id)
    {
        if ($level_id <= 5) {
            return 2;
        } else if ($level_id <= 10) {
            return 3;
        } else if ($level_id <= 20) {
            return 4;
        } else if ($level_id <= 30) {
            return 5;
        } else if ($level_id <= 40) {
            return 6;
        } else if ($level_id <= 50) {
            return 7;
        }
    }



    /**
     * 加权随机抽样（不重复）
     * @param FarmTask $tasks
     * @param int $count
     */
    public static function weightedRandomSelection($tasks, int $count)
    {
        $result = [];
        $taskList = $tasks->values(); // 重置索引



        for ($i = 0; $i < $count; $i++) {
            // 计算总权重
            $totalWeight = $taskList->sum('quality_ratio');

            // 生成随机数
            $random = mt_rand(1, $totalWeight);

            // 根据权重选择任务
            $selectedIndex = null;
            $currentWeight = 0;

            foreach ($taskList as $index => $task) {
                $currentWeight += $task->quality_ratio;
                if ($random <= $currentWeight) {
                    $selectedIndex = $index;
                    break;
                }
            }

            // 将选中的任务加入结果集
            $result[] = $taskList[$selectedIndex];

            // 从候选列表中移除已选中的任务（保证不重复）
            $taskList->forget($selectedIndex);
            $taskList = $taskList->values(); // 重置索引
        }

        return collect($result);
    }
}
