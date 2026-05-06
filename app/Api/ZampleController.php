<?php

namespace App\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Zample;
use App\Support\Response;
use Illuminate\Support\Facades\Log;

/**
 * Description of My
 *
 * @author Administrator
 */
class ZampleController extends Controller
{


    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);


        if ($validator->fails()) {
            return Response::error(trans('app-return.validator_fails'), 1212);
        }

        try {
            DB::beginTransaction();


            DB::commit();
            return Response::success();
        } catch (\Throwable $th) {
            DB::rollBack();
            if ($th->getCode() == 1235) {
                return Response::error($th->getMessage());
            } else {
                Log::error('异常：' . request()->route()->uri(), ['getMessage' => $th->getMessage(), 'getLine' => $th->getLine(), 'file' => $th->getFile()]);
                return Response::error(trans('app-return.error_msg'));
            }
        }
    }

    public function getList(Request $request)
    {
        $page = max(0, intval($request->page ?? 0));
        $size = min(intval($request->size ?? 10), 50);


        $list = Zample::where('user_id', Auth::id())->orderBy('id', 'DESC')->offset($page * $size)->limit($size)->get();

        return Response::success($list);
    }

    public function getDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return Response::error(trans('app-return.validator_fails'), 1212);
        }
        $detail = Zample::where('id', $request->id)->first();
        if (!$detail) {
            return Response::error(trans('app-return.not_found'), 456346);
        }
        return Response::success($detail);
    }
}
