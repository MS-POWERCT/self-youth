<?php

namespace App\Api;

use App\Models\Asset;
use App\Models\FarmWarehouse;
use App\Services\FarmWarehouseService;
use App\Services\WalletAssetService;
use App\Support\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Description of My
 *
 * @author Administrator
 */
class FarmWarehouseController extends Controller
{


    // 仓库列表
    public function getList(Request $request)
    {
        $type = $request->type ?? 'seed';

        $list = FarmWarehouse::with(['handbook' => function ($query) {
            $query->orderBy('level_id', 'ASC');
        }])->where('user_id', Auth::id())
            ->where('num', '>', 0)
            ->where('type', $type)
            ->orderBy('id', 'DESC')->get();

        return Response::success($list);
    }


    // 卖出
    // public function sell(Request $request)
    // {

    //     $validator = Validator::make($request->all(), [
    //         'id' => 'required|integer',
    //         'type' => 'required|in:seed,fruit',
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json(array('res_code' => 1212, 'res_msg' => trans('app-return.validator_fails'), 'data' => []));
    //     }

    //     $id = $request->id;
    //     $num = min(intval($request->num ?? 1), FarmWarehouseService::$FARM_DEES_MAX);
    //     $type = $request->type;
    //     $user = Auth::user();

    //     $warehouse = FarmWarehouse::with(['handbook'])
    //         ->where('id', $id)
    //         ->where('user_id', $user->id)
    //         ->where('num', '>', 0)
    //         ->where('type', $type)
    //         ->first();
    //     if (!$warehouse) {
    //         return response()->json(array('res_code' => 1212, 'res_msg' => '未找到该物品', 'data' => []));
    //     }

    //     // 剩余数量是否足够
    //     if ($warehouse->num < $num) {
    //         return response()->json(array('res_code' => 1212, 'res_msg' => '数量不足', 'data' => []));
    //     }

    //     try {
    //         DB::beginTransaction();

    //         if ($type == 'seed') {
    //             $total_amount = intval($warehouse->handbook->price / 2 * $num);
    //             $asset = Asset::find($warehouse->handbook->asset_id);
    //         } else {
    //             $total_amount = $warehouse->handbook->selling_price * $num;
    //             $asset = Asset::find($warehouse->handbook->selling_asset_id);
    //         }
    //         $wallet_account = WalletAssetService::getWalletAsset($user, $asset);

    //         // 扣除余额
    //         WalletAssetService::change($wallet_account, $total_amount, [
    //             'module_code' => 'SELL',
    //         ]);

    //         // 对应的仓库数量增加
    //         $warehouse->num -= $num;
    //         $warehouse->save();

    //         DB::commit();
    //         return response()->json(array('res_code' => 0, 'res_msg' => trans('app-return.success'), 'data' => WalletAssetService::getAccountAssetAll($user)));
    //     } catch (\Throwable $th) {

    //         DB::rollBack();
    //         Log::error('异常：' . request()->route()->uri(), [
    //             'getMessage' => $th->getMessage(),
    //             'getLine' => $th->getLine(),
    //             'getFile' => $th->getFile()
    //         ]);
    //         return response()->json(array('res_code' => 9999, 'res_msg' => trans('app-return.error_msg'), 'data' => []));
    //     }
    // }

    /**
     * 全部卖出
     * 针对果实
     *
     * @param Request $request
     * @return void
     */
    // public function sellAll()
    // {

    //     $user = Auth::user();

    //     $list = FarmWarehouse::with(['handbook'])
    //         ->where('user_id', $user->id)
    //         ->where('num', '>', 0)
    //         ->where('type', 'fruit')
    //         ->get();

    //     if (count($list) == 0) {
    //         return response()->json(array('res_code' => 1212, 'res_msg' => '未发现卖出产品', 'data' => []));
    //     }

    //     try {
    //         DB::beginTransaction();

    //         foreach ($list as $key => $warehouse) {

    //             $total_amount = $warehouse->handbook->selling_price * $warehouse->num;
    //             $asset = Asset::find($warehouse->handbook->selling_asset_id);
    //             $wallet_account = WalletAssetService::getWalletAsset($user, $asset);

    //             // 扣除余额
    //             WalletAssetService::change($wallet_account, $total_amount, [
    //                 'module_code' => 'SELL',
    //             ]);


    //             DB::table('farm_warehouses')->where('id', $warehouse->id)->update([
    //                 'num' => 0
    //             ]);
    //         }

    //         DB::commit();
    //         return response()->json(array('res_code' => 0, 'res_msg' => trans('app-return.success'), 'data' => WalletAssetService::getAccountAssetAll($user)));
    //     } catch (\Throwable $th) {

    //         DB::rollBack();
    //         Log::error('异常：' . request()->route()->uri(), [
    //             'getMessage' => $th->getMessage(),
    //             'getLine' => $th->getLine(),
    //             'getFile' => $th->getFile()
    //         ]);
    //         return response()->json(array('res_code' => 9999, 'res_msg' => trans('app-return.error_msg'), 'data' => []));
    //     }
    // }
}
