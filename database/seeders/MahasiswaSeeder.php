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
        $mahasiswa = [
            [
                'nim' => '2107112735',
                'nama' => 'Tri Murniati',
                'angkatan' => 2021,
                'email' => 'tri.murniati2735@student.unri.ac.id',
                'password' => Hash::make('bismillah123'),
                'prodi_id' => 2,
                'konsentrasi_id' => 1, 
                'role_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'nim' => '2107110665',
                'nama' => 'Desi Maya Sari',
                'angkatan' => 2021,
                'email' => 'desi.maya0665@student.unri.ac.id',
                'password' => Hash::make('2107110665'),
                'prodi_id' => 2,
                'konsentrasi_id' => 1, 
                'role_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'nim' => '2107110255',
                'nama' => 'Syahirah Tri Meilina',
                'angkatan' => 2021,
                'email' => 'syahirah.tri0255@student.unri.ac.id',
                'password' => Hash::make('2107110255'),
                'prodi_id' => 2,
                'konsentrasi_id' => 1, 
                'role_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ];

        DB::table('mahasiswas')->insert($mahasiswa);
    }
}