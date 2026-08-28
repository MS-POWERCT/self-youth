<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\FarmUserLand;
use App\Models\FarmHandbook;
use App\Models\FarmHandbook as FarmHandbookModel;
use App\Services\FarmUserLandService;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Illuminate\Support\Facades\View;

class FarmUserLandController extends AdminController
{
    protected function title()
    {
        return '用户土地';
    }

    protected function grid()
    {
        return Grid::make(new FarmUserLand(['user', 'handbook']), function (Grid $grid) {
            $grid->model()->orderByDesc('id');

            $grid->column('id')->sortable();
            $grid->column('user_info', '用户信息')->display(function () {
                $user = data_get($this, 'user');
                if (!$user) {
                    return "<span class='text-muted'>用户不存在 (ID: " . data_get($this, 'user_id') . ")</span>";
                }

                return View::make('admin.user-list-info', ['user' => $user])->render();
            })->width('22%');

            $grid->column('level_id', '土地等级')->display(function ($levelId) {
                $level = FarmUserLandService::$LEVEL[$levelId] ?? null;
                if (!$level) {
                    return $levelId;
                }

                return "<span class='badge badge-primary'>{$level['short_name']}</span> {$level['name']}";
            })->width('10%');

            $grid->column('crop_info', '种植作物')->display(function () {
                $name = data_get($this, 'handbook.name');
                if (!$name) {
                    return '<span class="text-muted">未种植</span>';
                }
                $icon = data_get($this, 'handbook.icon');
                $iconUrl = FarmHandbookModel::resolveIconUrl($icon);
                $iconHtml = $iconUrl ? "<img src=\"{$iconUrl}\" style=\"width:28px;height:28px;margin-right:6px;vertical-align:middle;object-fit:contain\" alt=\"icon\"/>" : '';

                return "{$iconHtml}{$name}";
            })->width('12%');

            $grid->column('output_info', '产出(剩/总)')->display(function () {
                return data_get($this, 'residue_output') . ' / ' . data_get($this, 'total_output');
            });

            $grid->column('quarter', '当前季度');
            $grid->column('status', '状态')->using(trans('app-status.farm_user_land.status'))->label([
                0 => 'success',
                1 => 'primary',
                2 => 'warning',
                3 => 'danger',
                9 => 'secondary',
            ]);

            $grid->column('plant_info', '种植时间')->display(function () {
                $start = data_get($this, 'plant_start_at');
                $mature = data_get($this, 'plant_mature_at');
                if (!$start) {
                    return '—';
                }

                return "<div>开始：{$start}</div><div class='text-muted' style='font-size:12px'>成熟：{$mature}</div>";
            })->width('14%');

            $grid->column('updated_at', '更新时间')->datetimeSplit()->sortable();
            $grid->column('user_id')->hide();
            $grid->column('handbook_id')->hide();
            $grid->column('total_output')->hide();
            $grid->column('residue_output')->hide();
            $grid->column('plant_mature_at')->hide();
            $grid->column('plant_start_at')->hide();
            $grid->column('created_at')->hide();
            $grid->showColumnSelector();
            $grid->paginate(20);

            $grid->quickSearch(function ($model, $query) {
                $model->where(function ($builder) use ($query) {
                    $builder->where('user_id', $query)
                        ->orWhere('id', $query)
                        ->orWhereHas('handbook', function ($q) use ($query) {
                            $q->where('name', 'like', "%{$query}%");
                        });
                });
            })->placeholder('搜索 ID / 用户ID / 作物名');

            $grid->selector(function (Grid\Tools\Selector $selector) {
                $selector->select('status', trans('app-status.farm_user_land.status'));
                $selector->select('level_id', trans('app-status.farm_user_land.level'));
            });

            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->expand();
                $filter->equal('id', 'ID')->width(3);
                $filter->equal('user_id', '用户ID')->width(3);
                $filter->equal('level_id', '土地等级')->select(trans('app-status.farm_user_land.level'))->width(3);
                $filter->equal('handbook_id', '图鉴ID')->width(3);
                $filter->equal('status', '状态')->select(trans('app-status.farm_user_land.status'))->width(3);
                $filter->equal('quarter', '季度')->width(3);
            });

            $grid->disableCreateButton();
        });
    }

    protected function detail($id)
    {
        return Show::make($id, new FarmUserLand(['user', 'handbook']), function (Show $show) {
            $show->field('id');
            $show->field('user_id', '用户ID');
            $show->field('user.name', '用户昵称');
            $show->field('level_id', '土地等级')->using(trans('app-status.farm_user_land.level'));
            $show->field('handbook.name', '种植作物');
            $show->field('handbook_id', '图鉴ID');
            $show->field('total_output', '总产出');
            $show->field('residue_output', '剩余产出');
            $show->field('quarter', '当前季度');
            $show->field('status', '状态')->using(trans('app-status.farm_user_land.status'));
            $show->field('plant_start_at', '种植时间');
            $show->field('plant_mature_at', '成熟时间');
            $show->field('created_at', '创建时间');
            $show->field('updated_at', '更新时间');
        });
    }

    protected function form()
    {
        return Form::make(new FarmUserLand(), function (Form $form) {
            $form->display('id');
            $form->display('user_id', '用户ID');
            $form->select('level_id', '土地等级')
                ->options(trans('app-status.farm_user_land.level'))
                ->required();
            $form->select('handbook_id', '种植图鉴')
                ->options(FarmHandbook::pluck('name', 'id'));
            $form->number('total_output', '总产出')->min(0);
            $form->number('residue_output', '剩余产出')->min(0);
            $form->number('quarter', '当前季度')->min(0);
            $form->select('status', '状态')
                ->options(trans('app-status.farm_user_land.status'))
                ->required();
            $form->datetime('plant_start_at', '种植时间');
            $form->datetime('plant_mature_at', '成熟时间');
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
        });
    }
}
