<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\WalletAsset;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Widgets\Modal;
use App\Admin\Forms\WalletAssetForm;
use App\Admin\Metrics\Tools\GlobalTool;
use App\Models\Asset;
use Illuminate\Support\Facades\View;

class WalletAssetController extends AdminController
{
    public function index(Content $content)
    {

        return $content->header('WalletAsset')
            ->description('列表')->body(function (Row $row) {
                $u = GlobalTool::getUser();
                if ($u->admin_role->id == 1) {
                    $row->column(2, Modal::make()
                        ->lg()
                        ->body(WalletAssetForm::make())
                        ->button('<button style="margin-bottom:10px" class="btn btn-white btn-outline"><i class="feather icon-edit"></i> 后台操作</button>'));
                }
            })->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new WalletAsset(['user', 'asset']), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('userInfo', '用户信息')->display(function () {
                return View::make('admin.user-info-card', ['user' => data_get($this, 'user')])->render();
            })->width('24%');
            $grid->column('asset.name');
            $grid->column('balance')->sortable();
            $grid->column('pledge')->sortable();
            $grid->column('freeze')->sortable();
            $grid->column('created_at')->datetimeSplit()->sortable();

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableEdit(); // 去掉
            });
            $grid->disableActions();
            $grid->paginate(30);
            $grid->disableCreateButton(); //禁用创建
            $grid->disableDeleteButton();
            $grid->tableCollapse(false);

            // 显示多选
            $grid->selector(function (Grid\Tools\Selector $selector) {
                $selector->select('asset_id', Asset::pluck('name', 'id')->toArray());
            });
            $grid->export();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('user_id')->width('12%');
                $filter->equal('user.address', '用户地址')->width('12%');
            });
        });
    }
}
