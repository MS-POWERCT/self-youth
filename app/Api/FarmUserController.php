<?php

namespace App\Api;

use App\Models\Asset;
use App\Models\FarmDeliveryRecord;
use App\Models\FarmHandbook;
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
use Illuminate\Support\Facades\Redis;
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

        $init_data = [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'level_id' => $farm_user_level,
            'level_title' => trans('app-status.farm_user.level_title')[$farm_user_level] ?? '称号之外', // 用户称号
            'exp' => FarmUserService::getFarmUserExp($user->id), // 用户经验
            'next_level_exp' => FarmUserService::getFarmUserNextLevelExp($farm_user_level + 1), // 下一级需要的经验
            'wallet_assets' => WalletAssetService::getAccountAssetAll($user),
            'default_exp' => [
                'plant' => FarmUserService::$FARM_PLANT_EXP, // 种植得多少经验
                'shovel' => FarmUserService::$FARM_SHOVEL_EXP, // 铲除得多少经验
                'water' => FarmUserService::$FARM_WATER_EXP, // 除草得多少经验
                'kill' => FarmUserService::$FARM_KILL_EXP, // 击虫得多少经验
                'till' => FarmUserService::$FARM_TILL_EXP, // 翻土得多少经验
            ],
            'handbooks' => FarmHandbook::pluck('name', 'id')->toArray(), //图谱
            'warehouse_size' => FarmWarehouseService::getWareHouseSize($user->id), // 仓库大小
            'warehouse_use' => FarmWarehouseService::getWareHouseUse($user->id), // 仓库使用情况
            'next_extend_price' => FarmWarehouseService::getNextExtendPrice($user->id), // 下一个扩充价格
            'next_extend_size' => FarmWarehouseService::$FARM_EXTEND_NUM, // 下一个扩充大小
        ];

        return Response::success($init_data);
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
        $land_id = $request->land_id ?? 0;
        $user = Auth::user();

        $farm_lands = $land_id > 0
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

        // 检查仓库容量是否足够
        // 要先判断当前入仓到是否是新的
        if (FarmWarehouseService::isFullHouse($user->id, $farm_land->handbook_id)) {
            return Response::error('仓库已满,请先扩充仓库', 1212);
        }
        // 判断果实是否超过最大数量---这段代码先不开启code...
        // $farm_warehouse = FarmWarehouseService::getUserWareHouse($user, $farm_land->handbook_id, 'fruit');
        // if ($farm_warehouse->num + $farm_land->residue_output > FarmWarehouseService::$FARM_WAREHOUSE_MAX) {
        //     return Response::error("果实数量超过最大数量" . FarmWarehouseService::$FARM_WAREHOUSE_MAX . "个");
        // }


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



    // 获取用户土地
    public function getLandList()
    {
        return Response::success(FarmUserLandService::getLandList(Auth::user()));
    }

    // 获取土地升级的信息
    public function getLandUpgradeInfo()
    {
        $user = Auth::user();
        $farm_user_level = FarmUserService::getFarmUserLevel($user->id);

        // 获取用户各等级土地数量
        $lands = FarmUserLand::where('user_id', $user->id)->where('status', '<>', 9)->get();
        $landCounts = [
            1 => $lands->whereIn('level_id', [1, 2, 3])->count(), // 普通土地
            2 => $lands->whereIn('level_id', [2, 3])->count(), // 红土地
            3 => $lands->where('level_id', 3)->count(), // 金土地
        ];

        $upgrade_info = [];
        $levelConfig = FarmUserLandService::$LEVEL;

        // 定义升级规则
        $upgradeRules = [
            1 => [
                'upgrade_type' => 1,
                'requirement' => null, // 普通土地无前置要求
                'action' => '开垦',
                'desc' => '开垦新的土地',
            ],
            2 => [
                'upgrade_type' => 2,
                'requirement' => fn() => $landCounts[1] > $landCounts[2], // 普通土地数量必须大于红土地数量
                'action' => '升级',
                'desc' => '升级红土地',
            ],
            3 => [
                'upgrade_type' => 3,
                'requirement' => fn() => $landCounts[2] > $landCounts[3], // 红土地数量必须大于金土地数量
                'action' => '升级',
                'desc' => '升级金土地',
            ],
        ];

        foreach ($upgradeRules as $levelId => $rule) {
            $nextCount = $landCounts[$rule['upgrade_type']] + 1;

            // 检查是否达到土地数量上限
            if ($nextCount > FarmUserLandService::$MAX_LAND_COUNT) {
                continue;
            }

            // 检查等级要求
            if ($levelConfig[$levelId]['level'][$nextCount] > $farm_user_level) {
                continue;
            }

            // 检查前置要求（红土地需要普通土地，金土地需要红土地）
            if ($rule['requirement'] && !$rule['requirement']()) {
                continue;
            }

            $upgrade_info[] = [
                'level_id' => $levelId,
                'name' => $levelConfig[$levelId]['name'],
                'price' => $levelConfig[$levelId]['price'][$nextCount],
                'desc' => $rule['desc'],
                'bottom' => $rule['action'],
                'upgrade_type' => $rule['upgrade_type'],
            ];
        }

        return Response::success($upgrade_info);
    }


    // 开垦土地或者升级土地
    public function upgradeLand(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'upgrade_type' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return Response::error($validator->errors()->first(), 1212);
        }

        $upgrade_type = $request->upgrade_type;
        if (!in_array($upgrade_type, [1, 2, 3])) {
            return Response::error('土地类型无效', 1212);
        }

        $user = Auth::user();
        $farm_user_level = FarmUserService::getFarmUserLevel($user->id);

        // 获取用户各等级土地数量
        $lands = FarmUserLand::where('user_id', $user->id)->where('status', '<>', 9)->get();
        $landCounts = [
            1 => $lands->whereIn('level_id', [1, 2, 3])->count(),
            2 => $lands->whereIn('level_id', [2, 3])->count(),
            3 => $lands->where('level_id', 3)->count(),
        ];

        $levelConfig = FarmUserLandService::$LEVEL;
        $nextCount = $landCounts[$upgrade_type] + 1;

        // 检查是否达到土地数量上限
        if ($nextCount > FarmUserLandService::$MAX_LAND_COUNT) {
            return Response::error('土地数量已达上限', 1212);
        }

        // 检查等级要求
        if ($levelConfig[$upgrade_type]['level'][$nextCount] > $farm_user_level) {
            return Response::error('等级不足，无法升级', 1212);
        }

        // 检查升级约束
        if ($upgrade_type == 2 && $landCounts[1] <= $landCounts[2]) {
            return Response::error('普通土地数量不足，无法升级红土地', 1212);
        }
        if ($upgrade_type == 3 && $landCounts[2] <= $landCounts[3]) {
            return Response::error('红土地数量不足，无法升级金土地', 1212);
        }

        // 计算价格
        $price = $levelConfig[$upgrade_type]['price'][$nextCount];

        // 得到用户资产
        $wallet_asset = WalletAssetService::getWalletAsset($user, 1);

        try {
            DB::beginTransaction();

            // 检查是否有足够的金币
            WalletAssetService::checkBalance($wallet_asset, $price);

            // 扣除金币
            WalletAssetService::change($wallet_asset, -$price, [
                'module_code' => 'FARM_LAND_UPGRADE',
            ]);

            // 执行土地操作
            $land = null;
            if ($upgrade_type == 1) {
                // 开垦普通土地：激活一块未开垦的土地
                $land = FarmUserLand::where('user_id', $user->id)
                    ->where('level_id', 1)
                    ->where('status', 9)
                    ->orderBy('id', 'asc')
                    ->first();
                if ($land) {
                    $land->update(['status' => 0]);
                }
            } else if ($upgrade_type == 2) {
                // 升级红土地：将一块普通土地升级为红土地
                $land = FarmUserLand::where('user_id', $user->id)
                    ->where('level_id', 1)
                    ->where('status', '<>', 9)
                    ->orderBy('id', 'asc')
                    ->first();
                if ($land) {
                    $land->update(['level_id' => 2]);
                }
            } else if ($upgrade_type == 3) {
                // 升级金土地：将一块红土地升级为金土地
                $land = FarmUserLand::where('user_id', $user->id)
                    ->where('level_id', 2)
                    ->where('status', '<>', 9)
                    ->orderBy('id', 'asc')
                    ->first();
                if ($land) {
                    $land->update(['level_id' => 3]);
                }
            }

            if (!$land) {
                throw new \Exception('没有可操作的土地', 1212);
            }

            DB::commit();

            // 返回用户土地列表
            $lands = FarmUserLandService::getLandList($user);
            return Response::success($lands);
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($th->getCode() == 1235) {
                return Response::error($th->getMessage());
            } else {
                Log::error('异常：' . request()->route()->uri(), ['getMessage' => $th->getMessage(), 'getLine' => $th->getLine(), 'file' => $th->getFile()]);
                return Response::error($th->getMessage());
            }
        }
    }


    // ============== 特殊建筑接口 ==============
    /**
     * 得到特殊建筑的基础信息
     */
    public function getSpecialInfo()
    {
        $user = Auth::user();
        $farm_user_level = FarmUserService::getFarmUserLevel($user->id);
        $level_exp = FarmUserService::getFarmUserNextLevelExp($farm_user_level);
        $exp = floor($level_exp * 0.02);
        $gold = ($farm_user_level + 1) * 50;

        return Response::success([
            'world_tree' => [
                'exp' => $exp,
                'gold' => $gold,
                'click_count' => intval(Redis::hget('farm_tree_count', $user->id)),
                'is_click' => intval(Redis::sismember('farm_tree_click', $user->id)),
                'total_exp' => intval(Redis::hget('farm_tree_total_exp', $user->id)),
                'total_gold' => intval(Redis::hget('farm_tree_total_gold', $user->id)),
            ],
        ]);
    }
    /**
     * 点击世界树
     *
     */
    public function clickWorldTree()
    {
        $user = Auth::user();

        // 判断是否点击过世界树
        if (Redis::sismember('farm_tree_click', $user->id)) {
            return Response::error('您已领取', 1212);
        }


        $farm_user_level = FarmUserService::getFarmUserLevel($user->id);
        $level_exp = FarmUserService::getFarmUserNextLevelExp($farm_user_level);
        $exp = floor($level_exp * 0.02);
        $gold = ($farm_user_level + 1) * 50;

        // 更新经验
        FarmUserService::farmAddExp($user->id, $exp);

        // 更新用户资产
        $wallet_asset = WalletAssetService::getWalletAsset($user, 1);
        WalletAssetService::change($wallet_asset, $gold, [
            'module_code' => 'FARM_WORLD_TREE',
        ]);

        // 点击后放到缓存中set
        Redis::sadd('farm_tree_click', $user->id);
        // 记录总点击次数
        Redis::hincrby('farm_tree_count', $user->id, 1);
        // 累计获得的经验
        Redis::hincrby('farm_tree_total_exp', $user->id, $exp);
        // 累计获得的金币
        Redis::hincrby('farm_tree_total_gold', $user->id, $gold);

        return Response::success();
    }



    // ============== 配送工具接口 ==============
    /**
     * 得到配送工具的基础信息
     */
    public function getDeliveryToolInfo()
    {
        $user = Auth::user();
        return Response::success(FarmUserService::getFarmUserDeliveryToolList($user));   // 获得用户的工具
    }

    // 用户购买配送工具
    public function buyDeliveryTool(Request $request)
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(array('res_code' => 1212, 'res_msg' => trans('app-return.validator_fails'), 'data' => []));
        }

        $user = Auth::user();
        $id = $params['id'];
        $tool = FarmUserService::$FARM_DELIVERY_TOOL[$id] ?? [];
        if (!$tool) {
            return Response::error('配送工具不存在', 1212);
        }

        $user_delivery_tools = json_decode(Redis::hget('users_delivery_tool', $user->id), true) ?? [];
        // 检查用户是否有这个工具
        if (in_array($tool['id'], $user_delivery_tools)) {
            return Response::error('您已购买过', 1212);
        }

        // 检查用户是否有足够的金币
        $wallet_asset = WalletAssetService::getWalletAsset($user, 1);


        try {
            DB::beginTransaction();
            WalletAssetService::checkBalance($wallet_asset, $tool['price']);


            // 扣除余额
            WalletAssetService::change($wallet_asset, -$tool['price'], [
                'module_code' => 'FARM_DELIVERY_TOOL',
            ]);

            // 更新用户配送工具
            // 先添加到数组中
            $user_delivery_tools[] = $tool['id'];
            // 再更新缓存
            Redis::hset('users_delivery_tool', $user->id, json_encode($user_delivery_tools, JSON_UNESCAPED_UNICODE));
            DB::commit();
            return Response::success();
        } catch (\Throwable $th) {
            DB::rollBack();
            return Response::error($th->getMessage());
        }
    }

    // 用户使用配送工具
    public function useDeliveryTool(Request $request)
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'tool_id' => 'required|integer',
            'handbook_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(array('res_code' => 1212, 'res_msg' => trans('app-return.validator_fails'), 'data' => []));
        }

        $user = Auth::user();
        $tool_id = $params['tool_id'];
        $handbook_id = $params['handbook_id'];
        $tool = FarmUserService::$FARM_DELIVERY_TOOL[$tool_id] ?? [];
        if (!$tool) {
            return Response::error('配送工具不存在', 1212);
        }

        $user_delivery_tools = json_decode(Redis::hget('users_delivery_tool', $user->id), true) ?? [];
        // 检查用户是否有这个工具
        if (!in_array($tool['id'], $user_delivery_tools)) {
            return Response::error('您未购买过', 1212);
        }

        // 检查用户是否有这个仓库果实
        $warehouse = FarmWarehouse::with(['handbook'])->where('user_id', $user->id)->where('handbook_id', $handbook_id)
            ->where('type', 'fruit')
            ->where('num', '>', 0)
            ->first();
        if (!$warehouse) {
            return Response::error('仓库果实不存在', 1212);
        }

        // 获得判断这次运送多少，如果用户仓库果实数量不足，就运送所有
        $send_num = min($warehouse['num'], $tool['capacity']);
        //

        try {
            DB::beginTransaction();
            // 更新用户仓库果实数量
            $warehouse->update([
                'num' => $warehouse['num'] - $send_num,
            ]);

            // 创建一条配送记录
            FarmDeliveryRecord::create([
                'user_id' => $user->id,
                'tool_id' => $tool['id'],
                'handbook_id' => $handbook_id,
                'num' => $send_num,
                'start_at' => now(),
                'end_at' => now()->addSeconds($tool['delivery_time']),
                'asset_id' => $warehouse->handbook->selling_asset_id,
                'amount' => $warehouse->handbook->selling_price * $send_num,
                'status' => 0
            ]);
            DB::commit();
            return Response::success(FarmUserService::getFarmUserDeliveryToolList($user));   // 获得用户的工具
        } catch (\Throwable $th) {
            DB::rollBack();
            return Response::error($th->getMessage());
        }
    }

    // 配送结束后更新用户资产和配送记录
    public function updateDeliveryRecord(Request $request)
    {
        $params = $request->all();
        $validator = Validator::make($params, [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(array('res_code' => 1212, 'res_msg' => trans('app-return.validator_fails'), 'data' => []));
        }

        $user = Auth::user();
        $id = $params['id'];
        $delivery_record = FarmDeliveryRecord::where('user_id', $user->id)->where('id', $id)->where('status', 0)->first();
        if (!$delivery_record) {
            return Response::error('配送记录不存在', 1212);
        }

        try {
            DB::beginTransaction();

            // 更新用户资产
            $wallet_asset = WalletAssetService::getWalletAsset($user, $delivery_record['asset_id']);
            WalletAssetService::change($wallet_asset, $delivery_record['amount'], [
                'module_code' => 'FARM_DELIVERY_RECORD',
            ]);

            // 更新配送记录
            $delivery_record->status = 1;
            $delivery_record->save();

            DB::commit();
            return Response::success(FarmUserService::getFarmUserDeliveryToolList($user));   // 获得用户的工具
        } catch (\Throwable $th) {
            DB::rollBack();
            return Response::error($th->getMessage());
        }
    }
}
