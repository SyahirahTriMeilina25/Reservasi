<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('usulan_bimbingans', function (Blueprint $table) {
            // Hapus foreign key lama
            $table->dropForeign('usulan_bimbingans_event_id_foreign');
            
            // Tambahkan foreign key baru dengan opsi ON DELETE SET NULL
            $table->foreign('event_id')
                  ->references('event_id')
                  ->on('jadwal_bimbingans')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('usulan_bimbingans', function (Blueprint $table) {
            // Hapus foreign key yang ditambahkan
            $table->dropForeign('usulan_bimbingans_event_id_foreign');
            
            // Kembalikan ke foreign key original
            $table->foreign('event_id')
                  ->references('event_id')
                  ->on('jadwal_bimbingans');
        });
    }
};