<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\FarmAssetChange;
use App\Models\Asset;
use App\Models\UserIdentity;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Illuminate\Support\Facades\View;

class FarmAssetChangeController extends AdminController
{
    protected function grid()
    {
        return Grid::make(new FarmAssetChange(['user.identities', 'asset']), function (Grid $grid) {
            $grid->column('id');
            $grid->column('userInfo', '用户信息')->display(function () {
                return View::make('admin.user-list-info', ['user' => data_get($this, 'user')])->render();
            })->width('24%');
            $grid->column('asset.name', '资产名称');
            $grid->column('balance_change', '余额变动')->setAttributes(['style' => 'color:red'])->sortable();
            $grid->column('module_code', '类型')->using(trans('app-status.farm_asset.module_code'));
            $grid->column('created_at')->datetimeSplit()->sortable();

            $grid->disableCreateButton();
            $grid->disableActions();
            $grid->column('user_id', '用户id');
            $grid->export()->rows(function ($rows) {
                foreach ($rows as &$row) {
                    $row['user_id'] = data_get($row, 'user.id');
                }

                return $rows;
            });
            $grid->hideColumns(['user_id']);
            $grid->showColumnSelector();
            $grid->selector(function (Grid\Tools\Selector $selector) {
                $selector->select('asset_id', Asset::pluck('name', 'id')->toArray());
                $selector->select('module_code', trans('app-status.farm_asset.module_code'));
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id', '流水号')->width('15%');
                $filter->equal('user.id', '用户id')->width('15%');
                $filter->where('user_web3', function ($query) {
                    $address = request('user_web3');
                    if ($address) {
                        $address = strtolower(trim($address));
                        $query->whereHas('user.identities', function ($identityQuery) use ($address) {
                            $identityQuery
                                ->where('provider', UserIdentity::PROVIDER_WEB3)
                                ->where('identifier', 'like', "%{$address}%");
                        });
                    }
                }, '用户地址')->width('20%');
                $filter->between('created_at')->datetime()->width('35%');
            });
        });
    }
}
