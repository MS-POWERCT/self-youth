<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\WalletAsset;
use App\Models\WalletAssetChange;
use Exception;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of My
 *
 * @author Administrato
 */
class WalletAssetService
{

    // const RADIX = 0.000001;

    // // 判断是否可以购买产品
    // static public function checkBalance($wallet_asset, $amount, $error = 1235)
    // {
    //     $wallet_asset = WalletAsset::lockForUpdate()->find($wallet_asset->id);
    //     if ($wallet_asset && $wallet_asset->balance + self::RADIX > $amount) {
    //     } else {
    //         $asset = Asset::select('id', 'name')->find($wallet_asset->asset_id);
    //         throw new Exception(trans('app-exception.balance_not_enough', ['unit' => $asset->name]), $error);
    //     }
    // }
    // static public function checkFreeze($wallet_asset, $amount)
    // {
    //     $wallet_asset = WalletAsset::lockForUpdate()->find($wallet_asset->id);
    //     if ($wallet_asset && $wallet_asset->freeze + self::RADIX > $amount) {
    //     } else {
    //         throw new Exception(trans('app-exception.freeze_not_enough'), 1235);
    //     }
    // }


    // // 默认账号有
    // static public function getAccountAssetAll($user)
    // {
    //     // $wallet_asset_arr = WalletAsset::with('asset')->where('user_id', $user->id)->get();
    //     // $wallet_asset_obj = [];
    //     // foreach ($wallet_asset_arr as $k => $v) {
    //     //     $wallet_asset_obj[$v->asset_id] = $k;
    //     // }

    //     // $assets = Asset::where('pre_create', 1)->get();
    //     // if (count($wallet_asset_arr) < count($assets)) {
    //     //     foreach ($assets as $key => $asset) {
    //     //         if (!isset($wallet_asset_obj[$asset->id])) {
    //     //             self::create($user, $asset->id);
    //     //         }
    //     //     }
    //     //     $wallet_asset_arr = WalletAsset::with('asset')->where('user_id', $user->id)->get();
    //     // }

    //     // return $wallet_asset_arr;
    //     // 1. 查询用户现有资产（带关联）
    //     $walletAssets = WalletAsset::with(['asset'])
    //         ->where('user_id', $user->id)
    //         ->get();

    //     // 2. 获取所有预创建资产ID
    //     $requiredAssetIds = Asset::where('pre_create', 1)->pluck('id')->toArray();

    //     // 3. 获取用户已有的资产ID
    //     $existingAssetIds = $walletAssets->pluck('asset_id')->toArray();

    //     // 4. 找出缺失的资产ID
    //     $missingAssetIds = array_diff($requiredAssetIds, $existingAssetIds);

    //     // 5. 如果有缺失，则创建并重新查询
    //     if (!empty($missingAssetIds)) {
    //         foreach ($missingAssetIds as $assetId) {
    //             self::create($user, $assetId);
    //         }
    //         // 重新查询完整集合（确保新创建的资产也带上关联）
    //         $walletAssets = WalletAsset::with(['asset'])
    //             ->where('user_id', $user->id)
    //             ->get();
    //     }

    //     return $walletAssets;
    // }


    // // 获得用户账户资产id,如果没有就创建该资产
    // static public function getWalletAsset($user, $asset_id)
    // {
    //     $wallet_asset = WalletAsset::where('user_id', $user->id)->where('asset_id', $asset_id)->first();

    //     if (!$wallet_asset) {
    //         $wallet_asset = self::create($user, $asset_id);
    //     }

    //     return $wallet_asset;
    // }



    // // 初始化基础账户
    // static public function fullCheck($user)
    // {
    //     $wallet_asset_arr = WalletAsset::where('user_id', $user->id)->get();
    //     $wallet_asset_obj = [];
    //     foreach ($wallet_asset_arr as $k => $v) {
    //         $wallet_asset_obj[$v->asset_id] = $k;
    //     }

    //     $assets = Asset::where('pre_create', 1)->get();

    //     if (count($wallet_asset_arr) < count($assets)) {
    //         foreach ($assets as $key => $asset) {
    //             if (!isset($wallet_asset_obj[$asset->id])) {
    //                 self::create($user, $asset->id);
    //             }
    //         }
    //         $wallet_asset_arr = WalletAsset::where('user_id', $user->id)->get();
    //     }

    //     return $wallet_asset_arr;
    // }


    // static public function change($account_asset, $balance_change = 0, $freeze_change = 0, array $params = [])
    // {

    //     $wallet_asset = WalletAsset::lockForUpdate()->find($account_asset->id);

    //     if ($balance_change != 0) {
    //         $wallet_asset->balance = $wallet_asset->balance + $balance_change;
    //     }
    //     if ($freeze_change != 0) {
    //         $wallet_asset->freeze = $wallet_asset->freeze + $freeze_change;
    //     }

    //     $wallet_asset->save();

    //     $preData = [
    //         'user_id' => $wallet_asset->user_id,
    //         'asset_id' => $wallet_asset->asset_id,
    //         'wallet_asset_id' => $wallet_asset->id,
    //         'balance_change' => $balance_change,
    //         'freeze_change' => $freeze_change,
    //     ];
    //     if (isset($params['module_code'])) {
    //         $preData['module_code'] = $params['module_code'];
    //     }
    //     if (isset($params['morph_id'])) {
    //         $preData['morph_id'] = $params['morph_id'];
    //     }
    //     if (isset($params['morph_model'])) {
    //         $preData['morph_model'] = $params['morph_model'];
    //     }
    //     if (isset($params['note'])) {
    //         $preData['note'] = $params['note'];
    //     }

    //     WalletAssetChange::create($preData);
    // }



    // static public function create($user, $asset_id)
    // {

    //     return WalletAsset::create([
    //         'user_id' => $user->id,
    //         'asset_id' => $asset_id,
    //         'balance' => 0,
    //         'freeze' => 0,
    //     ]);
    // }
}
