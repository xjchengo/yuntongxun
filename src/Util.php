<?php namespace Xjchen\Yuntongxun;

use ReflectionFunction;
use Exception;

class Util
{
    public static function generateUri($uri, $prefixUri)
    {
        if (is_string($prefixUri)) {
            return $prefixUri.$uri;
        } else {
            try {
                $reflection = new ReflectionFunction($prefixUri);
                if ($reflection->isClosure()) {
                    return $reflection->invoke($uri);
                }
            } catch (Exception $e) {}
            return $uri;
        }
    }

}
