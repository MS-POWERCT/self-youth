<?php

namespace App\Admin\Controllers;

use App\Admin\Forms\I18nForm;
use App\Admin\Forms\LlconfigCacheForm;
use App\Admin\Repositories\Llconfig;
use App\Services\ToolsService;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Widgets\Modal;

class LlconfigController extends AdminController
{
    public function index(Content $content)
    {
        return $content
            ->body(function (Row $row) {
                $row->column(12, function (Column $column) {
                    $column->row(function (Row $row) {
                        $row->column(2, Modal::make()
                            ->lg()
                            ->body(I18nForm::make())
                            ->button('<button style="margin-bottom:10px" class="btn btn-white btn-outline"><i class="feather icon-edit"></i> 全部国际化 </button>'));
                        $row->column(2, Modal::make()
                            ->lg()
                            ->body(LlconfigCacheForm::make())
                            ->button('<button style="margin-bottom:10px" class="btn btn-white btn-outline"><i class="feather icon-edit"></i> 刷新全部缓存 </button>'));
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
        return Grid::make(new Llconfig(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('value')->display(function () {
                $value = data_get($this, 'value');
                return substr($value, 0, 40);
            })->width('20%');
            $grid->column('cache', '缓存内容')->display(function () {
                $name = data_get($this, 'name');
                return substr(ToolsService::getCache($name), 0, 40);
            })->width('20%');
            $grid->column('description');
            $grid->column('created_at')->datetimeSplit()->sortable();


            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableEdit();
                $actions->quickEdit();
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id')->width('20%');
                $filter->like('name')->width('20%');
                $filter->equal('value')->width('20%');
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
        return Form::make(new Llconfig(), function (Form $form) {
            $form->display('id');
            $form->hidden('name')->required();
            // $form->select('type')->options(['text' => '文本', 'textarea' => '长文本'])->required();
            if ($form->model()->type == 'number') {
                $form->number('value')->required();
            } elseif ($form->model()->type == 'select') {
                $form->select('value')->options([0 => '0', 1 => '1'])->required();
            } elseif ($form->model()->type == 'tags') {
                $form->tags('value')->saving(function ($value) {
                    return json_encode($value);
                })->required();
            } else {
                $form->textarea('value')->required();
            }


            $form->text('description');
            $form->saved(function (Form $form) {
                ToolsService::delCache($form->name);
            });


            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
