<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateYuntongxunSmsLogsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yuntongxun_sms_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('client_ip');
            $table->string('to');
            $table->string('data');
            $table->string('template_id');
            $table->string('message_sid');
            $table->string('app_id');
            $table->string('server_ip');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('yuntongxun_sms_logs');
    }

}
