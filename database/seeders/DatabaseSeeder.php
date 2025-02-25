<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Nonaktifkan foreign key checks sementara
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate semua table untuk memastikan data bersih
        \DB::table('mahasiswas')->truncate();
        \DB::table('dosens')->truncate();
        \DB::table('konsentrasi')->truncate();
        \DB::table('prodi')->truncate();
        \DB::table('role')->truncate();

        // Aktifkan kembali foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Jalankan seeders dalam urutan yang benar
        $this->call([
            RoleSeeder::class,      // Jalankan pertama karena dibutuhkan oleh user
            ProdiSeeder::class,     // Jalankan kedua karena dibutuhkan oleh user
            KonsentrasiSeeder::class, // Jalankan ketiga karena dibutuhkan oleh mahasiswa
            DosenSeeder::class,     // Opsional: Jika ingin menambahkan data dosen default
            MahasiswaSeeder::class  // Opsional: Jika ingin menambahkan data mahasiswa default
        ]);
    }
}
