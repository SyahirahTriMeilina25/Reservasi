<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AllowNullEventIdInUsulanBimbingans extends Migration
{
    public function up()
    {
        Schema::table('usulan_bimbingans', function (Blueprint $table) {
            $table->string('event_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('usulan_bimbingans', function (Blueprint $table) {
            $table->string('event_id')->nullable(false)->change();
        });
    }
}