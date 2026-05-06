<?php

namespace App\CronJobs;

use App\Support\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ZampleCronController
{

    /**
     * 对快照订单进行处理
     */
    public function output(Request $request)
    {

        // 小于00:00时
        $now = date('H:i');
        if ($now < '00:00') {
            return 'not time start';
        }

        if ($now >= '04:00') {
            return 'not time end';
        }

        try {
            DB::beginTransaction();


            DB::commit();
            return Response::success(trans('app-return.success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('异常：' . request()->route()->uri(), ['getMessage' => $th->getMessage(), 'getLine' => $th->getLine(), 'file' => $th->getFile()]);
            return Response::error(trans('app-return.error'));
        }
    }
}
