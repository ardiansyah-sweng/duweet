<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\UserColumns;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         Schema::create('users', function (Blueprint $table) {
            $table->id(); // UserColumns::ID
            $table->string(UserColumns::NAME);
            $table->string(UserColumns::FIRST_NAME)->nullable();
            $table->string(UserColumns::MIDDLE_NAME)->nullable();
            $table->string(UserColumns::LAST_NAME)->nullable();
            $table->string(UserColumns::EMAIL)->unique();

            $table->string(UserColumns::PROVINSI)->nullable();
            $table->string(UserColumns::KABUPATEN)->nullable();
            $table->string(UserColumns::KECAMATAN)->nullable();
            $table->string(UserColumns::JALAN)->nullable();
            $table->string(UserColumns::KODE_POS)->nullable();

            $table->integer(UserColumns::TANGGAL_LAHIR);
            $table->integer(UserColumns::BULAN_LAHIR);
            $table->integer(UserColumns::TAHUN_LAHIR);
            $table->integer(UserColumns::USIA);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
