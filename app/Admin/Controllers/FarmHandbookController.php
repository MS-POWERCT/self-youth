<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\FarmHandbook;
use App\Models\Asset;
use App\Models\FarmHandbook as FarmHandbookModel;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class FarmHandbookController extends AdminController
{
    protected function title()
    {
        return '农场图鉴';
    }

    protected function grid()
    {
        return Grid::make(new FarmHandbook(['asset', 'sellingAsset']), function (Grid $grid) {

            $grid->column('id')->sortable();
            $grid->column('icon', '图标')->display(function ($icon) {
                $url = FarmHandbookModel::resolveIconUrl($icon);
                if (!$url) {
                    return '-';
                }

                return "<img src=\"{$url}\" style=\"width:40px;height:40px;object-fit:contain\" alt=\"icon\"/>";
            });
            $grid->column('name', '名称')->limit(16);
            $grid->column('desc', '描述')->limit(20);
            $grid->column('level_id', '所需等级')->badge('primary');
            $grid->column('quarter', '季度');
            $grid->column('quarter_output_num', '季度产出');
            $grid->column('quarter_exp', '季度经验');

            $grid->column('price_info', '买入价格')->display(function () {
                $price = data_get($this, 'price');
                $assetName = data_get($this, 'asset.name', data_get($this, 'asset_id'));

                return "<div><strong>{$price}</strong></div><div class='text-muted' style='font-size:12px'>{$assetName}</div>";
            })->width('9%');

            $grid->column('mature_info', '成熟时间')->display(function () {
                $first = data_get($this, 'mature_time');
                $again = data_get($this, 'mature_after_time');

                return "<div>首次：{$first}s</div><div class='text-muted' style='font-size:12px'>再次：{$again}s</div>";
            })->width('9%');

            $grid->column('selling_info', '出售价格')->display(function () {
                $price = data_get($this, 'selling_price');
                $assetName = data_get($this, 'sellingAsset.name', data_get($this, 'selling_asset_id'));

                return "<div><strong>{$price}</strong></div><div class='text-muted' style='font-size:12px'>{$assetName}</div>";
            })->width('9%');

            $grid->column('updated_at', '更新时间')->datetimeSplit()->sortable();
            $grid->column('price')->hide();
            $grid->column('asset_id')->hide();
            $grid->column('mature_time')->hide();
            $grid->column('mature_after_time')->hide();
            $grid->column('selling_price')->hide();
            $grid->column('selling_asset_id')->hide();
            $grid->column('created_at')->hide();
            $grid->showColumnSelector();
            $grid->paginate(20);

            $grid->quickSearch(['id', 'name', 'desc']);

            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->expand();
                $filter->equal('id', 'ID')->width(3);
                $filter->like('name', '名称')->width(3);
                $filter->equal('level_id', '所需等级')->width(3);
                $filter->equal('asset_id', '买入资产')->select(Asset::pluck('name', 'id'))->width(3);
                $filter->equal('selling_asset_id', '出售资产')->select(Asset::pluck('name', 'id'))->width(3);
            });
        });
    }

    protected function detail($id)
    {
        return Show::make($id, new FarmHandbook(['asset', 'sellingAsset']), function (Show $show) {
            $show->field('id');
            $show->field('name', '名称');
            $show->field('icon', '图标')->unescape()->as(function ($icon) {
                $url = FarmHandbookModel::resolveIconUrl($icon);
                if (!$url) {
                    return '-';
                }

                return "<img src=\"{$url}\" style=\"width:80px;height:80px;object-fit:contain\" alt=\"icon\"/>";
            });
            $show->field('desc', '描述');
            $show->field('level_id', '所需等级');
            $show->field('quarter', '季度');
            $show->field('quarter_output_num', '季度产出');
            $show->field('quarter_exp', '季度经验');
            $show->field('price', '买入价格');
            $show->field('asset.name', '买入资产');
            $show->field('mature_time', '首次成熟(秒)');
            $show->field('mature_after_time', '再次成熟(秒)');
            $show->field('selling_price', '出售价格');
            $show->field('sellingAsset.name', '出售资产');
            $show->field('created_at', '创建时间');
            $show->field('updated_at', '更新时间');
        });
    }

    protected function form()
    {
        return Form::make(new FarmHandbook(), function (Form $form) {
            $form->display('id');
            $form->text('name', '名称')->required();
            $form->text('icon', '图标')->help('文件名如 wheat.svg，或完整路径 ' . FarmHandbookModel::ICON_PATH . 'wheat.svg');
            $form->textarea('desc', '描述')->rows(3);
            $form->number('level_id', '所需等级')->min(1)->default(1)->required();
            $form->number('quarter', '季度')->min(1)->default(1)->required();
            $form->number('quarter_output_num', '季度产出')->min(1)->required();
            $form->number('quarter_exp', '季度经验')->min(0)->required();
            $form->decimal('price', '买入价格')->required();
            $form->select('asset_id', '买入资产')->options(Asset::pluck('name', 'id'))->required();
            $form->number('mature_time', '首次成熟(秒)')->min(1)->required();
            $form->number('mature_after_time', '再次成熟(秒)')->min(1)->required();
            $form->decimal('selling_price', '出售价格')->required();
            $form->select('selling_asset_id', '出售资产')->options(Asset::pluck('name', 'id'))->required();
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
        });
    }
}
