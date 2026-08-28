<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\FarmUserTask;
use App\Models\FarmTask;
use App\Models\FarmUserTask as FarmUserTaskModel;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Illuminate\Support\Facades\View;

class FarmUserTaskController extends AdminController
{
    protected function title()
    {
        return '用户任务';
    }

    protected function grid()
    {
        return Grid::make(new FarmUserTask(['user', 'farmTask']), function (Grid $grid) {
            $grid->model()->orderByDesc('id');

            $grid->column('id')->sortable();
            $grid->column('user_info', '用户信息')->display(function () {
                $user = data_get($this, 'user');
                if (!$user) {
                    return "<span class='text-muted'>用户不存在 (ID: " . data_get($this, 'user_id') . ")</span>";
                }

                return View::make('admin.user-list-info', ['user' => $user])->render();
            })->width('22%');

            $grid->column('farmTask.name', '任务名称')->limit(20);
            $grid->column('farm_task_id', '任务ID');
            $grid->column('status', '状态')->using(trans('app-status.user_task.status'))->label(FarmUserTaskModel::$status_color);
            $grid->column('ok_at', '完成时间')->datetimeSplit();
            $grid->column('task_log', '任务记录')->limit(30);
            $grid->column('updated_at', '更新时间')->datetimeSplit()->sortable();
            $grid->column('user_id')->hide();
            $grid->column('created_at')->hide();
            $grid->showColumnSelector();
            $grid->paginate(20);

            $grid->quickSearch(function ($model, $query) {
                $model->where(function ($builder) use ($query) {
                    $builder->where('user_id', $query)
                        ->orWhere('id', $query)
                        ->orWhere('farm_task_id', $query)
                        ->orWhereHas('farmTask', function ($q) use ($query) {
                            $q->where('name', 'like', "%{$query}%");
                        });
                });
            })->placeholder('搜索 ID / 用户ID / 任务ID / 任务名');

            $grid->selector(function (Grid\Tools\Selector $selector) {
                $selector->select('status', trans('app-status.user_task.status'));
            });

            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->expand();
                $filter->equal('id', 'ID')->width(3);
                $filter->equal('user_id', '用户ID')->width(3);
                $filter->equal('farm_task_id', '任务ID')->width(3);
                $filter->equal('farmTask.name', '任务名称')->width(3);
                $filter->equal('status', '状态')->select(trans('app-status.user_task.status'))->width(3);
                $filter->between('ok_at', '完成时间')->datetime()->width(6);
            });

            $grid->disableCreateButton();
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableEdit();
            });
        });
    }

    protected function detail($id)
    {
        return Show::make($id, new FarmUserTask(['user', 'farmTask']), function (Show $show) {
            $show->field('id');
            $show->field('user_id', '用户ID');
            $show->field('user.name', '用户昵称');
            $show->field('farm_task_id', '任务ID');
            $show->field('farmTask.name', '任务名称');
            $show->field('status', '状态')->using(trans('app-status.user_task.status'));
            $show->field('ok_at', '完成时间');
            $show->field('task_log', '任务记录')->json();
            $show->field('created_at', '创建时间');
            $show->field('updated_at', '更新时间');
        });
    }

    protected function form()
    {
        return Form::make(new FarmUserTask(), function (Form $form) {
            $form->display('id');
            $form->display('user_id', '用户ID');
            $form->select('farm_task_id', '任务')
                ->options(FarmTask::pluck('name', 'id'))
                ->required();
            $form->select('status', '状态')
                ->options(trans('app-status.user_task.status'))
                ->required();
            $form->datetime('ok_at', '完成时间');
            $form->textarea('task_log', '任务记录')->rows(5);
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
        });
    }
}
