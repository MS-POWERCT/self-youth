<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\MarkUser;
use App\Models\MarkCategory;
use App\Models\MarkItem;
use App\Models\MarkModule;
use App\Models\MarkUser as ModelsMarkUser;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;

class MarkUserController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new MarkUser(['module', 'item']), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('user_id');
            $grid->column('module.name', '大分类');
            $grid->column('item.title', '小分类');
            $grid->column('mark_type')->using(trans('app-status.mark_user.mark_type'))->label(ModelsMarkUser::MARK_TYPE_COLOR);
            $grid->column('remark')->editable();
            $grid->column('created_at')->datetimeSplit();


            $grid->disableActions();
            $grid->disableCreateButton();
            $grid->disableBatchDelete();
            $grid->paginate(30); // 默认分页10行

            // 多选
            $grid->selector(function (Grid\Tools\Selector $selector) {
                $selector->select('mark_type', trans('app-status.mark_user.mark_type'));
                $selector->select('module_id', MarkModule::pluck('name', 'id')->toArray());
            });

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('user_id')->width('20%');
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    // protected function form()
    // {
    //     return Form::make(new MarkUser(), function (Form $form) {
    //         $form->display('id');
    //         $form->text('user_id');
    //         // $form->text('module_id');
    //         // $form->text('item_id');
    //         $form->text('mark_type');
    //         $form->text('remark');
    //         $form->text('mark_time');

    //         $form->display('created_at');
    //         $form->display('updated_at');
    //     });
    // }
}
