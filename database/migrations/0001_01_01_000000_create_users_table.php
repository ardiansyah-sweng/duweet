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
        // Use configured table name with fallback
        $this->table = config('db_tables.user', 'users');
    }

    /**
     * Jalankan migration.
     */
    public function up(): void
    {
                Schema::create($this->table, function (Blueprint $table) {
                        // Prefer constants when available; fallback to common column names
                        $table->id(UserColumns::ID ?? 'id');

                        $table->string(UserColumns::NAME ?? 'name', 100);
                        $table->string(UserColumns::FIRST_NAME ?? 'first_name')->nullable();
                        $table->string(UserColumns::MIDDLE_NAME ?? 'middle_name')->nullable();
                        $table->string(UserColumns::LAST_NAME ?? 'last_name')->nullable();
                        $table->string(UserColumns::EMAIL ?? 'email')->unique();

                        // Optional address fields (nullable to remain compatible)
                        $table->string('provinsi')->nullable();
                        $table->string('kabupaten')->nullable();
                        $table->string('kecamatan')->nullable();
                        $table->string('jalan')->nullable();
                        $table->string('kode_pos')->nullable();

                        // Birth data
                        $table->date(UserColumns::TANGGAL_LAHIR ?? 'tanggal_lahir')->nullable();
                        $table->unsignedTinyInteger(UserColumns::BULAN_LAHIR ?? 'bulan_lahir')->nullable();
                        $table->integer(UserColumns::TAHUN_LAHIR ?? 'tahun_lahir')->nullable();
                        $table->unsignedTinyInteger(UserColumns::USIA ?? 'usia')->nullable();

                        // Common Laravel columns
                        $table->timestamp(UserColumns::EMAIL_VERIFIED ?? 'email_verified_at')->nullable();
                        $table->string(UserColumns::PASSWORD ?? 'password')->nullable();
                        $table->rememberToken();
                        $table->timestamps();

                        // Index for name parts
                        $table->index([UserColumns::LAST_NAME ?? 'last_name', UserColumns::FIRST_NAME ?? 'first_name']);
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