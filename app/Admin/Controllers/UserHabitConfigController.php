<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\UserHabitConfig;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class UserHabitConfigController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new UserHabitConfig(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('type');
            $grid->column('sort');
            $grid->column('icon');
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
        return Show::make($id, new UserHabitConfig(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('type');
            $show->field('sort');
            $show->field('icon');
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
        return Form::make(new UserHabitConfig(), function (Form $form) {
            $form->display('id');
            $form->text('name');
            $form->text('type');
            $form->text('sort');
            $form->text('icon');
            $form->text('status');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
