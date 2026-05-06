<?php

namespace App\CronJobs;

use App\Models\WalletAsset;
use App\Models\WalletWithdraw;
use App\Services3rd\BscscanService;
use App\Services\AppLogService;
use App\Services\ToolsService;
use App\Services\WalletAssetService;
use App\Support\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class WalletWithdrawCronController
{

    /**
     *
     */
    public function autoWalletWithdraw()
    {

        $wallet_withdraw = WalletWithdraw::with(['asset', 'user'])->whereIn('asset_id', [1])->where('status', 'AUDITED')
            ->whereNull('tx_id')->orderBy('sent_confirm_time')->first();

        if (!$wallet_withdraw) {
            return Response::error('not wallet_withdraw!', 5000);
        }

        if (
            $wallet_withdraw->user->status == 1 ||
            $wallet_withdraw->user->status == 2 ||
            $wallet_withdraw->user->status == 3 ||
            $wallet_withdraw->user->status == 4
        ) {
            $wallet_withdraw->status = 'FAILED';
            $wallet_withdraw->save();

            AppLogService::addLog(WalletWithdraw::class, $wallet_withdraw->id, '账户异常');
            return Response::error('account error!', 5001);
        }

        try {

            DB::beginTransaction();
            $data = BscscanService::sendTransaction(
                $wallet_withdraw->address,
                $wallet_withdraw->amount - $wallet_withdraw->fee,
                $wallet_withdraw->id,
                $wallet_withdraw->asset_id
            );

            if ($data['res_code'] == 200 && $data['data']) {

                $wallet_withdraw->sent_at = date('Y-m-d H:i:s');
                $wallet_withdraw->status = 'SENT';
                $wallet_withdraw->tx_id = $data['data'];

                Redis::hset('withdraw_uni', $wallet_withdraw->id, $wallet_withdraw->address);
                AppLogService::addLog(WalletWithdraw::class, $wallet_withdraw->id, 'SENT：' . $data['data']);
            } else {
                AppLogService::addLog(WalletWithdraw::class, $wallet_withdraw->id, $data['res_msg'] ?? '失败一次');
            }
            $wallet_withdraw->sent_confirm_time++;
            $wallet_withdraw->save();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('autoWalletWithdraw error' . $e->getMessage() . 'line: ' . $e->getLine());
        }

        return Response::success(trans('app-return.success'));
    }


    // 发送后的提现确定
    public function autoWalletWithdrawSuccess()
    {
        // 获取多条待确认的提现记录，限制每次处理的数量避免超时
        $wallet_withdraw_sends = WalletWithdraw::with(['user', 'asset'])->whereIn('asset_id', [1])
            ->where('status', 'SENT')->whereNotNull('tx_id')
            ->limit(10) // 每次最多处理10条记录
            ->get();

        if ($wallet_withdraw_sends->isEmpty()) {
            return Response::error('not wallet_withdraw_send!');
        }

        $processed_count = 0;
        $success_count = 0;
        $failed_count = 0;

        foreach ($wallet_withdraw_sends as $wallet_withdraw_send) {
            try {
                DB::beginTransaction();

                // 这里还有bug,如果是wen 主网的确定要走另外的方式
                $wallet_withdraw_send->success_confirm_time++;

                // if ($wallet_withdraw_send->asset_id == 1) {
                $result = BscscanService::getTransactionReceipt($wallet_withdraw_send->tx_id);
                // }

                if ($result && $result['status'] == '0x1') {

                    $wallet_withdraw_send->success_at = date('Y-m-d H:i:s');
                    $wallet_withdraw_send->status = 'SUCCEEDED';

                    $account_asset = WalletAsset::find($wallet_withdraw_send->wallet_asset_id);
                    WalletAssetService::change($account_asset, 0, -$wallet_withdraw_send->amount, [
                        'module_code' => 'WITHDRAW',
                        'morph_model' => WalletWithdraw::class,
                        'morph_id' => $wallet_withdraw_send->id,
                    ]);

                    AppLogService::addLog(WalletWithdraw::class, $wallet_withdraw_send->id, '矿工打包完成,确定次数：' . $wallet_withdraw_send->success_confirm_time);
                    $success_count++;
                }

                if ($wallet_withdraw_send->success_confirm_time > 30 && $wallet_withdraw_send->status == 'SENT') {
                    AppLogService::addLog(WalletWithdraw::class, $wallet_withdraw_send->id, '提交后确定' . $wallet_withdraw_send->success_confirm_time . '次null,矿工未打包,tx_id=' . $wallet_withdraw_send->tx_id);
                    $wallet_withdraw_send->tx_id = null;
                    $wallet_withdraw_send->status = 'CREATED';
                    $wallet_withdraw_send->success_confirm_time = 0;
                    $failed_count++;
                }

                $wallet_withdraw_send->save();
                $processed_count++;

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error('异常：' . request()->route()->uri(), [
                    'getMessage' => $e->getMessage(),
                    'getLine' => $e->getLine(),
                    'wallet_withdraw_id' => $wallet_withdraw_send->id
                ]);
                $failed_count++;
            }
        }

        return response()->json([
            'res_code' => 0,
            'res_msg' => trans('app-return.success'),
            'data' => [
                'processed_count' => $processed_count,
                'success_count' => $success_count,
                'failed_count' => $failed_count
            ]
        ]);
    }
}
