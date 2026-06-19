<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\FarmHandbook;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class FarmHandbookController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new FarmHandbook(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('icon');
            $grid->column('desc');
            $grid->column('level_id');
            $grid->column('quarter');
            $grid->column('quarter_output_num');
            $grid->column('quarter_exp');
            $grid->column('price');
            $grid->column('asset_id');
            $grid->column('mature_time');
            $grid->column('mature_after_time');
            $grid->column('selling_price');
            $grid->column('selling_asset_id');
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
        return Show::make($id, new FarmHandbook(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('icon');
            $show->field('desc');
            $show->field('level_id');
            $show->field('quarter');
            $show->field('quarter_output_num');
            $show->field('quarter_exp');
            $show->field('price');
            $show->field('asset_id');
            $show->field('mature_time');
            $show->field('mature_after_time');
            $show->field('selling_price');
            $show->field('selling_asset_id');
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
        return Form::make(new FarmHandbook(), function (Form $form) {
            $form->display('id');
            $form->text('name');
            $form->text('icon');
            $form->text('desc');
            $form->text('level_id');
            $form->text('quarter');
            $form->text('quarter_output_num');
            $form->text('quarter_exp');
            $form->text('price');
            $form->text('asset_id');
            $form->text('mature_time');
            $form->text('mature_after_time');
            $form->text('selling_price');
            $form->text('selling_asset_id');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
