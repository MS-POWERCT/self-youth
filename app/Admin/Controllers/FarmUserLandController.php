<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\FarmUserLand;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class FarmUserLandController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new FarmUserLand(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('user_id');
            $grid->column('level_id');
            $grid->column('handbook_id');
            $grid->column('total_output');
            $grid->column('residue_output');
            $grid->column('plant_mature_at');
            $grid->column('plant_start_at');
            $grid->column('quarter');
            $grid->column('status');
            $grid->column('is_unlocked');
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
        return Show::make($id, new FarmUserLand(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('level_id');
            $show->field('handbook_id');
            $show->field('total_output');
            $show->field('residue_output');
            $show->field('plant_mature_at');
            $show->field('plant_start_at');
            $show->field('quarter');
            $show->field('status');
            $show->field('is_unlocked');
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
        return Form::make(new FarmUserLand(), function (Form $form) {
            $form->display('id');
            $form->text('user_id');
            $form->text('level_id');
            $form->text('handbook_id');
            $form->text('total_output');
            $form->text('residue_output');
            $form->text('plant_mature_at');
            $form->text('plant_start_at');
            $form->text('quarter');
            $form->text('status');
            $form->text('is_unlocked');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
