<?php

use App\Constants\UserColumns;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(UserColumns::ID);
            $table->string(UserColumns::NAME)->nullable();
            $table->string(UserColumns::FIRST_NAME);
            $table->string(UserColumns::MIDDLE_NAME)->nullable();
            $table->string(UserColumns::LAST_NAME);
            $table->string(UserColumns::EMAIL)->unique();
            $table->string(UserColumns::PROVINSI)->nullable();
            $table->string(UserColumns::KABUPATEN)->nullable();
            $table->string(UserColumns::KECAMATAN)->nullable();
            $table->text(UserColumns::JALAN)->nullable();
            $table->string(UserColumns::KODE_POS)->nullable();
            $table->date(UserColumns::TANGGAL_LAHIR)->nullable();
            $table->integer(UserColumns::BULAN_LAHIR)->nullable();
            $table->integer(UserColumns::TAHUN_LAHIR)->nullable();
            $table->integer(UserColumns::USIA)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};