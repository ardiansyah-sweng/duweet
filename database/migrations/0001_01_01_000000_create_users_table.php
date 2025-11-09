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
        $this->table = config('db_tables.user');
    }

    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Identitas pengguna
            $table->string(UserColumns::NAME);
            $table->string(UserColumns::FIRST_NAME)->nullable();
            $table->string(UserColumns::MIDDLE_NAME)->nullable();
            $table->string(UserColumns::LAST_NAME)->nullable();

            // Kontak dan data pribadi
            $table->string(UserColumns::EMAIL)->unique();

            // Alamat (opsional, sesuai constants)
            $table->string(UserColumns::PROVINSI)->nullable();
            $table->string(UserColumns::KABUPATEN)->nullable();
            $table->string(UserColumns::KECAMATAN)->nullable();
            $table->string(UserColumns::JALAN)->nullable();
            $table->string(UserColumns::KODE_POS)->nullable();

            // Data lahir
            $table->integer(UserColumns::TANGGAL_LAHIR);
            $table->integer(UserColumns::BULAN_LAHIR);
            $table->integer(UserColumns::TAHUN_LAHIR);
            $table->integer(UserColumns::USIA)->nullable();

            // Autentikasi
            $table->string('password');
            $table->rememberToken();

            // Timestamp otomatis
            $table->timestamps();

            // Index untuk performa pencarian
            $table->index(UserColumns::EMAIL);
            $table->index([UserColumns::PROVINSI, UserColumns::KABUPATEN]);
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->table);
    }
};
