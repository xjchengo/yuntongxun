<?php namespace Xjchen\Yuntongxun\Facades;

use Illuminate\Support\Facades\Facade;

class Captcha extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'xjchen.yuntongxun.captcha-service'; }

}
