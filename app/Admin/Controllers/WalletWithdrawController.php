<?php

namespace App\Admin\Controllers;

use App\Admin\Metrics\Handle\WalletWithdrawAudited;
use App\Admin\Metrics\Handle\WalletWithdrawCancelAudited;
use App\Admin\Metrics\Handle\WalletWithdrawCanceled;
use App\Admin\Metrics\Handle\WalletWithdrawDeleteSendAction;
use App\Admin\Metrics\Tools\GlobalTool;
use App\Admin\Renderable\LogList;
use App\Admin\Repositories\WalletWithdraw;
use App\Models\AppLog;
use App\Models\WalletWithdraw as ModelsWalletWithdraw;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Illuminate\Support\Facades\View;

class WalletWithdrawController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new WalletWithdraw(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('userInfo', '用户信息')->display(function () {
                return View::make('admin.user-info-card', ['user' => data_get($this, 'user')])->render();
            })->width('24%');
            $grid->column('asset_name', '提现资产')->display(function () {
                return data_get($this, 'asset.chain_name') . '-' . data_get($this, 'asset.name');
            });
            $grid->column('address')->limit(10);

            $grid->column('amount_with_fee', '金额明细')->display(function () {
                $amount = trim_decimal(data_get($this, 'amount'));
                $fee = trim_decimal(data_get($this, 'fee'));
                $reality = $amount - $fee;

                return <<<HTML
                    <div style="align-items: center;">
                        <div style="background: #1890ff; color: white; margin:6px 0px; padding: 3px 6px; border-radius: 3px; font-size: 14px;">总额：{$amount}</div>
                        <div style="background: #faad14; color: white; margin:6px 0px; padding: 3px 6px; border-radius: 3px; font-size: 14px;">手续费：{$fee}</div>
                        <div style="background: #52c41a; color: white; margin:6px 0px; padding: 3px 6px; border-radius: 3px; font-size: 14px;">到账：{$reality}</div>
                    </div>
                    HTML;
            })->width('10%');

            $grid->column('tx_id')->display(function ($value) {
                if ($value) {
                    return GlobalTool::getBrowserUrl($value, data_get($this, 'asset'), 'tx');
                }
                return '无';
            })->width('10%');

            $grid->column('logs', '操作日志')->display(function () {
                return 'Logs: ' . AppLog::where('morph_model', ModelsWalletWithdraw::class)->where('morph_id', data_get($this, 'id'))->count();
            })->expand(function () {
                return LogList::make(['morph_model' => ModelsWalletWithdraw::class]);
            });
            $grid->column('status')->using(trans('app-status.wallet_withdraw.status'))->label(ModelsWalletWithdraw::$status_color);

            $grid->column('时间信息')->display(function () {
                $sent_confirm_time = data_get($this, 'sent_confirm_time');
                $sent_at = data_get($this, 'sent_at');
                $success_confirm_time = data_get($this, 'success_confirm_time');
                $success_at = data_get($this, 'success_at');
                $created_at = data_get($this, 'created_at');

                return <<<HTML
                        <div>
                            <div>确定发送：{$sent_confirm_time}</div>
                            <div>发送成功：{$sent_at}</div>
                            <div>确定成功：{$success_confirm_time}</div>
                            <div>成功时间：{$success_at}</div>
                            <div>创建时间：{$created_at}</div>
                        </div>
                        HTML;
            })->width('16%');


            $grid->column('user_address');
            $grid->column('user_id');
            $grid->column('created_at');
            $grid->column('amount');
            $grid->column('fee');
            $hiddens = ['user_address', 'user_id', 'created_at', 'amount', 'fee'];
            $grid->hideColumns($hiddens);
            $grid->showColumnSelector();
            $grid->export()->rows(function ($rows) {
                foreach ($rows as $index => &$row) {
                    // 判断用户是否存在
                    if (!$row['user']) {
                        $row['user_address'] = '用户不存在';
                        $row['user_id'] = '用户不存在';
                    } else {
                        $row['user_address'] = $row['user']['address'];
                        $row['user_id'] = $row['user']['id'];
                    }
                    $row['created_at'] = $row['created_at'];
                    $row['amount'] = $row['amount'];
                    $row['fee'] = $row['fee'];
                    $row['asset_name'] = $row['asset']['chain_name'] . '-' . $row['asset']['name'];
                }
                return $rows;
            });

            $grid->tableCollapse(false);
            $grid->disableCreateButton();
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableEdit(); // 禁止编辑

                $u = GlobalTool::getUser();
                if ($u->admin_role->id == 1) {
                    $status = $actions->row->status;
                    if ($status == 'CREATED') {
                        $actions->append(new WalletWithdrawAudited());
                        $actions->append('<br/>');
                        $actions->append('<br/>');
                        $actions->append(new WalletWithdrawCanceled());
                    } else if ($status == 'AUDITED') {
                        $actions->append(new WalletWithdrawCancelAudited());
                        $actions->append('<br/>');
                        $actions->append('<br/>');
                        $actions->append(new WalletWithdrawDeleteSendAction(data_get($this, 'id')));
                    }
                    $actions->append('<br/>');
                    $actions->append('<br/>');
                }
            });

            $grid->paginate(30);
            // 显示多选
            $grid->selector(function (Grid\Tools\Selector $selector) {
                $selector->select('status', trans('app-status.wallet_withdraw.status'));
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id')->width('15%');
                $filter->in('user_id')->width('15%');
                $filter->like('address')->width('15%');
                $filter->equal('tx_id')->width('15%');
                $filter->between('created_at')->datetime()->width('35%');
            });
        });
    }
}
