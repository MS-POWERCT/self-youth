<?php

namespace App\Admin\Controllers;

use App\Admin\Forms\UserRefreshRelationForm;
use App\Admin\Metrics\Handle\UserHabitAudited;
use App\Admin\Metrics\Tools\GlobalTool;
use App\Admin\Repositories\User;
use App\Models\User as ModelsUser;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Widgets\Modal;

class UserController extends AdminController
{
    // public function index(Content $content)
    // {
    //     return $content->description('列表')->body(function (Row $row) {})->body($this->grid());
    // }

    public function index(Content $content)
    {

        return $content
            ->title('用户列表')
            ->body(function (Row $row) {
                $row->column(12, function (Column $column) {
                    $column->row(function (Row $row) {
                        $row->column(2, Modal::make()
                            ->lg()
                            ->body(UserRefreshRelationForm::make())
                            ->button('<button style="margin-bottom:10px" class="btn btn-white btn-outline"><i class="feather icon-edit"></i> 刷新缓存 </button>'));
                    });
                    $column->row($this->grid());
                });
            });
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new User(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('email');
            $grid->column('address');
            // $grid->column('ip');
            // $grid->column('avatar');
            $grid->column('status')->using(trans('app-status.user.status'))->label(ModelsUser::$user_status_color);
            $grid->column('created_at')->datetimeSplit()->sortable();
            // $grid->column('updated_at')->sortable();


            $grid->disableCreateButton();
            // $grid->disableActions();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id')->width('20%');
                $filter->like('address')->width('30%');
                $filter->equal('status')->select(trans('app-status.user.status'))->width('20%');
            });


            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableEdit(); // 禁止编辑
                $u = GlobalTool::getUser();
                if ($u->admin_role->id == 1) {
                    $actions->append(new UserHabitAudited());
                }
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
    //     return Form::make(new User(), function (Form $form) {
    //         $form->display('id');
    //         $form->text('address');
    //         $form->text('referrer_address');
    //         $form->text('ip');
    //         $form->text('avatar');
    //         $form->text('user_level_id');
    //         $form->text('user_nft_id');
    //         $form->text('status');

    //         $form->display('created_at');
    //         $form->display('updated_at');
    //     });
    // }
}
