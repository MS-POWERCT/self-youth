<?php

namespace App\Admin\Forms;

use App\Services\UserReferrerService;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use Dcat\Admin\Contracts\LazyRenderable;
use Exception;

class UserRefreshRelationForm extends Form implements LazyRenderable
{
    use LazyWidget;


    public function handle(array $input)
    {

        // 得到make传过来的参数
        $type = $input['type'];

        try {

            if ($type == 'referrer') {
                // UserReferrerService::setUserReferrer();
            } else if ($type == 'name') {
                UserReferrerService::setUserName();
            } else if ($type == 'avatar') {
                UserReferrerService::setUserAvatar();
            } else {
                return $this->response()->error('操作失败')->refresh();
            }

            return $this->response()->success('操作成功')->refresh();
        } catch (Exception $e) {
            return $this->response()->error('操作失败' . $e->getMessage() . 'line: ' . $e->getLine())->refresh();
        }
    }

    public function default()
    {
        return [];
    }

    public function form()
    {
        $this->select('type')->options(['name' => '昵称', 'avatar' => '头像'])->required();
    }
}
