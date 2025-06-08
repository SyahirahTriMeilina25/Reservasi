<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DosenSeeder extends Seeder
{
    public function run()
    {
        // SEEDER INI KOSONG - DATA DOSEN AKAN DITAMBAHKAN MELALUI ADMIN PANEL
        $this->command->info('DosenSeeder: Data dosen akan ditambahkan melalui admin panel');
        
        // Jika memang diperlukan untuk testing, uncomment dan modifikasi sesuai kebutuhan:
        /*
        $koordinatorProdiRoleId = DB::table('role')
            ->where('role_akses', 'koordinator_prodi')
            ->value('id');

        if (!$koordinatorProdiRoleId) {
            $koordinatorProdiRoleId = DB::table('role')->insertGetId([
                'role_akses' => 'koordinator_prodi',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
        
        $dosen = [
            // Tambahkan data dosen di sini jika diperlukan
        ];

        DB::table('dosens')->insert($dosen);
        */
    }
}