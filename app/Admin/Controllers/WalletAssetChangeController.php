<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\WalletAssetChange;
use App\Models\Asset;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Illuminate\Support\Facades\View;

class WalletAssetChangeController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

        return Grid::make(new WalletAssetChange(['user', 'asset']), function (Grid $grid) {

            $grid->column('id');
            $grid->column('userInfo', '用户信息')->display(function () {
                return View::make('admin.user-info-card', ['user' => data_get($this, 'user')])->render();
            })->width('24%');

            $grid->column('asset.name', '资产名称');
            $grid->column('balance_change')->setAttributes(['style' => 'color:red'])->sortable();
            $grid->column('freeze_change');
            $grid->column('module_code')->using(trans('app-status.wallet_asset.module_code'));
            $grid->column('created_at')->datetimeSplit()->sortable();

            $grid->disableCreateButton(); //禁用创建
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableEdit(); // 禁止

            });

            $grid->column('user_id', '用户id');
            $grid->column('user_address', '地址');
            $grid->export()->rows(function ($rows) {
                foreach ($rows as $index => &$row) {
                    $row['user_id'] = $row['user']['id'];
                    $row['user_address'] = $row['user']['user_address'];
                }
                return $rows;
            });
            $hiddens = ['user_id', 'user_address'];
            $grid->showColumnSelector();
            $grid->hideColumns($hiddens);
            $grid->disableActions();
            // 显示多选
            $grid->selector(function (Grid\Tools\Selector $selector) {
                $selector->select('asset_id', Asset::pluck('name', 'id')->toArray());
                $selector->select('module_code', trans('app-status.wallet_asset.module_code'));
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id', '流水号')->width('15%');
                $filter->equal('user.id', '用户id')->width('15%');
                $filter->equal('user.address', '用户地址')->width('20%');
                $filter->between('created_at')->datetime()->width('35%');
            });
        });
    }
}
