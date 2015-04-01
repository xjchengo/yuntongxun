<?php namespace Xjchen\Yuntongxun\Models;

use Eloquent;

class SMSLog extends Eloquent {

    protected $table = 'yuntongxun_sms_logs';

    protected static $unguarded = true;

    protected $perPage = 10;

}
