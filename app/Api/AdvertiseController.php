<?php

namespace App\Api;

use Illuminate\Support\Facades\Validator;
use App\Models\Advertise;
use App\Support\Response;
use Illuminate\Http\Request;

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
class AdvertiseController extends Controller
{

    public function getList(Request $request)
    {
        $params = $request->all();
        $validator = Validator::make($request->all(), [
            'position' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(array('res_code' => 1212, 'res_msg' => trans('app-return.validator_fails'), 'data' => []));
        }


        $list = Advertise::where('position', $params['position'])
            ->where('status', 1)
            ->orderBy("id", 'DESC')->get();


        return Response::success($list);
    }
}
