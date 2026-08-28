<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Post\Restore;
use App\Admin\Repositories\WeightRecord;
use App\Models\WeightRecord as WeightRecordModel;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Illuminate\Support\Facades\View;

class WeightRecordController extends AdminController
{
    protected function title()
    {
        return '体重记录';
    }

    protected function grid()
    {
        return Grid::make(new WeightRecord(['user']), function (Grid $grid) {
            $grid->model()->orderByDesc('recorded_at')->orderByDesc('id');

            $grid->column('id')->sortable();
            $grid->column('user_info', '用户信息')->display(function () {
                $user = data_get($this, 'user');
                if (!$user) {
                    return "<span class='text-muted'>用户不存在 (ID: " . data_get($this, 'user_id') . ")</span>";
                }

                return View::make('admin.user-list-info', ['user' => $user])->render();
            })->width('22%');

            $grid->column('weight_info', '体重')->display(function () {
                $weight = number_format((float) data_get($this, 'weight'), 2, '.', '');
                $unitLabel = data_get($this, 'unit') === 'jin' ? '斤' : 'kg';
                $kg = number_format($this->weightInKg(), 2, '.', '');

                return <<<HTML
<div>
    <div style="font-size:16px;font-weight:600;color:#2c3e50;">{$weight} <span style="font-size:12px;color:#7f8c8d;">{$unitLabel}</span></div>
    <div style="font-size:12px;color:#95a5a6;margin-top:2px;">≈ {$kg} kg</div>
</div>
HTML;
            })->width('10%');

            $grid->column('change', '较上条变化')->display(function () {
                $change = $this->changeFromPrevious();
                if ($change === null) {
                    return '<span class="text-muted">—</span>';
                }
                if ($change > 0) {
                    return "<span style='color:#e74c3c;font-weight:600;'>↑ {$change} kg</span>";
                }
                if ($change < 0) {
                    $abs = abs($change);
                    return "<span style='color:#27ae60;font-weight:600;'>↓ {$abs} kg</span>";
                }

                return '<span class="text-muted">0</span>';
            })->width('9%');

            $grid->column('unit', '单位')->using([
                'kg'  => '千克(kg)',
                'jin' => '斤',
            ])->label([
                'kg'  => 'primary',
                'jin' => 'info',
            ]);

            $grid->column('recorded_at', '记录时间')->datetimeSplit()->sortable();
            $grid->column('note', '备注')->limit(20)->help('用户填写的备注');

            $grid->column('deleted_at', '状态')->display(function ($value) {
                return $value
                    ? '<span class="badge badge-secondary">已删除</span>'
                    : '<span class="badge badge-success">正常</span>';
            });

            $grid->column('created_at', '入库时间')->datetimeSplit()->sortable();
            $grid->column('user_id', '用户ID');
            $grid->column('weight', '原始体重');
            $grid->hideColumns(['user_id', 'weight']);
            $grid->showColumnSelector();

            $grid->paginate(20);
            $grid->quickSearch(function ($model, $query) {
                $model->where(function ($builder) use ($query) {
                    $builder->where('user_id', $query)
                        ->orWhere('id', $query)
                        ->orWhere('note', 'like', "%{$query}%");
                });
            })->placeholder('搜索 记录ID / 用户ID / 备注');

            $grid->selector(function (Grid\Tools\Selector $selector) {
                $selector->select('unit', [
                    'kg'  => '千克(kg)',
                    'jin' => '斤',
                ]);
            });

            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->expand();

                $filter->equal('id', '记录ID')->width(3);
                $filter->equal('user_id', '用户ID')->width(3);
                $filter->equal('unit', '单位')->select([
                    'kg'  => '千克(kg)',
                    'jin' => '斤',
                ])->width(3);
                $filter->between('weight', '体重范围')->width(3);
                $filter->like('note', '备注')->width(3);
                $filter->between('recorded_at', '记录时间')->datetime()->width(6);
                $filter->between('created_at', '入库时间')->datetime()->width(6);

                $filter->scope('trashed', '已删除')->onlyTrashed();
                $filter->scope('all', '含已删除')->withTrashed();
            });

            $grid->export()->rows(function ($rows) {
                foreach ($rows as &$row) {
                    $row['user_name'] = data_get($row, 'user.name', '');
                    $row['user_email'] = data_get($row, 'user.email', '');
                    $row['weight_kg'] = data_get($row, 'unit') === 'jin'
                        ? round(data_get($row, 'weight') / 2, 2)
                        : data_get($row, 'weight');
                }

                return $rows;
            });

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                if ($actions->row->trashed()) {
                    $actions->append(new Restore(WeightRecordModel::class));
                    $actions->disableEdit();
                    $actions->disableDelete();
                }
            });
        });
    }

    protected function detail($id)
    {
        return Show::make($id, new WeightRecord(['user']), function (Show $show) {
            $show->field('id');
            $show->field('user_id', '用户ID');
            $show->field('user.name', '用户昵称');
            $show->field('user.email', '用户邮箱');

            $show->field('weight', '体重')->as(function ($weight) {
                $unitLabel = data_get($this, 'unit') === 'jin' ? '斤' : 'kg';
                return number_format((float) $weight, 2) . ' ' . $unitLabel;
            });

            $show->field('weight_kg', '换算(kg)')->as(function () {
                return number_format($this->weightInKg(), 2) . ' kg';
            });

            $show->field('change', '较上条变化')->as(function () {
                $change = $this->changeFromPrevious();
                if ($change === null) {
                    return '—';
                }
                if ($change > 0) {
                    return "上升 {$change} kg";
                }
                if ($change < 0) {
                    return '下降 ' . abs($change) . ' kg';
                }

                return '无变化';
            });

            $show->field('unit', '单位')->using([
                'kg'  => '千克(kg)',
                'jin' => '斤',
            ]);

            $show->field('recorded_at', '记录时间');
            $show->field('note', '备注');
            $show->field('deleted_at', '删除时间');
            $show->field('created_at', '入库时间');
            $show->field('updated_at', '更新时间');
        });
    }

    protected function form()
    {
        return Form::make(new WeightRecord(), function (Form $form) {
            $form->display('id');
            $form->number('user_id', '用户ID')->required()->help('对应 users 表主键');

            $form->decimal('weight', '体重')
                ->required()
                ->rules('required|numeric|min:1|max:999.99')
                ->help('精确到小数点后两位，如 65.30');

            $form->radio('unit', '单位')
                ->options([
                    'kg'  => '千克(kg)',
                    'jin' => '斤',
                ])
                ->default('kg')
                ->required()
                ->rules('required|in:kg,jin');

            $form->datetime('recorded_at', '记录时间')
                ->required()
                ->default(now())
                ->help('用户实际称重时间，可与入库时间不同');

            $form->textarea('note', '备注')
                ->rows(3)
                ->rules('nullable|string|max:255');

            $form->display('deleted_at', '删除时间');
            $form->display('created_at', '入库时间');
            $form->display('updated_at', '更新时间');

            $form->saving(function (Form $form) {
                $form->weight = round((float) $form->weight, 2);
            });
        });
    }
}
