<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\FarmUserTask;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class FarmUserTaskController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new FarmUserTask(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('user_id');
            $grid->column('farm_task_id');
            $grid->column('task_log');
            $grid->column('ok_at');
            $grid->column('status');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
        
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new FarmUserTask(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('farm_task_id');
            $show->field('task_log');
            $show->field('ok_at');
            $show->field('status');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new FarmUserTask(), function (Form $form) {
            $form->display('id');
            $form->text('user_id');
            $form->text('farm_task_id');
            $form->text('task_log');
            $form->text('ok_at');
            $form->text('status');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
