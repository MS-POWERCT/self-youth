<?php

namespace App\Admin\Metrics\Handle;

use App\Admin\Metrics\Tools\GlobalTool;
use Dcat\Admin\Grid\RowAction;
use App\Models\WalletWithdraw;
use App\Services\AppLogService;
use Exception;

class WalletWithdrawCancelAudited extends RowAction
{


    /**
     * 返回字段标题
     *
     * @return string
     */
    public function title()
    {
        return '取消审核';
    }

    public function handle()
    {
        try {
            $wallet = WalletWithdraw::find($this->getKey());
            if ($wallet->status != 'AUDITED') {
                return $this->response()->error('操作失败,状态错误')->refresh();
            }
            $u = GlobalTool::getUser();
            if ($u->admin_role->id != 1) {
                return $this->response()->error('无权限');
            }

            if (!empty($wallet)) {
                $status = 'CREATED';
                $wallet->status = $status;
                $wallet->save();

                AppLogService::addLog(WalletWithdraw::class, $wallet->id, "后台id：{$u->id}：名称{$u->username}进行操作{$status}");
            }

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
            "确定" . $this->title() . "吗",
        ];
    }
}
