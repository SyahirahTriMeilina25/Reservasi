<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class MahasiswaSeeder extends Seeder
{
    public function run()
    {
        // SEEDER INI KOSONG - DATA MAHASISWA AKAN DITAMBAHKAN MELALUI ADMIN PANEL
        $this->command->info('MahasiswaSeeder: Data mahasiswa akan ditambahkan melalui admin panel');
        
        // Jika memang diperlukan untuk testing, uncomment dan modifikasi sesuai kebutuhan:
        /*
        $mahasiswa = [
            // Tambahkan data mahasiswa di sini jika diperlukan
        ];

        DB::table('mahasiswas')->insert($mahasiswa);
        */
    }
}