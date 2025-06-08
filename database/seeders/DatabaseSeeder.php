<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Nonaktifkan foreign key checks sementara
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate table
        \DB::table('role')->truncate();
        \DB::table('admin')->truncate();
        \DB::table('prodi')->truncate();
        \DB::table('konsentrasi')->truncate();
        
        // Aktifkan kembali foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Jalankan seeder yang diperlukan saja (TANPA DATA DUMMY)
        $this->call([
            RoleSeeder::class,
            KoordinatorProdiRoleSeeder::class,
            ProdiSeeder::class,
            KonsentrasiSeeder::class,
            AdminSeeder::class,
        ]);
        
        $this->command->info('Database seeding completed');
        $this->command->info('Admin user ready for login');
        $this->command->info('Dosen & Mahasiswa: Tambahkan melalui admin panel');
    }
}