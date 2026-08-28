<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\FarmWarehouse;
use App\Models\FarmHandbook;
use App\Models\FarmHandbook as FarmHandbookModel;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Illuminate\Support\Facades\View;

class FarmWarehouseController extends AdminController
{
    protected function title()
    {
        return '用户仓库';
    }

    protected function grid()
    {
        return Grid::make(new FarmWarehouse(['user', 'handbook']), function (Grid $grid) {
            $grid->model()->orderByDesc('id');

            $grid->column('id')->sortable();
            $grid->column('user_info', '用户信息')->display(function () {
                $user = data_get($this, 'user');
                if (!$user) {
                    return "<span class='text-muted'>用户不存在 (ID: " . data_get($this, 'user_id') . ")</span>";
                }

                return View::make('admin.user-list-info', ['user' => $user])->render();
            })->width('22%');

            $grid->column('item_info', '物品')->display(function () {
                $name = data_get($this, 'handbook.name', '未知图鉴');
                $icon = data_get($this, 'handbook.icon');
                $handbookId = data_get($this, 'handbook_id');
                $iconUrl = FarmHandbookModel::resolveIconUrl($icon);
                $iconHtml = $iconUrl
                    ? "<img src=\"{$iconUrl}\" style=\"width:32px;height:32px;margin-right:8px;vertical-align:middle;object-fit:contain\" alt=\"icon\"/>"
                    : '';

                return "<div>{$iconHtml}<strong>{$name}</strong><div class='text-muted' style='font-size:12px'>图鉴ID: {$handbookId}</div></div>";
            })->width('18%');

            $grid->column('num', '数量')->badge('primary');
            $grid->column('type', '类型')->using(trans('app-status.farm_warehouse.type'))->label([
                'seed'    => 'success',
                'fruit'   => 'warning',
                'product' => 'primary',
                'tool'    => 'info',
            ]);
            $grid->column('updated_at', '更新时间')->datetimeSplit()->sortable();
            $grid->column('user_id', '用户ID')->hide();
            $grid->column('handbook_id', '图鉴ID')->hide();
            $grid->column('created_at')->hide();
            $grid->showColumnSelector();
            $grid->paginate(20);

            $grid->quickSearch(function ($model, $query) {
                $model->where(function ($builder) use ($query) {
                    $builder->where('user_id', $query)
                        ->orWhere('id', $query)
                        ->orWhere('handbook_id', $query)
                        ->orWhereHas('handbook', function ($q) use ($query) {
                            $q->where('name', 'like', "%{$query}%");
                        });
                });
            })->placeholder('搜索 ID / 用户ID / 图鉴ID / 商品名');

            $grid->selector(function (Grid\Tools\Selector $selector) {
                $selector->select('type', trans('app-status.farm_warehouse.type'));
            });

            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->expand();
                $filter->equal('id', 'ID')->width(3);
                $filter->equal('user_id', '用户ID')->width(3);
                $filter->equal('handbook_id', '图鉴ID')->width(3);
                $filter->equal('handbook.name', '商品名称')->width(3);
                $filter->equal('type', '类型')->select(trans('app-status.farm_warehouse.type'))->width(3);
                $filter->between('num', '数量范围')->width(3);
            });

            $grid->disableCreateButton();
        });
    }

    protected function detail($id)
    {
        return Show::make($id, new FarmWarehouse(['user', 'handbook']), function (Show $show) {
            $show->field('id');
            $show->field('user_id', '用户ID');
            $show->field('user.name', '用户昵称');
            $show->field('handbook.name', '商品名称');
            $show->field('handbook.icon', '图标')->unescape()->as(function ($icon) {
                $url = FarmHandbookModel::resolveIconUrl($icon);
                if (!$url) {
                    return '-';
                }

                return "<img src=\"{$url}\" style=\"width:64px;height:64px;object-fit:contain\" alt=\"icon\"/>";
            });
            $show->field('handbook_id', '图鉴ID');
            $show->field('num', '数量');
            $show->field('type', '类型')->using(trans('app-status.farm_warehouse.type'));
            $show->field('created_at', '创建时间');
            $show->field('updated_at', '更新时间');
        });
    }

    protected function form()
    {
        return Form::make(new FarmWarehouse(), function (Form $form) {
            $form->display('id');
            $form->display('user_id', '用户ID');
            $form->select('handbook_id', '图鉴商品')
                ->options(FarmHandbook::pluck('name', 'id'))
                ->required();
            $form->number('num', '数量')->min(0)->required();
            $form->radio('type', '类型')
                ->options(trans('app-status.farm_warehouse.type'))
                ->required();
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
        });
    }
}
