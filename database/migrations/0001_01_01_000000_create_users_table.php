<?php

use App\Constants\UserColumns;
use Illuminate\Support\Facades\Schema;
<<<<<<< HEAD
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
=======
use App\Constants\UserColumns;
>>>>>>> f69f6a334e79a0c91b6090aee470fb63b59926ce

return new class extends Migration
{
    protected string $table;

<<<<<<< HEAD
    public function __construct(){

    $this->table = config('db_tables.user');
    }
=======
    public function __construct()
    {
        $this->table = config('db_tables.user');
    }

>>>>>>> f69f6a334e79a0c91b6090aee470fb63b59926ce
    /**
     * Run the migrations.
     */
    public function up(): void
    {
<<<<<<< HEAD
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string(UserColumns::NAME);
            $table->string(UserColumns::FIRST_NAME);
            $table->string(UserColumns::MIDDLE_NAME)->nullable();
            $table->string(UserColumns::LAST_NAME);
            $table->string(UserColumns::EMAIL)->unique();
            $table->date(UserColumns::TANGGAL_LAHIR)->nullable();
            $table->enum(UserColumns::JENIS_KELAMIN, ['L', 'P'])->nullable();
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
=======
        Schema::create($this->table, function (Blueprint $table) {
            $table->id(UserColumns::ID);
            $table->string(UserColumns::NAME);
            $table->string(UserColumns::FIRST_NAME)->nullable();
            $table->string(UserColumns::MIDDLE_NAME)->nullable();
            $table->string(UserColumns::LAST_NAME)->nullable();
            $table->string(UserColumns::EMAIL)->unique();
            
            // Address data
            $table->string(UserColumns::PROVINSI);
            $table->string(UserColumns::KABUPATEN);
            $table->string(UserColumns::KECAMATAN);
            $table->string(UserColumns::JALAN);
            $table->string(UserColumns::KODE_POS);
            
            // Birth data
            $table->integer(UserColumns::TANGGAL_LAHIR);
            $table->integer(UserColumns::BULAN_LAHIR);
            $table->integer(UserColumns::TAHUN_LAHIR);
            $table->integer(UserColumns::USIA);
        });

>>>>>>> f69f6a334e79a0c91b6090aee470fb63b59926ce
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};