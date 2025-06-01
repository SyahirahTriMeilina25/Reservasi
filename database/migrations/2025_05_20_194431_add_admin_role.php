<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        // Periksa jika role admin sudah ada
        $adminExists = DB::table('role')->where('role_akses', 'admin')->exists();
        
        if (!$adminExists) {
            // Dapatkan ID tertinggi
            $maxId = DB::table('role')->max('id');
            
            // Tambahkan role admin
            DB::table('role')->insert([
                'id' => $maxId + 1,
                'role_akses' => 'admin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
    }

    public function down(): void
    {
        DB::table('role')->where('role_akses', 'admin')->delete();
    }
};