<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use M1guelpf\Web3Login\Facades\Signature;

/**
 * Description of My
 *
 * @author Administrator
 */
class Web3LoginService
{
    public static function checkSignature(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => ['required', 'string'],
            'signature' => ['required', 'string'],
            'nonce' => ['required', 'string'],
        ], [
            'address.required' => 'Address is required.',
            'address.string' => 'Address must be a string.',
            'signature.required' => 'Signature is required.',
            'signature.string' => 'Signature must be a string.',
            'nonce.required' => 'Nonce is required.',
            'nonce.string' => 'Nonce must be a string.',
        ]);
        if ($validator->fails()) {
            throw new Exception($validator->errors()->first(), 1235);
        }

        $nonce = $request->nonce;

        $nonce = Cache::get('nonce:' . $nonce);
        if (!$nonce) {
            throw new Exception(trans('app-return.web3.nonce'), 1235);
        }
        Cache::forget('nonce:' . $nonce);

        if (!Signature::verify($nonce, $request->input('signature'), $request->input('address'))) {
            throw new Exception(trans('app-return.web3.signature'), 1235);
        }
    }
}
