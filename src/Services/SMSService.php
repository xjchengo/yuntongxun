<?php namespace Xjchen\Yuntongxun\Services;

use Xjchen\Yuntongxun\PhraseBuilder;
use Xjchen\Yuntongxun\REST;
use Illuminate\Session\SessionManager;

class SMSService extends AbstractService implements ServiceInterface
{
    static $sessionPrefix = 'xjchen.yuntongxun.sms';
    static $configPrefix = 'yuntongxun::sms';

    protected $sdk;
    protected $length;
    protected $charset;

    public function __construct(REST $sdk, PhraseBuilder $phraseBuilder, SessionManager $session, $attemptLimit, $expireSeconds=120)
    {
        parent::__construct($session, $attemptLimit, $expireSeconds);
        $this->sdk = $sdk;
        $this->phraseBuilder = $phraseBuilder;
    }

    public function send($to, $data, $templateId, $codeKey=1)
    {
        if (!isset($data[$codeKey])) {
            $phrase = $this->phraseBuilder->build();
            $data[$codeKey] = $phrase;
        }
        ksort($data, SORT_NUMERIC);
        $result = $this->sdk->sendTemplateSMS($to, $data, $templateId);
        self::put([
            'telephone' => $to,
            'sms' => $data[$codeKey]
        ]);
        return $result;
    }

    public static function isEqual($a, $b)
    {
        if (($a['telephone'] == $b['telephone']) and ($a['sms'] == $b['sms'])) {
            return true;
        } else {
            return false;
        }
    }

}
