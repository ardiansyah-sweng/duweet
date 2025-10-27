<?php

use App\Constants\UserAccountColumns;
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
        Schema::create('user_accounts', function (Blueprint $table) {
            $table->bigIncrements(UserAccountColumns::ID_USERACCOUNT);
            
            // Relasi ke tabel 'users'
            $table->foreignId('id_user')
                  ->constrained('users') 
                  ->onDelete('cascade'); 

             $table->string(UserAccountColumns::USERNAME)->unique();
            $table->string(UserAccountColumns::PASSWORD);
            $table->string(UserAccountColumns::STATUS)->default('active');

            $table->timestamp(UserAccountColumns::TANGGAL_DAFTAR)
                  ->nullable()
                  ->useCurrent();

            $table->timestamp(UserAccountColumns::TANGGAL_UPDATE)
                  ->nullable()
                  ->useCurrentOnUpdate();

            $table->timestamp(UserAccountColumns::TANGGAL_HAPUS)
                  ->nullable()
                  ->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('user_accounts');
        Schema::enableForeignKeyConstraints();
    }
};
