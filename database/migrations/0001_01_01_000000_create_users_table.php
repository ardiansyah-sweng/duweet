<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\UserColumns;

return new class extends Migration
{
    protected string $table;

    public function __construct()
    {
        // table name from config with fallback
        $this->table = config('db_tables.user', 'users');
    }

    /**
     * Jalankan migration.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id(UserColumns::ID);
            $table->string(UserColumns::NAME);
            $table->string(UserColumns::FIRST_NAME)->nullable();
            $table->string(UserColumns::MIDDLE_NAME)->nullable();
            $table->string(UserColumns::LAST_NAME)->nullable();
            $table->string(UserColumns::EMAIL)->unique();

            // Address data
            $table->string('provinsi')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('jalan')->nullable();
            $table->string('kode_pos')->nullable();

            // Birth data
            $table->integer(UserColumns::TANGGAL_LAHIR)->nullable();
            $table->integer(UserColumns::BULAN_LAHIR)->nullable();
            $table->integer(UserColumns::TAHUN_LAHIR)->nullable();
            $table->integer(UserColumns::USIA)->nullable();

            // Authentication
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
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