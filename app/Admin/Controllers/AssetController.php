<?php

namespace App\Admin\Controllers;

use App\Admin\Metrics\Tools\GlobalTool;
use App\Admin\Repositories\Asset;
use App\Models\Asset as ModelsAsset;
use App\Services\ToolsService;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;

class AssetController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Asset(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('chain_name');
            $grid->column('chain_id');
            $grid->column('unique_code')->limit(10);
            $grid->column('icon')->image('', 60);
            // $grid->column('browser_url');
            $grid->column('rate')->editable();
            // $grid->column('pre_create')->switch();
            $grid->column('withdraw_enable')->switch();
            $grid->column('withdraw_min')->editable();
            $grid->column('withdraw_max')->editable();
            $grid->column('withdraw_fee_rate')->editable();
            $grid->column('withdraw_fee_min')->editable();
            $grid->column('withdraw_fee_max')->editable();
            $grid->column('withdraw_audit')->editable();
            // $grid->column('withdraw_audit_day')->editable();
            // $grid->column('withdraw_count')->editable();
            $grid->column('deposit_enable')->switch();
            $grid->column('deposit_min')->editable();
            // $grid->column('done_block_number');
            // $grid->column('last_block_size');
            // $grid->column('created_at');


            $grid->disableCreateButton();
        });
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Asset(), function (Form $form) {
            $form->display('id');
            $form->text('name');
            $form->text('chain_name');
            $form->text('unique_code');
            $form->image('icon')->move(GlobalTool::getImageMove())
                ->maxSize(GlobalTool::getImageMaxsize())
                ->accept(GlobalTool::getImageAccept())
                ->autoUpload()
                ->uniqueName()
                ->required();
            $form->text('browser_url');
            $form->text('rate');
            $form->switch('pre_create');
            $form->switch('withdraw_enable');
            $form->text('withdraw_min');
            $form->text('withdraw_max');
            $form->text('withdraw_fee_rate');
            $form->text('withdraw_fee_min');
            $form->text('withdraw_fee_max');
            $form->text('withdraw_audit');
            $form->text('withdraw_audit_day');
            $form->text('withdraw_count');
            $form->switch('deposit_enable');
            $form->text('deposit_min');
            $form->text('done_block_number');
            $form->text('last_block_size');
            $form->text('created_at');

            // 如果rate更新，需要更新缓存
            $form->saving(function (Form $form) {
                if ($form->isCreating()) {
                    return;
                }

                $asset = ModelsAsset::find($form->getKey()); // 原数据
                $form->id = $form->getKey(); // id默认好像是空，需要赋值一下
            });
        });
    }
}
