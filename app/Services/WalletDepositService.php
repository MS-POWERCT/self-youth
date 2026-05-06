<?php

namespace App\Services;

use App\Models\WalletDeposit;

class WalletDepositService
{


    // public static function createConttractDeposit($data, $asset)
    // {

    //     $logs = $data['logs'];
    //     if (count($logs) < 0) {
    //         return 0;
    //     }

    //     $count = 0;
    //     // 查看合约
    //     foreach ($logs as $item) {

    //         if (strtolower($asset->unique_code) !== strtolower($item['address'])) {
    //             continue;
    //         }

    //         if (count($item['topics']) <= 2) {
    //             continue;
    //         }

    //         $topics1 = $item['topics'][1];
    //         $topics1 = '0x' . strtolower(substr($topics1, 26));

    //         $topics2 = $item['topics'][2];
    //         $topics2 = '0x' . strtolower(substr($topics2, 26));

    //         $block_number = hexdec($item['blockNumber']);

    //         // 如果topics1是0x0000000000000000000000000000000000000000 就跳过
    //         if ($topics1 == '0x0000000000000000000000000000000000000000') {
    //             continue;
    //         }

    //         // 检查hash是否已充值
    //         if (WalletDeposit::where('tx_id', $item['transactionHash'])->first()) {
    //             continue;
    //         }

    //         $hexNumber = $item['data'];
    //         $decimalNumber = hexdec('0x' . ltrim($hexNumber, '0x'));
    //         $amount = $decimalNumber / (1000000000000000000);

    //         $status = $amount >= $asset->deposit_min ? 'CREATED' : 'LACKED';

    //         WalletDeposit::create([
    //             'asset_id' => $asset->id,
    //             'chain_name' => $asset->chain_name,
    //             'block_number' => $block_number,
    //             'amount' => $amount,
    //             'tx_id' => $item['transactionHash'],
    //             'amount' => $amount,
    //             'nonce' => hexdec($item['logIndex']),
    //             'from' => $topics1,
    //             'to' => $topics2,
    //             'status' => $status
    //         ]);
    //         $count++;
    //     }
    //     return $count;
    // }
}
