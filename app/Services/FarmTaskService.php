<?php

namespace App\Services;

use App\Models\FarmTask;
use App\Models\FarmUserTask;
use Illuminate\Support\Facades\Redis;

class FarmTaskService
{

    /**
     * 获取用户的任务数量
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
        }
        return 7;
    }

    /**
     * 补齐用户任务到指定数量（幂等，带并发锁）
     * @param int $user_id
     * @return \Illuminate\Support\Collection 补齐后的任务列表
     */
    public static function replenishTasks(int $user_id)
    {
        $lockKey = "farm_replenish_lock:{$user_id}";
        $lockValue = uniqid('', true);

        // 原子获取锁（SET NX EX，10秒自动过期防止死锁）
        $locked = Redis::set($lockKey, $lockValue, 'EX', 10, 'NX');
        if (!$locked) {
            // 并发情况下直接返回当前任务列表
            return self::getUserTaskList($user_id);
        }

        try {
            $userTaskList = self::getUserTaskList($user_id);
            $farm_user_level = FarmUserService::getFarmUserLevel($user_id);
            $taskNumber = self::getTaskNumber($farm_user_level);

            $remainingTasks = $taskNumber - count($userTaskList);

            if ($remainingTasks <= 0) {
                return $userTaskList;
            }

            // 只取已上架的任务
            $availableTasks = FarmTask::where('status', 1)
                ->where('level_id', '<=', $farm_user_level)
                ->get();

            // 排除已领取的任务
            $excludeIds = $userTaskList->pluck('farm_task_id')->toArray();
            $taskPool = $availableTasks->reject(fn($task) => in_array($task->id, $excludeIds));

            // 如果可用任务池小于需要的数量，就把所有可用任务给用户；否则随机抽取
            $newTasks = $taskPool->count() < $remainingTasks
                ? $taskPool
                : self::weightedRandomSelection($taskPool, $remainingTasks);

            // 批量保存用户任务
            if ($newTasks->isNotEmpty()) {
                FarmUserTask::insert($newTasks->map(fn($task) => [
                    'user_id' => $user_id,
                    'farm_task_id' => $task->id,
                ])->toArray());
            }

            return self::getUserTaskList($user_id);
        } finally {
            // 只释放自己的锁
            if (Redis::get($lockKey) === $lockValue) {
                Redis::del($lockKey);
            }
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

    /**
     * 返回用户当前任务列表
     * @param int $user_id
     */
    public static function getUserTaskList($user_id)
    {
        return FarmUserTask::with('farmTask', 'farmTask.rewardAsset')
            ->select('farm_user_tasks.id', 'farm_user_tasks.user_id', 'farm_user_tasks.farm_task_id', 'farm_user_tasks.status')
            ->join('farm_tasks', 'farm_user_tasks.farm_task_id', '=', 'farm_tasks.id')
            ->where('farm_user_tasks.user_id', $user_id)
            ->where('farm_user_tasks.status', 0)
            ->orderBy('farm_tasks.quality_type', 'desc')
            ->get();
    }
}
