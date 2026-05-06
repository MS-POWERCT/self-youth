<?php

namespace App\Admin\Metrics\Handle;

use App\Admin\Metrics\Tools\GlobalTool;
use App\Models\User;
use Dcat\Admin\Grid\RowAction;
use App\Services\HabitService;
use Exception;

class UserHabitAudited extends RowAction
{

    /**
     * 返回字段标题
     *
     * @return string
     */
    public function title()
    {
        return '用户习惯刷新';
    }

    public function handle()
    {
        try {

            $u = GlobalTool::getUser();
            if ($u->admin_role->id != 1) {
                return $this->response()->error('无权限');
            }

            $user = User::find($this->getKey());
            HabitService::getDefaultHabit($user);

            // if (!empty($wallet)) {
            //     $status = 'AUDITED';
            //     $wallet->status = $status;
            //     $wallet->save();

            //     AppLogService::addLog(WalletWithdraw::class, $wallet->id, "后台id：{$u->id}：名称{$u->username}进行操作{$status}");
            // }

            return $this->response()->success('操作成功')->refresh();
        } catch (Exception $e) {
            return $this->response()->error('操作失败')->refresh();
        }
    }

    /**
     * 设置确认弹窗信息，如果返回空值，则不会弹出弹窗
     *
     * 允许返回字符串或数组类型
     *
     * @return array|string|void
     */
    public function confirm()
    {
        return [
            "确定刷新用户习惯数据吗",
        ];
    }
}
