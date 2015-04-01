<?php namespace Xjchen\Yuntongxun\Models;

use Eloquent;
use Carbon\Carbon;

class SMSLog extends Eloquent {

    protected $table = 'yuntongxun_sms_logs';

    protected static $unguarded = true;

    protected $perPage = 10;

    public function scopeSendToday($query, $ip)
    {
        return $query->where('created_at', '>=', Carbon::today())
            ->where('client_ip', $ip);
    }
}
