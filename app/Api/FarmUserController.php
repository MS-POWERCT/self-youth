<?php

namespace App\Api;

use App\Models\FarmUserLand;
use App\Models\FarmWarehouse;
use Illuminate\Support\Facades\Auth;
use App\Services\FarmUserLandService;
use App\Services\FarmUserService;
use App\Services\FarmWarehouseService;
use App\Services\WalletAssetService;
use App\Support\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Description of My
 *
 * @author Administrator
 */
class FarmUserController extends Controller
{

    // 初始化农场参数等
    public function initFarm()
    {
        $user = Auth::user();

        $farm_user_level = FarmUserService::getFarmUserLevel($user->id);

        $farm_user = [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'level_id' => $farm_user_level,
            'level_title' => trans('app-status.farm_user.level_title')[$farm_user_level] ?? '--', // 用户称号
            'exp' => FarmUserService::getFarmUserExp($user->id), // 用户经验
            'next_level_exp' => FarmUserService::getFarmUserNextLevelExp($farm_user_level + 1), // 下一级需要的经验
            'wallet_assets' => WalletAssetService::getAccountAssetAll($user),
        ];

        return Response::success($farm_user);
    }



    // 获取用户土地
    public function getLandList()
    {
        return Response::success(FarmUserLandService::getLandList(Auth::user()));
    }


    // 种植
    public function plant(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'handbook_id' => 'required|integer',
            'land_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return Response::error($validator->errors()->first(), 1212);
        }

        // 这里要考虑的是普通种子不能种植其他(红土地,以此类推)
        // code...

        $user = Auth::user();
        $handbook_id = $request->handbook_id;
        $land_id = $request->land_id;
        // 检查该土地是否空闲
        $farm_land = FarmUserLand::where('user_id', $user->id)->where('id', $land_id)->where('status', 0)->first();
        if (!$farm_land) {
            return Response::error(trans('app-return.not_found'), 1212);
        }

        $farm_warehouse = FarmWarehouse::with('handbook')->where('user_id', $user->id)->where('handbook_id', $handbook_id)
            ->where('type', 'seed')->where('num', '>', 0)->first();
        if (!$farm_warehouse) {
            return Response::error(trans('app-return.not_found'), 1212);
        }



        try {
            DB::beginTransaction();


            $plant_mature_at = date('Y-m-d H:i:s', time() + ($farm_warehouse->handbook->mature_time)); // 这里可以对加速时间进行计算

            $farm_land->handbook_id = $handbook_id;
            $farm_land->plant_mature_at = $plant_mature_at; // 成熟时间
            $farm_land->plant_start_at = date('Y-m-d H:i:s'); // 种植时间
            $farm_land->residue_output = 0;
            $farm_land->total_output = 0;
            $farm_land->quarter = 1; // 季度
            $farm_land->status = 1;
            $farm_land->save();


            // 种子数量-1
            $farm_warehouse->num -= 1;
            $farm_warehouse->save();


            FarmUserService::farmAddExp($user->id, FarmUserService::$FARM_PLANT_EXP); // 增加经验

            DB::commit();
            return Response::success(FarmUserLandService::getLandList($user));
        } catch (\Throwable $th) {

            DB::rollBack();
            Log::error('异常：' . request()->route()->uri(), [
                'getMessage' => $th->getMessage(),
                'getLine' => $th->getLine(),
                'getFile' => $th->getFile()
            ]);
            if ($th->getCode() == 1235) {
                return Response::error($th->getMessage(), 1235);
            }
            return Response::error(trans('app-return.error_msg'));
        }
    }


    // 刷新用户土地
    public function refresh(Request $request)
    {
        $land_id = $request->land_id;
        $user = Auth::user();

        $farm_lands = $land_id
            ? FarmUserLand::with('handbook')->where('user_id', $user->id)->where('id', $land_id)->get()
            : FarmUserLand::with('handbook')->where('user_id', $user->id)->get();

        foreach ($farm_lands as $farm_land) {
            if ($farm_land->status == 1 && $farm_land->plant_mature_at <= date('Y-m-d H:i:s')) {

                $output = $farm_land->handbook->quarter_output_num;
                // 下面做产出的附加逻辑

                FarmUserLand::where('id', $farm_land->id)->update([
                    'total_output' => $output,
                    'residue_output' => $output,
                    'status' => 2,
                ]);
            } elseif ($farm_land->status == 1) {
                // 这里可以添加放一些虫子或者杂草让用户可以进行除草
            }
        }

        return Response::success(FarmUserLandService::getLandList($user));
    }



    // 收获
    public function harvest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'land_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return Response::error($validator->errors()->first(), 1212);
        }

        $user = Auth::user();
        $land_id = $request->land_id;
        $farm_land = FarmUserLand::with('handbook')->where('user_id', $user->id)->where('id', $land_id)->where('status', 2)->first();

        if (!$farm_land) {
            return Response::error(trans('app-return.not_found'), 1212);
        }
        try {
            DB::beginTransaction();


            $farm_warehouse = FarmWarehouseService::getUserWareHouse($user, $farm_land->handbook_id, 'fruit');
            $farm_warehouse->num += $farm_land->residue_output; // 增加果实
            $farm_warehouse->save();

            // 这里判断是枯萎还是进入下一季
            $farm_land->residue_output = 0;
            $farm_land->total_output = 0;
            if ($farm_land->handbook->quarter > $farm_land->quarter) {
                $farm_land->status = 1;
                $farm_land->quarter += 1;
                $farm_land->plant_mature_at = date('Y-m-d H:i:s', time() + ($farm_land->handbook->mature_after_time)); // 增加成熟时间
            } else {
                $farm_land->status = 3; // 设置枯萎状态
            }
            $farm_land->save();


            FarmUserService::farmAddExp($user->id, $farm_land->handbook->quarter_exp); // 增加经验

            DB::commit();
            return Response::success(FarmUserLandService::getLandList($user));
        } catch (\Throwable $th) {

            DB::rollBack();
            Log::error('异常：' . request()->route()->uri(), [
                'getMessage' => $th->getMessage(),
                'getLine' => $th->getLine(),
                'getFile' => $th->getFile()
            ]);
            if ($th->getCode() == 1235) {
                return Response::error($th->getMessage(), 1235);
            }
            return Response::error(trans('app-return.error_msg'));
        }
    }


    /**
     * 铲除
     * 可以铲除正常种植的但不是可以收获状态
     *
     * @param Request $request
     * @return void
     */
    public function remove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'land_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return Response::error($validator->errors()->first(), 1212);
        }

        $user = Auth::user();
        $land_id = $request->land_id;
        // 种植中和枯萎的可以铲除
        $farm_land = FarmUserLand::with('handbook')->where('user_id', $user->id)->where('id', $land_id)->whereIn('status', [1, 3])->first();

        if (!$farm_land) {
            return Response::error(trans('app-return.not_found'));
        }

        $farm_land->status = 0;
        $farm_land->save();

        FarmUserService::farmAddExp($user->id, FarmUserService::$FARM_SHOVEL_EXP); // 增加经验

        return Response::success(FarmUserLandService::getLandList($user));
    }
}
