<?php namespace Xjchen\Yuntongxun\Services;

use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Session\SessionManager;

class CaptchaService extends AbstractService implements ServiceInterface
{
    static $sessionPrefix = 'xjchen.yuntongxun.captcha';
    static $configPrefix = 'yuntongxun::captcha';

    protected $captcha;
    protected $width;
    protected $height;

    public function __construct(CaptchaBuilder $builder, $width, $height, SessionManager $session, $attemptLimit, $expireSeconds=120)
    {
        parent::__construct($session, $attemptLimit, $expireSeconds);
        $this->captcha = $builder;
        $this->width = $width;
        $this->height = $height;
    }

    public function generate($code = null, $width=null, $height=null)
    {
        if ($width) {
            $this->width = $width;
        }
        if ($height) {
            $this->height = $height;
        }
        if ($code) {
            $this->captcha->setPhrase($code);
        }
        $image = $this->captcha->build($this->width, $this->height);
        static::put($image->getPhrase());
        return $image;
    }

    protected function isEqual($a, $b)
    {
        if ($a == $b) {
            return true;
        } else {
            return false;
        }
    }

}
