<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create
         ('user_account_totals', function (Blueprint $table) 
        {
            $table->id();

            
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('account_id');

            
            $table->decimal('total_balance', 15, 2)
                  ->default(0);
            $table->decimal('initial_balance', 15, 2)
                  ->default(0);

            $table->timestamps();

            
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            $table->foreign('account_id')
                  ->references('id')
                  ->on('accounts')
                  ->onDelete('cascade');
    
        });
    }

        public function down(): void
            {
                Schema::dropIfExists('user_account_totals');
            }
};

