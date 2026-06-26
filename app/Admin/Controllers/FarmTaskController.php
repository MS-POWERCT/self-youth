<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\FarmTask;
use App\Models\Asset;
use App\Models\FarmHandbook;
use App\Models\FarmTask as ModelsFarmTask;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class FarmTaskController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new FarmTask(['rewardAsset']), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('npc_name');
            $grid->column('name');
            $grid->column('description');
            $grid->column('task_need')->display(function ($value) {
                if (empty($value)) {
                    return '-';
                }
                // 由于模型中设置了 JSON cast，$value 已经是数组
                $items = is_array($value) ? $value : [];
                $handbooks = FarmHandbook::whereIn('id', collect($items)->pluck('handbook_id'))->pluck('name', 'id')->toArray();
                $result = [];
                foreach ($items as $item) {
                    $name = $handbooks[$item['handbook_id']] ?? '未知商品';
                    $result[] = "{$name} × {$item['quantity']}";
                }
                return implode('<br>', $result);
            });
            $grid->column('reward_exp');
            $grid->column('reward_gold');
            $grid->column('rewardAsset.name', '奖励资产');
            $grid->column('level_id');
            $grid->column('quality_type')->using(trans('app-status.task.quality_type'))->label(ModelsFarmTask::$quality_type_color);
            $grid->column('quality_ratio');
            $grid->column('status')->switch();
            // $grid->column('created_at');
        });
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new FarmTask(), function (Form $form) {
            $form->display('id');
            $form->text('npc_name')->required();
            $form->text('name')->required();
            $form->text('description')->required();

            // 任务要求：选择商品并填写数量
            $form->table('task_need', function ($table) {
                $table->select('handbook_id', '商品')
                    ->options(function () {
                        return FarmHandbook::pluck('name', 'id')->toArray();
                    })
                    ->required();
                $table->number('quantity', '数量')
                    ->min(1)
                    ->required();
            });

            $form->number('reward_exp')->required();
            $form->number('reward_gold')->required();
            $form->radio('reward_asset_id')->options(Asset::pluck('name', 'id'))->required();
            $form->number('level_id')->required();
            $form->radio('quality_type')->options(trans('app-status.task.quality_type'))->required();
            $form->number('quality_ratio')->required();
            $form->switch('status');
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
