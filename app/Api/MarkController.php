<?php

namespace App\Api;

use App\Models\MarkCategory;
use App\Models\MarkItem;
use App\Models\MarkModule;
use App\Models\MarkUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Support\Response;
use Illuminate\Support\Facades\Redis;

/**
 * Description of My
 *
 * @author Administrator
 */
class MarkController extends Controller
{

    public function getCategoryList()
    {
        $list = MarkCategory::select('id', 'name', 'icon')->where('status', 1)->get();
        return Response::success($list);
    }

    public function getModuleList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return Response::error(trans('app-return.validator_fails'), 1212);
        }
        $category_id = $request->category_id;
        $list = MarkModule::select('id', 'name', 'title', 'img_url')
            ->where('category_id', $category_id)
            ->where('status', 1)
            ->orderBy('sort')
            ->get();

        try {
            foreach ($list as $item) {
                $item->pv = config('app.env') == 'production' ?
                    Redis::get('mark_item_pv:' . $item->id) ?? 0 :
                    0; // 浏览量
                $item->participant = config('app.env') == 'production' ?
                    Redis::scard('mark_item_participant:' . $item->id) ?? 0 :
                    0; // 参与量
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Response::error($th->getMessage(), 1213);
        }


        return Response::success($list);
    }


    // 这里需要额外处理一下，因为MarkItem表中没有user_id字段，所以不能直接查询，另外要关联用户是否标记了该项
    public function getItemList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'module_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return Response::error(trans('app-return.validator_fails'), 1212);
        }
        // $page = max(0, intval($request->page ?? 0));
        // $size = min(intval($request->size ?? 10), 50);

        $module_id = $request->module_id;
        $list = MarkItem::where('module_id', $module_id)
            ->where('status', 1)
            ->orderBy('sort')
            ->orderBy('id')
            ->get();

        // 每次请求更新浏览量
        if (config('app.env') == 'production') {
            Redis::incr('mark_item_pv:' . $module_id);
        }


        // 这里要和用户标记表进行关联查询，判断用户是否标记了该项
        // 将用户标记的项添加到列表中
        $markList = MarkUser::select('item_id', 'mark_type')
            ->where('user_id', Auth::id())
            ->where('module_id', $module_id)
            ->whereIn('mark_type', [1, 2])
            ->get();
        $markList = $markList->keyBy('item_id')->toArray();
        foreach ($list as $item) {
            $item->mark_type = $markList[$item->id]['mark_type'] ?? 0;
        }
        $pv = config('app.env') == 'production' ?
            Redis::get('mark_item_pv:' . $module_id) ?? 0 :
            0;
        $participant = config('app.env') == 'production' ?
            Redis::scard('mark_item_participant:' . $module_id) ?? 0 :
            0;
        return Response::success([
            'list' => $list,
            'pv' => $pv, // 浏览量
            'participant' => $participant, // 参与量
        ]);
    }


    // 用户进行标记
    public function markItem(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'item_id' => 'required|integer',
            'mark_type' => 'required|in:0,1,2',
        ]);
        if ($validator->fails()) {
            return Response::error(trans('app-return.validator_fails'), 1212);
        }
        $item_id = $request->item_id;
        $mark_type = $request->mark_type;

        // item_id检查这个是否存在
        $item = MarkItem::where('id', $item_id)->first();
        if (!$item) {
            return Response::error('项不存在', 1213);
        }

        // 检查用户是否标记了该项
        $mark = MarkUser::where('user_id', Auth::id())->where('item_id', $item_id)->first();
        if (!$mark) {
            MarkUser::create([
                'user_id' => Auth::id(),
                'module_id' => $item->module_id,
                'item_id' => $item_id,
                'mark_type' => $mark_type,
            ]);
        } else {
            $mark->mark_type = $mark_type;
            $mark->save();
        }

        // 每次用户进行标记就要记录参与量，一个用户最大只能标记一次，使用set进行唯一存储
        Redis::sadd('mark_item_participant:' . $item->module_id, Auth::id());

        return Response::success();
    }

    // 批量标记项
    public function batchMarkItem(Request $request) {}
}
