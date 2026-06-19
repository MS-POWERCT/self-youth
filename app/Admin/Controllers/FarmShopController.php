<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\FarmShop;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class FarmShopController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new FarmShop(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('handbook_id');
            $grid->column('type');
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
        return Show::make($id, new FarmShop(), function (Show $show) {
            $show->field('id');
            $show->field('handbook_id');
            $show->field('type');
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
        return Form::make(new FarmShop(), function (Form $form) {
            $form->display('id');
            $form->text('handbook_id');
            $form->text('type');
            $form->text('status');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
