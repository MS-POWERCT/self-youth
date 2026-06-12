<?php

namespace App\Admin\Controllers;

use App\Admin\Metrics\Tools\GlobalTool;
use App\Admin\Repositories\MarkModule;
use App\Models\MarkCategory;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class MarkModuleController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new MarkModule(['category']), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('category.name', '大分类');
            $grid->column('name')->editable();
            $grid->column('title')->editable();
            // 图片
            // $grid->column('img_url')->image('', 60);
            $grid->column('sort')->editable();
            $grid->column('status')->switch();
            $grid->column('created_at')->datetimeSplit();

            // 多选
            $grid->selector(function (Grid\Tools\Selector $selector) {
                $selector->select('category_id', MarkCategory::pluck('name', 'id')->toArray());
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new MarkModule(), function (Form $form) {
            $form->display('id');
            $form->select('category_id')->options(MarkCategory::pluck('name', 'id')->toArray());
            $form->text('name');
            $form->text('title');
            // 图片
            $form->image('img_url')->move(GlobalTool::getImageMove())
                ->maxSize(GlobalTool::getImageMaxsize())
                ->accept(GlobalTool::getImageAccept())
                ->autoUpload()
                ->required();
            $form->number('sort');
            $form->switch('status');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
