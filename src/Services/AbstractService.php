<?php namespace Xjchen\Yuntongxun\Services;

use Illuminate\Session\SessionManager;

abstract class AbstractService
{
    static $sessionPrefix = 'xjchen.yuntongxun.abstract';
    static $configPrefix = 'yuntongxun::abstract';

    protected $session;
    protected $expireSeconds;
    protected $attemptLimit;

    protected $errorCode = 0;

    public function __construct(SessionManager $session, $attemptLimit, $expireSeconds=120)
    {
        $this->session = $session;
        $this->expireSeconds = $expireSeconds;
        $this->attemptLimit = $attemptLimit;
    }

    public function put($code)
    {
        $timeNow = time();
        $captcha = [
            'code' => $code,
            'created_time' => $timeNow,
            'expired_time' => $timeNow+$this->expireSeconds,
            'current_attempt' => 0,
            'max_attempt' => $this->attemptLimit
        ];
        $this->session->put(static::$sessionPrefix, $captcha);
    }

    public function get()
    {
        $code = $this->session->get(static::$sessionPrefix.'.code');
        $expiredTime = $this->session->get(static::$sessionPrefix.'.expired_time');
        if (!$code or !$expiredTime) {
            $this->errorCode = 2;
            return false;
        } else {
            $timeNow = time();
            if ($timeNow > $expiredTime) {
                static::forget();
                $this->errorCode = 3;
                return false;
            }
            $currentAttempt = $this->session->get(static::$sessionPrefix.'.current_attempt');
            $maxAttempt = $this->session->get(static::$sessionPrefix.'.max_attempt');
            if ($currentAttempt >= $maxAttempt) {
                static::forget();
                $this->errorCode = 4;
                return false;
            }
            return $code;
        }
    }

    public function check($userInput)
    {
        $code = static::get();
        if (!$userInput) {
            $this->errorCode = 1;
            return false;
        } elseif (!$code) {
            // 在get方法中已设置errorCode
            return false;
        } else {
            $currentAttempt = $this->session->get(static::$sessionPrefix.'.current_attempt');
            $this->session->put(static::$sessionPrefix.'.current_attempt', $currentAttempt+1);
            if (!static::isEqual($code, $userInput)) {
                $this->errorCode = 5;
                return false;
            }
            return true;
        }
    }

    public function forget()
    {
        $this->session->forget(static::$sessionPrefix);
    }

    public function useIt($userInput)
    {
        if (!static::check($userInput)) {
            $result = false;
        } else {
            $result = true;
        }
        static::forget();
        return $result;
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    abstract protected function isEqual($a, $a);

}
