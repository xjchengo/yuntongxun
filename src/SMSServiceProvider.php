<?php namespace Xjchen\Yuntongxun;

use Illuminate\Support\ServiceProvider;
use Gregwar\Captcha\CaptchaBuilder;
use Xjchen\Yuntongxun\Services\CaptchaService;
use Xjchen\Yuntongxun\Services\SMSService;

class SMSServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Booting
     */
    public function boot()
    {
        $this->package('xjchen/yuntongxun', null, __DIR__);
        include __DIR__ . '/routes.php';
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCaptcha();
        $this->registerCaptchaService();
        $this->registerSMS();
        $this->registerSMSService();
    }

    protected function registerCaptcha()
    {
        $this->app->bind('xjchen.yuntongxun.captcha', function ($app, $params) {
            $phraseBuilder = new PhraseBuilder(
                $app['config']->get('yuntongxun::captcha.length'),
                $app['config']->get('yuntongxun::captcha.charset')
            );
            $builder = new CaptchaBuilder(
                isset($params['phrase']) ? $params['phrase'] : null,
                $phraseBuilder
            );
            $builder->setDistortion($app['config']->get('yuntongxun::captcha.distortion', true));
            if ($app['config']->has('yuntongxun::captcha.background_color.red')) {
                $builder->setBackgroundColor(
                    $app['config']->get('yuntongxun::captcha.background_color.red', 255),
                    $app['config']->get('yuntongxun::captcha.background_color.green', 255),
                    $app['config']->get('yuntongxun::captcha.background_color.blue', 255)
                );
            }
            if ($app['config']->has('yuntongxun::captcha.max_bebind_lines')) {
                $builder->setMaxBehindLines($app['config']->get('yuntongxun::captcha.max_bebind_lines', 1));
            }
            if ($app['config']->has('yuntongxun::captcha.max_front_lines')) {
                $builder->setMaxBehindLines($app['config']->get('yuntongxun::captcha.max_front_lines', 1));
            }
            return $builder;
        });
    }

    protected function registerCaptchaService()
    {
        $this->app->bind('xjchen.yuntongxun.captcha-service', function ($app) {

            return new CaptchaService(
                $app['xjchen.yuntongxun.captcha'],
                $app['config']->get('yuntongxun::captcha.width'),
                $app['config']->get('yuntongxun::captcha.height'),
                $app['session'],
                $app['config']->get('yuntongxun::captcha.attempt_limit'),
                $app['config']->get('yuntongxun::captcha.expire_seconds')
            );
        });
    }

    protected function registerSMS()
    {
        $this->app['xjchen.yuntongxun.sms'] = $this->app->share(function ($app) {
            $accountSid = $app['config']->get('yuntongxun::ronglian.account_sid');
            $accountToken = $app['config']->get('yuntongxun::ronglian.account_token');
            $appId = $app['config']->get('yuntongxun::ronglian.app_id');
            $serverIP = $app['config']->get('yuntongxun::ronglian.server_ip');
            $serverPort = $app['config']->get('yuntongxun::ronglian.server_port');
            $softVersion = $app['config']->get('yuntongxun::ronglian.soft_version');
            $enableLog = $app['config']->get('yuntongxun::ronglian.enable_log');
            $rest = new REST($serverIP, $serverPort, $softVersion, $enableLog);
            $rest->setAccount($accountSid, $accountToken);
            $rest->setAppId($appId);
            return $rest;
        });
    }

    protected function registerSMSService()
    {
        $this->app['xjchen.yuntongxun.sms-service'] = $this->app->share(function ($app) {
            $phraseBuilder = new PhraseBuilder(
                $app['config']->get('yuntongxun::sms.length'),
                $app['config']->get('yuntongxun::sms.charset')
            );
            return new SMSService(
                $app['xjchen.yuntongxun.sms'],
                $phraseBuilder,
                $app['session'],
                $app['config']->get('yuntongxun::sms.attempt_limit'),
                $app['config']->get('yuntongxun::sms.expire_seconds')
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'xjchen.yuntongxun.captcha',
            'xjchen.yuntongxun.captcha-service',
            'xjchen.yuntongxun.sms',
            'xjchen.yuntongxun.sms-service'
        ];
    }

}
