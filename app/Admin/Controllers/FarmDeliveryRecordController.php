<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\FarmDeliveryRecord;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class FarmDeliveryRecordController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new FarmDeliveryRecord(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('user_id');
            $grid->column('handbook_id');
            $grid->column('num');
            $grid->column('tool_id');
            $grid->column('start_at');
            $grid->column('end_at');
            $grid->column('asset_id');
            $grid->column('amount');
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
        return Show::make($id, new FarmDeliveryRecord(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('handbook_id');
            $show->field('num');
            $show->field('tool_id');
            $show->field('start_at');
            $show->field('end_at');
            $show->field('asset_id');
            $show->field('amount');
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
        return Form::make(new FarmDeliveryRecord(), function (Form $form) {
            $form->display('id');
            $form->text('user_id');
            $form->text('handbook_id');
            $form->text('num');
            $form->text('tool_id');
            $form->text('start_at');
            $form->text('end_at');
            $form->text('asset_id');
            $form->text('amount');
            $form->text('status');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
