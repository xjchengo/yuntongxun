<?php namespace Xjchen\Yuntongxun\Services;

use Xjchen\Yuntongxun\PhraseBuilder;
use Xjchen\Yuntongxun\REST;
use Illuminate\Session\SessionManager;
use Xjchen\Yuntongxun\Models\SMSLog;
use Xjchen\Yuntongxun\SMSThrottlingException;

class SMSService extends AbstractService implements ServiceInterface
{
    static $sessionPrefix = 'xjchen.yuntongxun.sms';
    static $configPrefix = 'yuntongxun::sms';

    protected $sdk;
    protected $length;
    protected $charset;
    protected $ip;
    protected $log;
    public $throttling;

    public function __construct($throttling, SMSLog $log, REST $sdk, PhraseBuilder $phraseBuilder, SessionManager $session, $attemptLimit, $expireSeconds=120, $ip='0.0.0.0')
    {
        parent::__construct($session, $attemptLimit, $expireSeconds);
        $this->sdk = $sdk;
        $this->phraseBuilder = $phraseBuilder;
        $this->ip = $ip;
        $this->log = $log;
        $this->throttling = $throttling;
    }

    public function send($to, $data, $templateId, $codeKey=1)
    {
        $this->throttle();
        if (!isset($data[$codeKey])) {
            $phrase = $this->phraseBuilder->build();
            $data[$codeKey] = $phrase;
        }
        ksort($data, SORT_NUMERIC);
        $result = $this->sdk->sendTemplateSMS($to, $data, $templateId);
        static::put([
            'telephone' => $to,
            'sms' => $data[$codeKey]
        ]);
        $this->logSMS();
        return $result;
    }

    protected function isEqual($a, $b)
    {
        if (($a['telephone'] == $b['telephone']) and ($a['sms'] == $b['sms'])) {
            return true;
        } else {
            return false;
        }
    }

    protected function throttle()
    {
        if (isset($this->throttling['enabled']) and ($this->throttling['enabled']==false)) {
            return;
        }
        $limit = isset($this->throttling['send_limit'])?$this->throttling['send_limit']:10;
        if ($this->log->sendToday($this->ip)->count() >= $limit) {
            throw new SMSThrottlingException('已达到今日短信发送限制');
        }
    }

    protected function logSMS()
    {
        $this->log->create([
            'client_ip' => $this->ip,
            'to' => json_encode($this->sdk->getLastTo()),
            'data' => json_encode($this->sdk->getLastData()),
            'template_id' => $this->sdk->getLastTemplateId(),
            'message_sid' => $this->sdk->getLastSuccessResult()->TemplateSMS->smsMessageSid,
            'app_id' => $this->sdk->getAppId(),
            'server_ip' => $this->sdk->getServerIp()
        ]);
    }

}
