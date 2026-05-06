<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\WalletDeposit;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class WalletDepositController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new WalletDeposit(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('user_id');
            $grid->column('wallet_asset_id');
            $grid->column('asset_id');
            $grid->column('tx_confirm_time');
            $grid->column('block_number');
            $grid->column('amount');
            $grid->column('tx_id');
            $grid->column('nonce');
            $grid->column('from');
            $grid->column('to');
            $grid->column('note');
            $grid->column('chain_name');
            $grid->column('status');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
        
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new WalletDeposit(), function (Show $show) {
            $show->field('id');
            $show->field('user_id');
            $show->field('wallet_asset_id');
            $show->field('asset_id');
            $show->field('tx_confirm_time');
            $show->field('block_number');
            $show->field('amount');
            $show->field('tx_id');
            $show->field('nonce');
            $show->field('from');
            $show->field('to');
            $show->field('note');
            $show->field('chain_name');
            $show->field('status');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new WalletDeposit(), function (Form $form) {
            $form->display('id');
            $form->text('user_id');
            $form->text('wallet_asset_id');
            $form->text('asset_id');
            $form->text('tx_confirm_time');
            $form->text('block_number');
            $form->text('amount');
            $form->text('tx_id');
            $form->text('nonce');
            $form->text('from');
            $form->text('to');
            $form->text('note');
            $form->text('chain_name');
            $form->text('status');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
