<?php namespace Xjchen\Yuntongxun\Services;

interface ServiceInterface
{
    public function put($code);

    public function get();

    public function check($userInput);

    public function forget();

    public function useIt($userInput);

    public function getErrorCode();

    public static function isEqual($a, $b);

}
