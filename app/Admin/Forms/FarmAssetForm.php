<?php

namespace App\Admin\Forms;

use App\Admin\Metrics\Tools\GlobalTool;
use App\Models\Asset;
use App\Models\User;
use App\Services\FarmAssetService;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use Exception;
use Illuminate\Support\Facades\DB;

class FarmAssetForm extends Form implements LazyRenderable
{
    use LazyWidget;

    protected static $max_user_id = 30;

    public function handle(array $input)
    {
        $user_id = $input['user_id'];
        $number = $input['number'];
        $asset_id = $input['asset_id'];
        $change_type = $input['change_type'];
        $google_auth = $input['google_auth'];

        $u = GlobalTool::getUser();
        if ($u->admin_role->id != 1) {
            return $this->response()->error('无权限');
        }

        if (!GlobalTool::verifyGoogleCode($google_auth, $u)) {
            return $this->response()->error('google验证码错误')->alert();
        }

        $ids = explode(',', $user_id);

        if (count($ids) > self::$max_user_id) {
            return $this->response()->error('一次最多操作' . self::$max_user_id . '个用户');
        }

        try {
            DB::beginTransaction();

            foreach ($ids as $value) {
                $user = User::where('id', trim($value))->first();
                if (!$user) {
                    throw new Exception('未发现用户信息');
                }

                $farmAsset = FarmAssetService::getFarmAsset($user, (int) $asset_id);
                if ($change_type == 'decrease') {
                    FarmAssetService::checkBalance($farmAsset, $number);
                    $number *= -1;
                }

                FarmAssetService::change($farmAsset, $number, [
                    'module_code' => 'ADMIN',
                ]);
            }

            DB::commit();

            return $this->response()->success('操作成功');
        } catch (Exception $e) {
            DB::rollBack();

            return $this->response()->error('操作失败' . $e->getMessage() . ' class:' . $e->getFile() . ' line: ' . $e->getLine());
        }
    }

    public function form()
    {
        $this->text('user_id')->help('支持多个用户，最多' . self::$max_user_id . '个')->required();
        $this->number('number', '金额')->required();
        $this->select('asset_id', '资产')->options(Asset::pluck('name', 'id')->toArray())->required();
        $this->select('change_type', '状态')->options(['increase' => '增加', 'decrease' => '减少'])->default('increase');
        $this->text('google_auth', 'google验证码')->required();
    }
}
