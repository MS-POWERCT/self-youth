<?php

namespace App\Api;

use App\Models\WeightRecord;
use App\Support\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WeightRecordController extends Controller
{
    /** 新增体重记录 POST /api/weightRecord/create */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'weight'      => 'required|numeric|min:1|max:999.99',
            'unit'        => 'nullable|string|in:kg,jin',
            'recorded_at' => 'nullable|date_format:Y-m-d H:i:s',
            'note'        => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->first(), 1212);
        }

        try {
            $record = WeightRecord::create([
                'user_id'     => Auth::id(),
                'weight'      => round((float) $request->weight, 2),
                'unit'        => $request->input('unit', 'kg'),
                'recorded_at' => $request->input('recorded_at', now()->format('Y-m-d H:i:s')),
                'note'        => $request->input('note'),
            ]);

            return Response::success($record->toApiArray(true));
        } catch (\Throwable $th) {
            Log::error('异常：' . request()->route()->uri(), [
                'getMessage' => $th->getMessage(),
                'getLine'    => $th->getLine(),
                'file'       => $th->getFile(),
            ]);
            return Response::error(trans('app-return.error_msg'));
        }
    }

    /** 获取体重记录列表 POST /api/weightRecord/getList */
    public function getList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'nullable|integer|min:0',
            'size' => 'nullable|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return Response::error(trans('app-return.validator_fails'), 1212);
        }

        $page = max(0, (int) $request->input('page', 0));
        $size = min((int) $request->input('size', 20), 50);
        $userId = Auth::id();

        $records = WeightRecord::where('user_id', $userId)
            ->orderByDesc('recorded_at')
            ->orderByDesc('id')
            ->offset($page * $size)
            ->limit($size)
            ->get();

        $list = $records->map(fn(WeightRecord $record) => $record->toApiArray(true))->values();

        return Response::success([
            'list' => $list,
            'page' => $page,
            'size' => $size,
        ]);
    }

    /** 查看体重记录详情 POST /api/weightRecord/getDetail */
    public function getDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return Response::error(trans('app-return.validator_fails'), 1212);
        }

        $record = WeightRecord::where('id', $request->id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$record) {
            return Response::error('记录不存在', 1214);
        }

        return Response::success($record->toApiArray(true));
    }

    /** 编辑体重记录 POST /api/weightRecord/edit */
    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'          => 'required|integer',
            'weight'      => 'required|numeric|min:1|max:999.99',
            'unit'        => 'nullable|string|in:kg,jin',
            'recorded_at' => 'required|date_format:Y-m-d H:i:s',
            'note'        => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return Response::error(trans('app-return.validator_fails'), 1212);
        }

        $record = WeightRecord::where('id', $request->id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$record) {
            return Response::error('记录不存在', 1214);
        }

        try {
            $record->update([
                'weight'      => round((float) $request->weight, 2),
                'unit'        => $request->input('unit', $record->unit ?? 'kg'),
                'recorded_at' => $request->recorded_at,
                'note'        => $request->input('note'),
            ]);

            return Response::success($record->fresh()->toApiArray(true));
        } catch (\Throwable $th) {
            Log::error('异常：' . request()->route()->uri(), [
                'getMessage' => $th->getMessage(),
                'getLine'    => $th->getLine(),
                'file'       => $th->getFile(),
            ]);
            return Response::error(trans('app-return.error_msg'));
        }
    }

    /** 删除体重记录 POST /api/weightRecord/del */
    public function del(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return Response::error(trans('app-return.validator_fails'), 1212);
        }

        $record = WeightRecord::where('id', $request->id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$record) {
            return Response::error('记录不存在', 1214);
        }

        $record->delete();

        return Response::success();
    }

    /** 获取体重统计 GET /api/weightRecord/stats */
    public function stats(Request $request)
    {
        $userId = Auth::id();

        $records = WeightRecord::where('user_id', $userId)
            ->orderBy('recorded_at')
            ->orderBy('id')
            ->get();

        if ($records->isEmpty()) {
            return Response::success([
                'current_weight' => null,
                'unit'           => 'kg',
                'total_change'   => null,
                'start_weight'   => null,
                'min_weight'     => null,
                'avg_weight'     => null,
                'record_count'   => 0,
            ]);
        }

        $weightsKg = $records->map(fn(WeightRecord $record) => $record->weightInKg());
        $latest = $records->last();
        $first = $records->first();

        $currentWeight = $latest->weight;
        $startWeight = $first->weight;
        $unit = $latest->unit ?? 'kg';

        // 统计统一用 kg 计算，返回时按最新记录单位展示
        $currentKg = $latest->weightInKg();
        $startKg = $first->weightInKg();
        $totalChangeKg = round($currentKg - $startKg, 2);

        $minKg = round($weightsKg->min(), 2);
        $avgKg = round($weightsKg->avg(), 2);

        if ($unit === 'jin') {
            $totalChange = round($totalChangeKg * 2, 2);
            $minWeight = round($minKg * 2, 2);
            $avgWeight = round($avgKg * 2, 2);
        } else {
            $totalChange = $totalChangeKg;
            $minWeight = $minKg;
            $avgWeight = $avgKg;
        }

        return Response::success([
            'current_weight' => $currentWeight,
            'unit'           => $unit,
            'total_change'   => $totalChange,
            'start_weight'   => $startWeight,
            'min_weight'     => $minWeight,
            'avg_weight'     => $avgWeight,
            'record_count'   => $records->count(),
        ]);
    }

    /** 获取体重图表 GET /api/weightRecord/chart */
    public function chart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'days' => 'nullable|integer|min:7|max:365',
        ]);

        if ($validator->fails()) {
            return Response::error(trans('app-return.validator_fails'), 1212);
        }

        $days = (int) $request->input('days', 90);
        $userId = Auth::id();
        $startAt = now()->subDays($days)->startOfDay();

        $records = WeightRecord::where('user_id', $userId)
            ->where('recorded_at', '>=', $startAt)
            ->orderBy('recorded_at')
            ->orderBy('id')
            ->get();

        $points = $records->map(function (WeightRecord $record) {
            return [
                'date'        => $record->recorded_at->format('Y-m-d'),
                'weight'      => $record->weight,
                'unit'        => $record->unit ?? 'kg',
                'recorded_at' => $record->recorded_at->format('Y-m-d H:i:s'),
            ];
        })->values();

        $trend = 'stable';
        $trendLabel = '保持稳定';

        if ($records->count() >= 2) {
            $firstKg = $records->first()->weightInKg();
            $lastKg = $records->last()->weightInKg();
            $diff = round($lastKg - $firstKg, 2);

            if ($diff < -0.1) {
                $trend = 'down';
                $trendLabel = '下降趋势';
            } elseif ($diff > 0.1) {
                $trend = 'up';
                $trendLabel = '上升趋势';
            }
        }

        return Response::success([
            'trend'       => $trend,
            'trend_label' => $trendLabel,
            'days'        => $days,
            'points'      => $points,
        ]);
    }
}
