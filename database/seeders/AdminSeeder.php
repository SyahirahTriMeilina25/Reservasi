<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Tetapkan password admin yang kuat tapi konsisten
        $adminPassword = 'Admin_Secure_2025!';
        
        // Dapatkan ID role admin
        $adminRoleId = DB::table('role')
            ->where('role_akses', 'admin')
            ->value('id');
            
        if (!$adminRoleId) {
            // Jika role admin belum ada, buat dulu
            $adminRoleId = DB::table('role')->insertGetId([
                'role_akses' => 'admin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            
            $this->command->info('Role admin dibuat dengan ID: ' . $adminRoleId);
        }
        
        // Cek apakah admin sudah ada
        $adminExists = DB::table('admin')
            ->where('username', 'admin')
            ->exists();
            
        if (!$adminExists) {
            // Tambahkan user admin default
            DB::table('admin')->insert([
                'username' => 'admin',
                'nama' => 'Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make($adminPassword),
                'role_id' => $adminRoleId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            
            $this->command->info('Admin user created successfully');
            $this->command->info('----------------------------------------');
            $this->command->info('KREDENSIAL LOGIN ADMIN:');
            $this->command->info('Username: admin');
            $this->command->info('Password: ' . $adminPassword);
            $this->command->info('----------------------------------------');
            $this->command->info('Simpan informasi ini untuk login!');
        } else {
            // Update password untuk admin yang sudah ada
            DB::table('admin')
                ->where('username', 'admin')
                ->update([
                    'password' => Hash::make($adminPassword),
                    'updated_at' => Carbon::now()
                ]);
                
            $this->command->info('Admin user already exists, password updated');
            $this->command->info('----------------------------------------');
            $this->command->info('KREDENSIAL LOGIN ADMIN BARU:');
            $this->command->info('Username: admin');
            $this->command->info('Password: ' . $adminPassword);
            $this->command->info('----------------------------------------');
        }
    }
}