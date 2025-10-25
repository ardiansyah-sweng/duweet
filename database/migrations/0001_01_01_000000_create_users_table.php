<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Primary Key

            // Identitas pengguna
            $table->string('name');
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();

            // Kontak dan data pribadi
            $table->string('email')->unique();
            $table->integer('tanggal_lahir');
            $table->integer('bulan_lahir');
            $table->integer('tahun_lahir');
            $table->integer('usia')->nullable();

            // Autentikasi
            $table->string('password');
            $table->rememberToken();

            // Timestamps otomatis (created_at, updated_at)
            $table->timestamps();

        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
{
    Schema::disableForeignKeyConstraints();
    Schema::dropIfExists('users');
    Schema::enableForeignKeyConstraints();
}

};
