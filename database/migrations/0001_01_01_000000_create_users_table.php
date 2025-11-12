<?php

use App\Constants\UserColumns;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    protected string $table;

    public function __construct()
    {
        $this->table = config('db_tables.user', 'users');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id(UserColumns::ID);
            $table->string(UserColumns::NAME)->nullable();
            $table->string(UserColumns::FIRST_NAME)->nullable();
            $table->string(UserColumns::MIDDLE_NAME)->nullable();
            $table->string(UserColumns::LAST_NAME)->nullable();
            $table->string(UserColumns::EMAIL)->unique();
            
            // Address data
            $table->string(UserColumns::PROVINSI)->nullable();
            $table->string(UserColumns::KABUPATEN)->nullable();
            $table->string(UserColumns::KECAMATAN)->nullable();
            $table->text(UserColumns::JALAN)->nullable();
            $table->string(UserColumns::KODE_POS)->nullable();
            
            // Birth data
            $table->date(UserColumns::TANGGAL_LAHIR)->nullable();
            $table->integer(UserColumns::BULAN_LAHIR)->nullable();
            $table->integer(UserColumns::TAHUN_LAHIR)->nullable();
            $table->integer(UserColumns::USIA)->nullable();
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
        Schema::dropIfExists($this->table);
        Schema::dropIfExists('sessions');
    }
};