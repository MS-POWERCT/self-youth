<?php

namespace App\Admin\Forms;

use App\Admin\Metrics\Tools\GlobalTool;
use App\Models\Asset;
use App\Models\User;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use Exception;
use Illuminate\Support\Facades\DB;
use Dcat\Admin\Contracts\LazyRenderable;
use App\Services\WalletAssetService;

class WalletAssetForm extends Form implements LazyRenderable
{
    use LazyWidget;

    protected static $max_user_id = 30;

    // public function handle(array $input)
    // {

    //     $user_id = $input['user_id'];
    //     $number = $input['number'];
    //     $asset_id = $input['asset_id'];
    //     $change_type = $input['change_type'];
    //     $note = $input['note'];
    //     $google_auth = $input['google_auth'];

    //     $u = GlobalTool::getUser();
    //     if ($u->admin_role->id != 1) {
    //         return $this->response()->error('无权限');
    //     }

    //     // 判断输入的google验证码是否正确
    //     if (!GlobalTool::verifyGoogleCode($google_auth, $u)) {
    //         return $this->response()->error('google验证码错误')->alert();
    //     }

    //     $ids = explode(',', $user_id);

    //     if (count($ids) > self::$max_user_id) {
    //         return $this->response()->error('一次最多操作' . self::$max_user_id . '个用户');
    //     }

    //     try {
    //         DB::beginTransaction();


    //         foreach ($ids as $key => $value) {
    //             $user = User::where('id', trim($value))->first();
    //             if (!$user) {
    //                 throw new Exception('未发现用户信息');
    //             }

    //             $asset = (object)[
    //                 'id' => $asset_id,
    //             ];
    //             $wallet_asset = WalletAssetService::getWalletAsset($user, $asset->id);
    //             if ($change_type == 'decrease') {
    //                 WalletAssetService::checkBalance($wallet_asset, $number);
    //                 $number *= -1;
    //             }

    //             WalletAssetService::change($wallet_asset, $number, 0, [
    //                 'module_code' => 'ADMIN',
    //                 'note' => $note
    //             ]);
    //         }

    //         DB::commit();
    //         return $this->response()->success('操作成功');
    //     } catch (Exception $e) {
    //         DB::rollBack();
    //         return $this->response()->error('操作失败' . $e->getMessage() . ' class:' . $e->getFile() . ' line: ' . $e->getLine());
    //     }
    // }


    // public function form()
    // {
    //     $this->text('user_id')->help('后台只能给用户基本户操作，支持多个最多' . self::$max_user_id . '个')->required();
    //     $this->number('number', '金额')->required();
    //     $assets = Asset::pluck('name', 'id')->toArray();
    //     $this->select('asset_id', '资产')->options($assets)->required();
    //     $this->text('note', '备注')->help('操作备注,可以为空');
    //     $this->select('change_type', '状态')->options(['increase' => '增加', 'decrease' => '减少'])->default('increase');
    //     // $this->password('password', '充值密码')->required();
    //     $this->text('google_auth', 'google验证码')->required();
    // }
}
