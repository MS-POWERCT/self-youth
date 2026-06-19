<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\FarmWarehouse;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class FarmWarehouseController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new FarmWarehouse(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('user_id');
            $grid->column('handbook_id');
            $grid->column('num');
            $grid->column('type');
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
        return Show::make($id, new FarmWarehouse(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('handbook_id');
            $show->field('num');
            $show->field('type');
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
        return Form::make(new FarmWarehouse(), function (Form $form) {
            $form->display('id');
            $form->text('user_id');
            $form->text('handbook_id');
            $form->text('num');
            $form->text('type');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
