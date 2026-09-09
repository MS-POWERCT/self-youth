<?php

namespace App\Admin\Controllers;

use App\Admin\Forms\FarmAssetForm;
use App\Admin\Metrics\Tools\GlobalTool;
use App\Admin\Repositories\FarmAsset;
use App\Models\Asset;
use App\Models\UserIdentity;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Widgets\Modal;
use Illuminate\Support\Facades\View;

class FarmAssetController extends AdminController
{
    public function index(Content $content)
    {
        return $content
            ->header('农场资产')
            ->description('列表')
            ->body(function (Row $row) {
                $u = GlobalTool::getUser();
                if ($u->admin_role->id == 1) {
                    $row->column(2, Modal::make()
                        ->lg()
                        ->body(FarmAssetForm::make())
                        ->button('<button style="margin-bottom:10px" class="btn btn-white btn-outline"><i class="feather icon-edit"></i> 后台操作</button>'));
                }
            })
            ->body($this->grid());
    }

    protected function grid()
    {
        return Grid::make(new FarmAsset(['user.identities', 'asset']), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('userInfo', '用户信息')->display(function () {
                return View::make('admin.user-list-info', ['user' => data_get($this, 'user')])->render();
            })->width('24%');
            $grid->column('asset.name', '资产');
            $grid->column('balance', '余额')->sortable();
            $grid->column('created_at')->datetimeSplit()->sortable();

            $grid->disableActions();
            $grid->paginate(30);
            $grid->disableCreateButton();
            $grid->disableDeleteButton();
            $grid->tableCollapse(false);

            $grid->selector(function (Grid\Tools\Selector $selector) {
                $selector->select('asset_id', Asset::pluck('name', 'id')->toArray());
            });
            $grid->export();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('user_id')->width('12%');
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
                }, '用户地址')->width('12%');
            });
        });
    }
}
