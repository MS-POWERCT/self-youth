<?php

namespace App\Api;

use App\Models\LoverCircle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\LoverCircleService;
use App\Services\Rule\NoUrlLinksRule;
use App\Services\TrieTree\TrieTreeServer;
use App\Support\Response;
use Illuminate\Support\Facades\Redis;

/**
 * Description of My
 *
 * @author Administrator
 */
class LoverCircleController extends Controller
{


    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => ['required', 'string', 'max:1500'],
        ], [
            'content.required' => trans('app-return.validator_fails'),
            'content.max' => trans('app-return.validator.exceeding_maximum')
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->first(), 1212);
        }

        $user = Auth::user();
        $content = $request->content ?? '';


        // 1.这里要进行一些限制,比如有些用户不能发

        // 2.也不能单日发太多的动态


        // 检查敏感词和link
        $trie = TrieTreeServer::createdLinks();
        if ($content && $trie->filter($content)) {
            return Response::error(trans('app-return.lover_content_sensitive'), 5000);
        } else if (!NoUrlLinksRule::check($content)) {
            return Response::error(trans('app-return.lover_link_not'), 5001);
        }
        $images = array_unique(explode(',', $request->images));
        if (count($images) > 4) {
            return Response::error(trans('app-return.lover_image_num_max', ['num' => 9]));
        }
        $images = implode(',', $images);
        $status = 'ENABLED';

        LoverCircle::create([
            'user_id' => $user->id,
            'content' => $content,
            'images' => $images,
            'status' => $status
        ]);

        return Response::success([]);
    }

    public function getList(Request $request)
    {
        $page = intval($request->page ?? 0);
        $size = min(intval($request->size ?? 10), 50);
        $user = Auth::user();


        $query = LoverCircle::with(['user', 'comment']);

        if ($request->user_id) {
            $query = $query->where('user_id', $request->user_id);
        }
        $list = $query->where('status', 'ENABLED')
            ->orderBy('created_at', 'DESC')->offset($page * $size)->limit($size)->get();

        $list = LoverCircleService::organizeData($list, $user);

        return Response::success($list);
    }



    // 用户进行点赞或者踩
    public function userClick(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->first(), 1212);
        }
        $user = Auth::user();


        $detail = LoverCircle::where('status', 'ENABLED')->where('id', $request->id)->first();
        if (!$detail) {
            return Response::error(trans('app-return.lover_not_found'));
        }

        return Response::success([]);
    }


    /**
     * 软删除部落圈
     */
    public function delData(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return Response::error($validator->errors()->first(), 1212);
        }

        $user = Auth::user();

        $detail = LoverCircle::where('user_id', $user->id)->where('id', $request->id)->first();

        if (empty($detail)) {
            return Response::error(trans('app-return.lover_not_found'));
        }

        $detail->deleted_at = date('Y-m-d H:i:s');
        $detail->save();

        return Response::success([]);
    }
}
