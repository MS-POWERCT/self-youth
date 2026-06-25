<?php

namespace App\Api;

use App\Models\Asset;
use App\Models\FarmShop;
use App\Services\FarmUserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Services\FarmWarehouseService;
use App\Services\WalletAssetService;
use App\Support\Response;
use Illuminate\Support\Facades\Log;

/**
 * Description of FarmProductController
 *
 * @author Administrator
 */
class FarmShopController extends Controller
{


    // 获取列表
    public function getList(Request $request)
    {
        $page = intval($request->page ?? 0);
        $size = min(intval($request->size ?? 50), 500);
        $type = $request->type ?? 'seed';

        $list = FarmShop::with('handbook')->select('id', 'handbook_id', 'type', 'status')->where('type', $type)
            ->where('status', 1)
            ->offset($page * $size)
            ->limit($size)
            ->get();

        // 查询所有相关的Asset
        $allAssets = Asset::select('id', 'name', 'icon')->get()->keyBy('id');
        // 分配查询结果回$list中的每个元素
        foreach ($list as &$value) {
            if ($value->handbook) {
                $value->handbook->asset = $allAssets[$value->handbook->asset_id] ?? '';
                $value->handbook->sellingAsset = $allAssets[$value->handbook->selling_asset_id] ?? '';
            }
        }

        return Response::success($list);
    }

    // 购买
    public function buy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->first());
        }

        // 查询这个产品是否
        $product = FarmShop::with('handbook')->where('id', $request->id)->where('status', 1)->first();
        if (!$product) {
            return Response::error(trans('app-return.not_found'));
        }

        $num = min(intval($request->num ?? 1), 999);
        $asset = Asset::find($product->handbook->asset_id);
        $user = Auth::user();


        // 查看是否会超过数量
        $warehouse = FarmWarehouseService::getUserWareHouse($user, $product->handbook_id, $product->type);
        if ($warehouse->num + $num > FarmWarehouseService::$FARM_DEES_MAX) {
            return Response::error(trans('app-return.farm_seed_max'));
        }

        // 有一个等级限制
        $farm_user_level = FarmUserService::getFarmUserLevel($user->id);
        if ($product->handbook->level_id > $farm_user_level) {
            return Response::error(trans('app-return.farm_level_mismatched'));
        }

        try {
            DB::beginTransaction();

            $total_amount = $product->handbook->price * $num;
            $wallet_account = WalletAssetService::getWalletAsset($user, $asset->id);
            WalletAssetService::checkBalance($wallet_account, $total_amount);

            // 扣除余额
            WalletAssetService::change($wallet_account, -$total_amount, [
                'module_code' => 'PAY',
            ]);

            // 对应的仓库数量增加
            $warehouse->num += $num;
            $warehouse->save();

            DB::commit();
            return Response::success();
        } catch (\Throwable $th) {

            DB::rollBack();
            Log::error('异常：' . request()->route()->uri(), [
                'getMessage' => $th->getMessage(),
                'getLine' => $th->getLine(),
                'getFile' => $th->getFile()
            ]);
            if ($th->getCode() == 1235) {
                return Response::error($th->getMessage());
            }
            return Response::error(trans('app-return.error_msg'));
        }
    }
}
