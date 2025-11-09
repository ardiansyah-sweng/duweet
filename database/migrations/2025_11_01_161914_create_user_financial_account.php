<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function __construct(){
        $this->table = config('db_tables.user_financial_account');
    }

     /**
     * Run the migrations.
     */

    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')
                  ->constrained('users')
                  ->onDelete('cascade');
            
            $table->foreignId('financial_account_id')
                  ->constrained('financial_accounts')
                  ->onDelete('cascade');

            $table->integer('balance')->default(0);
            $table->integer('initial_balance')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Untuk mencegah duplikasi relasi user ↔ akun finansial
            $table->unique(['id_user', 'financial_account_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_financial_accounts');
    }
};
