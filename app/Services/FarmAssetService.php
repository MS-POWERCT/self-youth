<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\FarmAsset;
use App\Models\FarmAssetChange;
use App\Models\User;
use Exception;

class FarmAssetService
{
    public static function checkBalance(FarmAsset $farmAsset, int $amount, $error = 1235)
    {
        $farmAsset = FarmAsset::lockForUpdate()->find($farmAsset->id);

        if (!$farmAsset || $farmAsset->balance < $amount) {
            $asset = Asset::select('id', 'name')->find($farmAsset->asset_id);
            throw new Exception(trans('app-exception.balance_not_enough', ['unit' => $asset->name]), $error);
        }
    }

    public static function getFarmAssetsAll(User $user)
    {
        $farmAssets = FarmAsset::with(['asset'])
            ->where('user_id', $user->id)
            ->get();

        $requiredAssetIds = Asset::where('pre_create', 1)->pluck('id')->toArray();
        $existingAssetIds = $farmAssets->pluck('asset_id')->toArray();
        $missingAssetIds = array_diff($requiredAssetIds, $existingAssetIds);

        if (!empty($missingAssetIds)) {
            foreach ($missingAssetIds as $assetId) {
                self::create($user, $assetId);
            }

            $farmAssets = FarmAsset::with(['asset'])
                ->where('user_id', $user->id)
                ->get();
        }

        return $farmAssets;
    }

    public static function getFarmAsset(User $user, int $asset_id): FarmAsset
    {
        $farmAsset = FarmAsset::where('user_id', $user->id)
            ->where('asset_id', $asset_id)
            ->first();

        if (!$farmAsset) {
            $farmAsset = self::create($user, $asset_id);
        }

        return $farmAsset;
    }

    public static function change(FarmAsset $farmAsset, int $balance_change = 0, array $params = [])
    {
        $farmAsset = FarmAsset::lockForUpdate()->find($farmAsset->id);

        if ($balance_change != 0) {
            $farmAsset->balance = $farmAsset->balance + $balance_change;
        }
        $farmAsset->save();

        $preData = [
            'user_id' => $farmAsset->user_id,
            'asset_id' => $farmAsset->asset_id,
            'farm_asset_id' => $farmAsset->id,
            'balance_change' => $balance_change,
        ];

        if (isset($params['module_code'])) {
            $preData['module_code'] = $params['module_code'];
        }

        FarmAssetChange::create($preData);
    }

    public static function create(User $user, int $asset_id): FarmAsset
    {
        return FarmAsset::create([
            'user_id' => $user->id,
            'asset_id' => $asset_id,
            'balance' => 0,
        ]);
    }
}
