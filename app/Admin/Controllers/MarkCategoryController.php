<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\MarkCategory;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;

class MarkCategoryController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new MarkCategory(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name')->editable();
            $grid->column('sort')->editable();
            $grid->column('status')->switch();
            $grid->column('created_at')->datetimeSplit();
            $grid->disableActions();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new MarkCategory(), function (Form $form) {
            $form->display('id');
            $form->text('name');
            $form->text('icon')->help('Iconify 图标标识符，如: mdi:home, bi:bookmark');
            $form->number('sort');
            $form->switch('status');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
