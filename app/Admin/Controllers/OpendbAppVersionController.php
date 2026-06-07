<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\OpendbAppVersion;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class OpendbAppVersionController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new OpendbAppVersion(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('appid');
            $grid->column('name');
            $grid->column('title');
            $grid->column('contents');
            $grid->column('platform');
            $grid->column('type');
            $grid->column('uni_platform');
            $grid->column('version');
            $grid->column('min_uni_version');
            $grid->column('url');
            $grid->column('stable_publish');
            $grid->column('is_silently');
            $grid->column('is_mandatory');
            $grid->column('create_date');
            $grid->column('create_env');
        
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
        return Show::make($id, new OpendbAppVersion(), function (Show $show) {
            $show->field('id');
            $show->field('appid');
            $show->field('name');
            $show->field('title');
            $show->field('contents');
            $show->field('platform');
            $show->field('type');
            $show->field('uni_platform');
            $show->field('version');
            $show->field('min_uni_version');
            $show->field('url');
            $show->field('stable_publish');
            $show->field('is_silently');
            $show->field('is_mandatory');
            $show->field('create_date');
            $show->field('create_env');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new OpendbAppVersion(), function (Form $form) {
            $form->display('id');
            $form->text('appid');
            $form->text('name');
            $form->text('title');
            $form->text('contents');
            $form->text('platform');
            $form->text('type');
            $form->text('uni_platform');
            $form->text('version');
            $form->text('min_uni_version');
            $form->text('url');
            $form->text('stable_publish');
            $form->text('is_silently');
            $form->text('is_mandatory');
            $form->text('create_date');
            $form->text('create_env');
        });
    }
}
