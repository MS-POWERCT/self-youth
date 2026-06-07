<?php

namespace App\Api;

use App\Models\HabitStat;
use App\Models\UserHabit;
use App\Services\ToolsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Support\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Description of My
 *
 * @author Administrator
 */
class HabitController extends Controller
{

    // 获取用户习惯列表
    public function getList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:1,2',
        ]);

        if ($validator->fails()) {
            return Response::error(trans('app-return.validator_fails'), 1212);
        }

        $type = $request->type;
        $user = Auth::user();

        $list = UserHabit::where('user_id', $user->id)->where('type', $type)
            ->where('is_show', 1)->orderBy('sort', 'DESC')->limit(20)->get();

        return Response::success($list);
    }


    // 获取可以编辑的习惯列表
    public function getEditableList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:1,2',
        ]);

        if ($validator->fails()) {
            return Response::error(trans('app-return.validator_fails'), 1212);
        }
        $user = Auth::user();

        $list = UserHabit::where('user_id', $user->id)->where('type', $request->type)
            ->where('fixed', 0)
            ->orderBy('sort', 'DESC')->limit(20)->get();

        return Response::success($list);
    }


    // 返回icon列表
    public function getIconList()
    {
        // 从缓存中获取
        $list = Cache::get('habit_icon_list');
        if ($list) {
            return Response::success(json_decode($list, true));
        }

        $list = DB::table('user_habit_icon')->select('id', 'name', 'icon')->get();

        // 缓存
        Cache::set('habit_icon_list', json_encode($list));

        return Response::success($list);
    }



    // 新增习惯
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',  // 习惯名称
            'type' => 'required|in:1,2',          // 类型：1-打卡型，2-计数型
            'icon' => 'required|string|max:100',  // 图标
        ]);

        if ($validator->fails()) {
            return Response::error(trans('app-return.validator_fails'), 1212);
        }

        // 检查唯一性
        $exists = UserHabit::where('user_id', Auth::id())
            ->where('name', $request->name)
            ->where('type', $request->type)
            ->exists();

        if ($exists) {
            return Response::error('该习惯已存在');
        }

        try {
            // 创建习惯
            UserHabit::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'type' => $request->type,
                'sort' => 0,
                'is_show' => 1,
                'fixed' => 0,
                'icon' => $request->icon,
            ]);
            return Response::success();
        } catch (\Throwable $th) {
            Log::error('异常：' . request()->route()->uri(), ['getMessage' => $th->getMessage(), 'getLine' => $th->getLine(), 'file' => $th->getFile()]);
            return Response::error(trans('app-return.error_msg'));
        }
    }


    // 隐藏/显示
    public function hide(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'is_show' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return Response::error(trans('app-return.validator_fails'), 1212);
        }

        $user_id = Auth::id();
        $habit = UserHabit::where('user_id', $user_id)->where('fixed', 0)->where('id', $request->id)->first();
        if (!$habit) {
            return Response::error(trans('app-return.not_found'), 456346);
        }
        $habit->is_show = $request->is_show;
        $habit->save();
        return Response::success();
    }



    // 删除习惯
    // 删除习惯后，该习惯下的所有数据也会被删除
    // N天只能删除1个习惯
    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return Response::error(trans('app-return.validator_fails'), 1212);
        }
        $user_id = Auth::id();

        // 检查最近是否删除过习惯-通过缓存
        $key = 'delete_habit:' . $user_id;
        $cachedTime = Cache::get($key);
        if ($cachedTime) {
            // 计算一下还是多少时间
            $diffTime = $cachedTime - time();
            return Response::error('最近已删除过习惯，' . ToolsService::formatSeconds($diffTime) . '后才能删除');
        }

        $habit = UserHabit::where('user_id', $user_id)
            ->where('fixed', 0)->where('id', $request->id)->first();
        if (!$habit) {
            return Response::error(trans('app-return.not_found'), 456346);
        }
        // 检查是否是最后1个习惯
        $count = UserHabit::where('user_id', $user_id)->where('is_show', 1)->count();
        if ($count == 1) {
            return Response::error('最后1个习惯不能删除');
        }

        // $habit->delete();
        $habit->deleted_at = now();
        $habit->name = $habit->name . '_' . ToolsService::getRandomStr(6, 1) . '_已删除';
        $habit->save();
        $deleteTime = ToolsService::getCache('HABIT_DELETE_TIME');
        // 缓存最近删除时间
        Cache::put($key, time() + $deleteTime, $deleteTime);

        return Response::success();
    }


    //编辑习惯
    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'name' => 'required|string|max:100',  // 习惯名称
            'type' => 'required|in:1,2',          // 类型：1-打卡型，2-计数型
            'icon' => 'required|string|max:100',  // 图标
        ]);


        if ($validator->fails()) {
            return Response::error(trans('app-return.validator_fails'), 1212);
        }

        // 检查唯一性
        $habit = UserHabit::where('user_id', Auth::id())
            ->where('id', $request->id)
            ->first();

        if (!$habit) {
            return Response::error(trans('app-return.not_found'), 456346);
        }
        // 检查名称是否重复
        $exists = UserHabit::where('user_id', Auth::id())
            ->where('name', $request->name)
            ->where('type', $request->type)
            ->where('id', '!=', $request->id)
            ->exists();

        if ($exists) {
            return Response::error('该习惯已存在');
        }

        try {
            // 创建习惯

            $habit->name = $request->name;
            $habit->icon = $request->icon;
            $habit->save();

            return Response::success();
        } catch (\Throwable $th) {
            Log::error('异常：' . request()->route()->uri(), ['getMessage' => $th->getMessage(), 'getLine' => $th->getLine(), 'file' => $th->getFile()]);
            return Response::error(trans('app-return.error_msg'));
        }
    }


    // 热力贡献值 GET /api/habit/stat
    public function stat()
    {
        // 得到364天内的数据
        $date = date('Y-m-d', strtotime('-366 days'));

        $list = HabitStat::where('user_id', Auth::id())
            ->whereDate('date', '>', $date)->get();

        $data = [];
        // 处理数据，key是date，value 是total
        foreach ($list as $key => $value) {
            # code...
            $data[$value->date] = $value->total;
        }

        return Response::success($data);
    }
}
