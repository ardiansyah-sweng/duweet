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
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id(UserColumns::ID);
            $table->string(UserColumns::NAME);

            // Name components
            $table->string(UserColumns::FIRST_NAME)->nullable();
            $table->string(UserColumns::MIDDLE_NAME)->nullable();
            $table->string(UserColumns::LAST_NAME)->nullable();

            $table->string(UserColumns::EMAIL)->unique();

            // Address
            $table->string(UserColumns::PROVINSI);
            $table->string(UserColumns::KABUPATEN);
            $table->string(UserColumns::KECAMATAN);
            $table->string(UserColumns::JALAN);
            $table->string(UserColumns::KODE_POS);

            // Birth data (integer based)
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
        Schema::dropIfExists($this->table);
    }
};
