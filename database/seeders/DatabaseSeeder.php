<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Nonaktifkan foreign key checks sementara
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate table, tapi tidak termasuk mahasiswa dan dosen
        \DB::table('role')->truncate();
        \DB::table('admin')->truncate();
        
        // Aktifkan kembali foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Jalankan seeder yang diperlukan saja
        $this->call([
            RoleSeeder::class,
            KoordinatorProdiRoleSeeder::class,
            ProdiSeeder::class,
            KonsentrasiSeeder::class,
            // DosenSeeder::class, -- dihapus/di-comment
            // MahasiswaSeeder::class, -- dihapus/di-comment
            AdminSeeder::class,
        ]);
    }
}