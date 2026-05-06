<?php

namespace App\Admin\Metrics\Handle;

use App\Admin\Forms\WalletWithdrawsDeleteSendForm;
use Dcat\Admin\Widgets\Modal;
use Dcat\Admin\Grid\RowAction;

class WalletWithdrawDeleteSendAction extends RowAction
{
    protected $title = '删除唯一提现标识';
    public $id;

    public function __construct($id)
    {
        $this->id = $id;
    }
    public function render()
    {
        // 实例化表单类并传递自定义参数
        $form = WalletWithdrawsDeleteSendForm::make()->payload(['id' => $this->id]);

        return Modal::make()
            ->lg()
            ->title($this->title)
            ->body($form)
            ->button($this->title);
    }
}
