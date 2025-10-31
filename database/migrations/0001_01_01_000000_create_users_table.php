
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
            $table->id();
            $table->string(UserColumns::NAME);
            $table->string(UserColumns::FIRST_NAME);
            $table->string(UserColumns::MIDDLE_NAME)->nullable();
            $table->string(UserColumns::LAST_NAME);
            $table->string(UserColumns::EMAIL)->unique();
            $table->date(UserColumns::TANGGAL_LAHIR)->nullable();
            $table->string(UserColumns::PROVINSI)->nullable();
            $table->string(UserColumns::KABUPATEN)->nullable();
            $table->string(UserColumns::KECAMATAN)->nullable();
            $table->string(UserColumns::JALAN)->nullable();
            $table->string(UserColumns::KODE_POS)->nullable();
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
