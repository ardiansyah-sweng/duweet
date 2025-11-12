<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected string $table;

    public function __construct()
    {
        // gunakan config dengan default 'users'
        $this->table = config('db_tables.user', 'users');
    }

    /**
     * Jalankan migration.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();

            // Identitas
            $table->string('name', 100);
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();

            // Alamat
            $table->string('provinsi')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('jalan')->nullable();
            $table->string('kode_pos', 10)->nullable();

            // Data lahir
            $table->unsignedTinyInteger('tanggal_lahir')->nullable(); // 1–31
            $table->unsignedTinyInteger('bulan_lahir')->nullable();   // 1–12
            $table->unsignedSmallInteger('tahun_lahir')->nullable();  // contoh: 1900–2100
            $table->unsignedTinyInteger('usia')->nullable();          // 0–255

            // Laravel defaults
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();

            // Index opsional
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
