<?php

namespace App\Api;

use App\Models\LoverCircle;
use App\Models\LoverComment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\Rule\NoUrlLinksRule;
use App\Services\TrieTree\TrieTreeServer;
use App\Support\Response;

/**
 * Description of LoverCommentController
 *
 * @author Administrator
 */
class LoverCommentController extends Controller
{


    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'content' => ['required', 'string', 'max:500'],
        ], [
            'id.required' => trans('app-return.validator_fails'),
            'content.required' => trans('app-return.validator_fails'),
            'content.max' => trans('app-return.validator.exceeding_maximum')
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->first(), 1212);
        }

        $user = Auth::user();
        $content = $request->content ?? '';

        $detail = LoverCircle::where('status', 'ENABLED')->where('id', $request->id)->first();
        if (!$detail) {
            return Response::error(trans('app-return.lover_not_found'), 5000);
        }

        // 1.这里要进行一些限制,比如有些用户不能发

        // 2.也不能单日发太多的动态


        // 检查敏感词和link
        $trie = TrieTreeServer::createdLinks();
        if ($content && $trie->filter($content)) {
            return Response::error(trans('app-return.lover_content_sensitive'), 5000);
        } else if (!NoUrlLinksRule::check($content)) {
            return Response::error(trans('app-return.lover_link_not'), 5001);
        }

        LoverComment::create([
            'circle_id' => $detail->id,
            'user_id' => $user->id,
            'content' => $content,
        ]);

        // 这里给发布的用户添加情侣圈的相应数
        // code...

        return Response::success([]);
    }

    public function getList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return Response::error($validator->errors()->first(), 1212);
        }

        $list = LoverComment::with('user')->where('circle_id', $request->id)->get();

        return Response::success($list);
    }



    /**
     *
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

        $detail = LoverComment::where('user_id', $user->id)->where('id', $request->id)->first();

        if (empty($detail)) {
            return Response::error(trans('app-return.lover_not_found'), 5000);
        }

        $detail->deleted_at = date('Y-m-d H:i:s');
        $detail->save();

        return Response::success([]);
    }
}
