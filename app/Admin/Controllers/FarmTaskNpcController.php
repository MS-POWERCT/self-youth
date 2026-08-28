<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\FarmTaskNpc;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class FarmTaskNpcController extends AdminController
{
    protected function title()
    {
        return '任务 NPC';
    }

    protected function grid()
    {
        return Grid::make(new FarmTaskNpc(), function (Grid $grid) {
            $grid->model()->orderByDesc('id');

            $grid->column('id')->sortable();
            $grid->column('name', '名称');
            $grid->column('updated_at', '更新时间')->datetimeSplit()->sortable();
            $grid->column('created_at', '创建时间')->datetimeSplit();
            $grid->paginate(20);

            $grid->quickSearch(['id', 'name']);

            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->equal('id', 'ID')->width(3);
                $filter->like('name', '名称')->width(3);
            });
        });
    }

    protected function detail($id)
    {
        return Show::make($id, new FarmTaskNpc(), function (Show $show) {
            $show->field('id');
            $show->field('name', '名称');
            $show->field('created_at', '创建时间');
            $show->field('updated_at', '更新时间');
        });
    }

    protected function form()
    {
        return Form::make(new FarmTaskNpc(), function (Form $form) {
            $form->display('id');
            $form->text('name', '名称')->required()->rules('required|string|max:100');
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
        });
    }
}
