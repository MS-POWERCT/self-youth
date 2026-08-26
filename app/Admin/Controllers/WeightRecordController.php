<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\WeightRecord;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class WeightRecordController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new WeightRecord(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('user_id');
            $grid->column('weight');
            $grid->column('unit');
            $grid->column('recorded_at');
            $grid->column('note');
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
        return Show::make($id, new WeightRecord(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('weight');
            $show->field('unit');
            $show->field('recorded_at');
            $show->field('note');
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
        return Form::make(new WeightRecord(), function (Form $form) {
            $form->display('id');
            $form->text('user_id');
            $form->text('weight');
            $form->text('unit');
            $form->text('recorded_at');
            $form->text('note');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
