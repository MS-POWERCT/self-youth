<?php

namespace App\Admin\Controllers;

use App\Admin\Metrics\Tools\GlobalTool;
use App\Admin\Repositories\MarkItem;
use App\Models\MarkModule;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class MarkItemController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new MarkItem(['module']), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('module.name', '小分类');
            $grid->column('title')->editable();
            $grid->column('img_url')->image('', 60);
            $grid->column('star')->editable();
            $grid->column('sort')->editable();
            $grid->column('status')->switch();
            $grid->column('created_at')->datetimeSplit();
            // $grid->column('updated_at')->sortable();

            // 多选
            $grid->selector(function (Grid\Tools\Selector $selector) {
                $selector->select('module_id', MarkModule::pluck('name', 'id')->toArray());
            });
            // $grid->filter(function (Grid\Filter $filter) {
            //     $filter->equal('id');
            // });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new MarkItem(), function (Form $form) {
            $form->display('id');
            $form->select('module_id')->options(MarkModule::pluck('name', 'id')->toArray());
            $form->text('title');
            $form->image('img_url')->move(GlobalTool::getImageMove())
                ->maxSize(GlobalTool::getImageMaxsize())
                ->accept(GlobalTool::getImageAccept())
                ->autoUpload();
            // $form->text('img_url');
            $form->text('star');
            $form->text('sort');
            $form->switch('status');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
