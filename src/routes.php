<?php

use Xjchen\Yuntongxun\Util;

$prefixUri = Config::get('yuntongxun::uri_prefix');

Route::get(Util::generateUri('captcha', $prefixUri), [
    'as' => 'xjchen.yuntongxun.captcha.refresh',
    function(){
        $image = Captcha::generate();
        return Response::make($image->get())->header('Content-Type', 'image/jpg');
    }
]);

Route::post(Util::generateUri('captcha-check', $prefixUri), [
    'as' => 'xjchen.yuntongxun.captcha.check',
    function(){
        $userInput = Input::get('captcha');
        if (Captcha::check($userInput)) {
            return Response::json(true);
        } else {
            return Response::json(false);
        }
    }
]);

Route::post(Util::generateUri('send-sms', $prefixUri), [
    'as' => 'xjchen.yuntongxun.sms.send',
    function(){
        $telephone = Input::get('telephone');
        $captcha = Input::get('captcha');
        if (Captcha::useIt($captcha)) {
            $data = [
                2 => (Config::get('yuntongxun::sms.expire_seconds')/60)
            ];
            try {
                SMS::send($telephone, $data, Config::get('yuntongxun::sms.default_template_id'));
                $response = [
                    'status' => 0,
                    'msg' => '发送成功'
                ];
            } catch (Exception $e) {
                Log::error($e);
                $response = [
                    'status' => 1,
                    'msg' => $e->getMessage()
                ];
            }
        } else {
            $response = [
                'status' => 1,
                'msg' => '验证码错误'
            ];
        }
        return $response;
    }
]);

if (Config::get('app.debug')) {

    Route::get(Util::generateUri('demo', $prefixUri), [
        'as' => 'xjchen.yuntongxun.demo',
        function(){
            return View::make('yuntongxun::demo');
        }
    ]);

    Route::post(Util::generateUri('sms-check', $prefixUri), [
        'as' => 'xjchen.yuntongxun.sms.check',
        function(){
            $userInput = Input::all();
            if (SMS::useIt($userInput)) {
                $response = [
                    'status' => 0,
                    'msg' => '手机验证码验证成功'
                ];
            } else {
                $response = [
                    'status' => 1,
                    'msg' => '手机验证码验证失败'
                ];
            }
            return $response;
        }
    ]);
}

