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
        return Grid::make(new FarmTask(['rewardAsset', 'npc']), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('npc.name', 'npc');
            $grid->column('name');
            $grid->column('task_need')->display(function ($value) {
                if (empty($value)) {
                    return '-';
                }
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
            $grid->column('reward_ratio')->display(function ($value) {
                return ($value * 100) . '%';
            })->badge();
            $grid->column('rewardAsset.name', '奖励资产');
            $grid->column('level_id');
            $grid->column('quality_type')->using(trans('app-status.task.quality_type'))->label(ModelsFarmTask::$quality_type_color);
            $grid->column('quality_ratio');
            $grid->column('status')->bool();

            $grid->paginate(50);
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
            $form->text('name')->required();
            $form->text('description');

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

            $form->hidden('reward_exp');
            $form->hidden('reward_gold');
            $form->hidden('reward_ratio');
            $form->radio('reward_asset_id')->options(Asset::pluck('name', 'id'))->required();
            $form->number('level_id')->required();
            $form->radio('quality_type')->options(trans('app-status.task.quality_type'))->required();
            $form->number('quality_ratio')->required();
            $form->switch('status');
            $form->display('created_at');
            $form->display('updated_at');

            $form->saving(function (Form $form) {
                if ($form->status) {
                    $taskNeed = $form->task_need;
                    $taskNeedCount = count($taskNeed);
                    $totalValue = 0;
                    foreach ($taskNeed as $item) {
                        if ($item['handbook_id']) {
                            $totalValue += FarmHandbook::find($item['handbook_id'])->selling_price * $item['quantity'];
                        }
                    }

                    $ranges = [
                        0 => ['min' => 0.8, 'max' => 1.0],
                        1 => ['min' => 1.4, 'max' => 1.8],
                        2 => ['min' => 2.4, 'max' => 2.8],
                    ];

                    $range = $ranges[$form->quality_type] ?? $ranges[0];
                    $ratio = $range['min'] + ($range['max'] - $range['min']) * min($taskNeedCount - 1, 2) / 2;

                    $rewardGold = (int)round($totalValue * (1 + $ratio));

                    $expRanges = [
                        0 => ['min' => 10, 'max' => 180],
                        1 => ['min' => 80, 'max' => 280],
                        2 => ['min' => 160, 'max' => 580],
                    ];

                    $expRange = $expRanges[$form->quality_type] ?? $expRanges[0];
                    $levelFactor = min($form->level_id / 50, 1);
                    $difficultyFactor = min($taskNeedCount, 3) / 3;
                    $rewardExp = (int)round($expRange['min'] + ($expRange['max'] - $expRange['min']) * ($levelFactor + $difficultyFactor) / 2);

                    $form->reward_ratio = $ratio;
                    $form->reward_gold = $rewardGold;
                    $form->reward_exp = $rewardExp;
                }
            });
        });
    }
}
