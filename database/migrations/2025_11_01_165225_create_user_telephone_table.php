<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\UserTelephoneColumns as Columns;

return new class extends Migration
{
    protected string $table;

    public function __construct(){
        $this->table = config('db_tables.user_telephone', 'user_telephones');
    }

    /**
     * Jalankan migration: membuat tabel user_telephones.
     */
    public function up(): void
    {
        Schema::create(config('db_tables.user_telephone', 'user_telephones'), function (Blueprint $table) {
            $table->id(Columns::ID); // Primary Key
            $table->unsignedBigInteger(Columns::USER_ID); // Foreign Key ke users.id
            $table->string(Columns::NUMBER)->nullable(); // Nomor telepon (boleh null)
            $table->timestamps();

            // Relasi ke tabel users
            $table->foreign(Columns::USER_ID)
                  ->references('id')
                  ->on(config('db_tables.users', 'users'))
                  ->onDelete('cascade'); // Jika user dihapus, telepon ikut terhapus
        });

        Schema::table($this->table, function (Blueprint $table) {
            $table->index(Columns::NUMBER, 'idx_user_telephone_number');
            $table->index(Columns::USER_ID, 'idx_user_telephone_user_id');
        });
    }

    /**
     * Membatalkan migration (rollback)
     */
    public function down(): void
    {
        Schema::dropIfExists($this->table);
    }
};
