<?php

namespace App\CronJobs;

use App\Models\Asset;
use App\Services3rd\BscscanService;
use App\Services\WalletDepositService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EventController
{

    /**
     * 得到wen的高度信息
     *
     * @param [type] $asset_id
     * @param [type] $size
     * @return void
     */
    // public static function handleNewblocks()
    // {

    //     // 刷新高度
    //     $assets = Asset::whereIn('id', [1])->get();

    //     // 刷新各资产最新区块高度
    //     foreach ($assets as $asset) {
    //         $asset->done_block_number = BscscanService::blockNumber();
    //         $asset->save();
    //     }
    //     return ' done:';
    // }


    // 对高度的信息进行分析-BSC
    // public function getTransactionByBlockBsc()
    // {
    //     $asset = Asset::find(1);
    //     // 检查是否有bsc的充值
    //     $list = BscscanService::getLogs(
    //         $asset->unique_code,
    //         $asset->deposit_address,
    //         $asset->done_block_number - $asset->last_block_size,
    //         $asset->done_block_number
    //     );

    //     if (!$list) {
    //         return 'no logs';
    //     }

    //     try {

    //         foreach ($list as $key => $value) {
    //             $data = BscscanService::getTransactionReceipt($value['transactionHash']);
    //             if ($data) {
    //                 WalletDepositService::createConttractDeposit($data, $asset);
    //             }
    //         }

    //         return 'success';
    //     } catch (\Throwable $th) {
    //         Log::error('异常：' . request()->route()->uri(), ['getMessage' => $th->getMessage(), 'getLine' => $th->getLine(), 'file' => $th->getFile()]);
    //         return 'error';
    //     }
    // }
}
