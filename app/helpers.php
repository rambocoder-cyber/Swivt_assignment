<?php

use Illuminate\Support\Arr;

if (!function_exists('validateHash')) {
    function verifyHash($request)
    {
        $request_data = Arr::except($request, 'hash');
        ksort($request_data);
        $rawSignatureString = http_build_query($request_data) . config('app.secret_key');
        $calculatedHash = md5($rawSignatureString);
        return $calculatedHash == $request['hash'];
    }
}