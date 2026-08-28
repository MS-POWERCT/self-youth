<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\FarmShop;
use App\Models\FarmHandbook;
use App\Models\FarmHandbook as FarmHandbookModel;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class FarmShopController extends AdminController
{
    protected function title()
    {
        return '农场商店';
    }

    protected function grid()
    {
        return Grid::make(new FarmShop(['handbook']), function (Grid $grid) {
            $grid->model()->orderByDesc('id');

            $grid->column('id')->sortable();
            $grid->column('handbook.icon', '图标')->display(function ($icon) {
                $url = FarmHandbookModel::resolveIconUrl($icon);
                if (!$url) {
                    return '-';
                }

                return "<img src=\"{$url}\" style=\"width:36px;height:36px;object-fit:contain\" alt=\"icon\"/>";
            });
            $grid->column('handbook.name', '商品名称');
            $grid->column('handbook_id', '图鉴ID');
            $grid->column('type', '类型')->using(trans('app-status.farm_shop_type'))->label([
                'seed'    => 'success',
                'product' => 'primary',
                'tool'    => 'warning',
            ]);
            $grid->column('day_limit', '每日限购');
            $grid->column('status', '上架')->switch();
            $grid->column('updated_at', '更新时间')->datetimeSplit()->sortable();
            $grid->column('created_at')->hide();
            $grid->showColumnSelector();
            $grid->paginate(20);

            $grid->quickSearch(['id', 'handbook_id']);

            $grid->selector(function (Grid\Tools\Selector $selector) {
                $selector->select('type', trans('app-status.farm_shop_type'));
                $selector->select('status', [1 => '上架', 0 => '下架']);
            });

            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->expand();
                $filter->equal('id', 'ID')->width(3);
                $filter->equal('handbook_id', '图鉴ID')->width(3);
                $filter->equal('handbook.name', '商品名称')->width(3);
                $filter->equal('type', '类型')->select(trans('app-status.farm_shop_type'))->width(3);
                $filter->equal('status', '状态')->select([1 => '上架', 0 => '下架'])->width(3);
            });
        });
    }

    protected function detail($id)
    {
        return Show::make($id, new FarmShop(['handbook']), function (Show $show) {
            $show->field('id');
            $show->field('handbook.name', '商品名称');
            $show->field('handbook.icon', '图标')->unescape()->as(function ($icon) {
                $url = FarmHandbookModel::resolveIconUrl($icon);
                if (!$url) {
                    return '-';
                }

                return "<img src=\"{$url}\" style=\"width:64px;height:64px;object-fit:contain\" alt=\"icon\"/>";
            });
            $show->field('handbook_id', '图鉴ID');
            $show->field('type', '类型')->using(trans('app-status.farm_shop_type'));
            $show->field('day_limit', '每日限购');
            $show->field('status', '上架')->using([1 => '上架', 0 => '下架']);
            $show->field('created_at', '创建时间');
            $show->field('updated_at', '更新时间');
        });
    }

    protected function form()
    {
        return Form::make(new FarmShop(), function (Form $form) {
            $form->display('id');
            $form->select('handbook_id', '图鉴商品')
                ->options(FarmHandbook::pluck('name', 'id'))
                ->required();
            $form->radio('type', '类型')
                ->options(trans('app-status.farm_shop_type'))
                ->default('seed')
                ->required();
            $form->number('day_limit', '每日限购')->min(0)->default(0)->help('0 表示不限购');
            $form->switch('status', '上架')->default(1);
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
        });
    }
}
