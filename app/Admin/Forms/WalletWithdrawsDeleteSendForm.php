<?php

namespace App\Admin\Forms;

use App\Admin\Metrics\Tools\GlobalTool;
use App\Models\WalletWithdraw;
use App\Services3rd\BscscanService;
use App\Services\AppLogService;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use Dcat\Admin\Contracts\LazyRenderable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class WalletWithdrawsDeleteSendForm extends Form implements LazyRenderable
{
    use LazyWidget; // 使用异步加载功能

    protected $model;

    // 处理请求
    // public function handle(array $input)
    // {

    //     $google_auth = $input['google_auth'] ?? null;


    //     $u = GlobalTool::getUser();
    //     if ($u->admin_role->id != 1) {
    //         return $this->response()->error('您没有权限执行此操作');
    //     }
    //     // 判断输入的google验证码是否正确
    //     if (!GlobalTool::verifyGoogleCode($google_auth, $u)) {
    //         return $this->response()->error('google验证码错误');
    //     }

    //     AppLogService::addLog(WalletWithdraw::class, $this->model->id, "后台id{$u->id}:用户名称：{$u->username}进行操作,删除唯一标识");

    //     $data = BscscanService::deleteSendTransaction($this->model->id);
    //     Log::alert('WalletWithdrawsDeleteSendForm', ['data' => $data]);
    //     if ($data['res_code'] == 200) {

    //         Redis::hdel('withdraw_uni', $this->model->id);

    //         AppLogService::addLog(WalletWithdraw::class, $this->model->id, "后台id{$u->id}:用户名称：{$u->username}进行操作,将订单状态更新,请重新审核该订单");

    //         $this->model->tx_id = null;
    //         $this->model->status = 'CREATED';
    //         $this->model->sent_confirm_time = 0;
    //         $this->model->sent_at = null;
    //         $this->model->save();

    //         return $this->response()->success('操作成功')->refresh();
    //     }
    //     return $this->response()->error('操作失败')->refresh();
    // }

    // public function form()
    // {
    //     $id = $this->payload['id'] ?? null;
    //     if (!$id) {
    //         return $this->response()->error('参数错误');
    //     }
    //     $this->model = WalletWithdraw::with('asset')->find($id);

    //     $this->text('google_auth', 'google验证码')->required();
    // }


    // public function default()
    // {

    //     return [];
    // }
}
