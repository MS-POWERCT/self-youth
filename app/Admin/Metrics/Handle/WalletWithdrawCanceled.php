<?php

namespace App\Admin\Metrics\Handle;

use App\Admin\Metrics\Tools\GlobalTool;
use Dcat\Admin\Grid\RowAction;
use Illuminate\Support\Facades\DB;
use App\Models\WalletWithdraw;
use App\Models\WalletAsset;
use App\Services\AppLogService;
use App\Services\WalletAssetService;
use Exception;


class WalletWithdrawCanceled extends RowAction
{


    /**
     * 返回字段标题
     *
     * @return string
     */
    public function title()
    {
        return '后台退回';
    }

    public function handle()
    {
        try {

            $wallet = WalletWithdraw::find($this->getKey());

            if ($wallet->status != 'CREATED') {
                return $this->response()->error('操作失败,状态错误')->refresh();
            }

            DB::beginTransaction();

            $u = GlobalTool::getUser();
            if ($u->admin_role->id != 1) {
                return $this->response()->error('无权限');
            }

            if (!empty($wallet)) {
                $status = 'CANCELED';
                $wallet->status = $status;
                $wallet->save();

                $account_asset = WalletAsset::find($wallet->wallet_asset_id);
                $change_params = [
                    'module_code' => 'WITHDRAW_CANCELED',
                    'morph_model' => WalletWithdraw::class,
                    'morph_id' => $wallet->id,
                ];
                WalletAssetService::change($account_asset, $wallet->amount, -$wallet->amount, $change_params);


                AppLogService::addLog(WalletAsset::class, $wallet->id, "后台id：{$u->id}：名称{$u->username}进行操作{$status}");
            }
            DB::commit();
            return $this->response()->success('操作成功')->refresh();
        } catch (Exception $e) {
            DB::rollBack();
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
            "将冻结资金退回至余额,确定退回吗",
        ];
    }
}
