<?php

namespace App\Api;

use Illuminate\Support\Facades\Validator;
use App\Models\Announce;
use App\Services\I18nService;
use App\Support\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of My
 *
 * @author Administrator
 */
class AnnounceController extends Controller
{

    /**
     * 获取公告列表（使用 Redis 缓存优化）
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getList(Request $request)
    {
        // 参数验证和规范化
        $page = max(0, (int)($request->input('page', 0)));
        $size = min(max(1, (int)($request->input('size', 10))), 20);
        $postion = $request->input('postion', 'HOME');
        $user_lang = $GLOBALS['user_lang'] ?? 'zh_CN';

        // 构建缓存键（包含所有影响结果的参数）
        $cache_key = sprintf(
            'announce_list:%s%d%d%s',
            $postion,
            $page,
            $user_lang
        );

        // 尝试从 Redis 获取缓存
        $list = Redis::get($cache_key);

        if ($list) {
            $list = json_decode($list, true);
        } else {
            // 缓存未命中，查询数据库
            $list = Announce::select(['id', 'sort', 'is_popup', 'img_url', 'jumpType', 'link', 'postion', 'title', 'status', 'created_at'])
                ->where('postion', $postion)
                ->where('status', 1)
                ->orderBy('sort', 'DESC')
                ->orderBy('id', 'DESC') // 添加二级排序，确保稳定性
                ->skip($page * $size)
                ->take($size)
                ->get();

            // 进行翻译处理
            if ($user_lang !== 'zh_CN' && count($list) > 0) {
                $list = I18nService::getTranslateList($list, Announce::class);
            }

            Redis::set($cache_key, json_encode($list));
        }

        return Response::success($list);
    }


    /**
     * 获取公告详情（使用 Redis 缓存优化）
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDetail(Request $request)
    {
        // 参数验证
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return Response::error(trans('app-return.validator_fails'), 1212);
        }

        $id = $request->id;

        $detail = Announce::where('id', $id)
            ->where('status', 1)
            ->first();

        if (!$detail) {
            return Response::error(trans('app-return.not_found'), 456346);
        }
        $detail = I18nService::getTranslateDetail($detail, Announce::class);

        return Response::success($detail);
    }

    // 获取要弹出的公告接口
    public function getPopup(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'postion' => 'required',
        ]);
        if ($validator->fails()) {
            return Response::error(trans('app-return.validator_fails'), 1212);
        }

        $postion = $request->postion;

        // 构建缓存键（包含所有影响结果的参数）
        $cache_key = sprintf(
            'announce_popup:%s%d',
            $postion,
        );

        // 尝试从 Redis 获取缓存
        $detail = Redis::get($cache_key);

        if ($detail) {
            $detail = json_decode($detail, true);
        } else {
            $detail = Announce::where('postion', $postion)
                ->where('is_popup', 1)
                ->orderBy('id', 'desc')
                ->first();

            $detail = I18nService::getTranslateDetail($detail, Announce::class);

            Redis::set($cache_key, json_encode($detail));
        }

        return Response::success($detail);
    }
}
