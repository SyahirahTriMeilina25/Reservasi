<?php

// php artisan make:migration create_user_photos_table

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_photos', function (Blueprint $table) {
            $table->id();
            $table->string('user_id'); // bisa nim, nip, atau admin_id
            $table->enum('user_type', ['mahasiswa', 'dosen', 'admin']);
            $table->longText('foto_base64'); // untuk menyimpan base64
            $table->string('original_name')->nullable(); // nama file asli
            $table->string('mime_type')->default('image/jpeg'); // jenis file
            $table->integer('file_size')->nullable(); // ukuran file dalam bytes
            $table->timestamps();
            
            // Index untuk performa
            $table->unique(['user_id', 'user_type']);
            $table->index(['user_type', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_photos');
    }
};