<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected string $table;

    public function __construct()
    {
        // sesuai PRD → tabel utama untuk pengguna
        $this->table = config('db_tables.user', 'users');
    }

    /**
     * Jalankan migration.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();

            // Kolom sesuai PRD
            $table->string('name', 100);
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();

            // Data lahir sesuai PRD
            $table->unsignedTinyInteger('tanggal_lahir');
            $table->unsignedTinyInteger('bulan_lahir');
            $table->unsignedSmallInteger('tahun_lahir');
            $table->unsignedTinyInteger('usia');

            // Tambahan umum Laravel
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();

            // Index tambahan (opsional)
            $table->index(['last_name', 'first_name']);
        });
    }

    /**
     * Rollback migration.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->table);
    }
};
