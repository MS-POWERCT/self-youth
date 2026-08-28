<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\FarmTaskNpc;
use App\Models\FarmTaskNpc as FarmTaskNpcModel;
use Dcat\Admin\Grid;
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

            $grid->column('icon', '图标')->display(function ($icon) {
                $url = FarmTaskNpcModel::resolveIconUrl($icon);
                if (!$url) {
                    return '-';
                }

                return "<img src=\"{$url}\" style=\"width:40px;height:40px;object-fit:contain\" alt=\"icon\"/>";
            });
            $grid->column('created_at')->datetimeSplit()->sortable();
            $grid->paginate(20);

            $grid->quickSearch(['id', 'name']);

            $grid->disableActions();
            $grid->disableCreateButton();
        });
    }
}
