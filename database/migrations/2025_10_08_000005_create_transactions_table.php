<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\TransactionColumns;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {

            $table->id(TransactionColumns::ID);

            $table->unsignedBigInteger(TransactionColumns::ACCOUNT_ID);
            $table->foreign(TransactionColumns::ACCOUNT_ID)
                  ->references('id')
                  ->on('accounts')
                  ->onDelete('cascade');

            $table->unsignedBigInteger(TransactionColumns::USER_ID);
            $table->foreign(TransactionColumns::USER_ID)
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->bigInteger(TransactionColumns::AMOUNT);

            $table->text(TransactionColumns::DESCRIPTION)
                  ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
