<?php

namespace App\Admin\Controllers;

use App\Admin\Metrics\Tools\GlobalTool;
use App\Admin\Repositories\Advertise;
use App\Models\Advertise as ModelsAdvertise;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;

class AdvertiseController extends AdminController
{

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Advertise(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('sort')->editable();
            $grid->column('position')->select(ModelsAdvertise::$positions)->sortable();
            $grid->column('name')->editable(true);
            $grid->column('img_url')->image('', 60);
            $grid->column('status')->switch();

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->delete();
            });

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id')->width('15%');
                $filter->equal('position')->select(ModelsAdvertise::$positions)->width('15%');
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
        return Form::make(new Advertise(), function (Form $form) {
            $form->display('id');

            $form->select('position')->options(ModelsAdvertise::$positions)->required();
            $form->text('name')->required();
            $form->image('img_url')->move(GlobalTool::getImageMove())
                ->maxSize(GlobalTool::getImageMaxsize())
                ->accept(GlobalTool::getImageAccept())
                ->autoUpload()
                ->required();

            $form->number('sort');
            $form->switch('status')
                ->customFormat(function ($v) {
                    return $v == 1 ? 1 : 0;
                })
                ->saving(function ($v) {
                    return $v ? 1 : 0;
                })->help('如是新增广告,默认不启动 !!!');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
