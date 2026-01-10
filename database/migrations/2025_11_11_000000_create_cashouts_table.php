<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\CashoutColumns;

return new class extends Migration
{
    protected string $table;

    public function __construct()
    {
        $this->table = config('db_tables.cashout') ?? 'cashouts';
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();

            // Foreign key to user_accounts
            $table->foreignId(CashoutColumns::USER_ACCOUNT_ID)
                ->constrained('user_accounts')
                ->onDelete('cascade');

            // Cashout details
            $table->bigInteger(CashoutColumns::AMOUNT); // Amount in smallest currency unit
            $table->enum(CashoutColumns::STATUS, [
                CashoutColumns::STATUS_PENDING,
                CashoutColumns::STATUS_APPROVED,
                CashoutColumns::STATUS_REJECTED,
                CashoutColumns::STATUS_COMPLETED,
            ])->default(CashoutColumns::STATUS_PENDING);

            // Dates
            $table->dateTime(CashoutColumns::REQUEST_DATE);
            $table->dateTime(CashoutColumns::APPROVAL_DATE)->nullable();
            $table->dateTime(CashoutColumns::COMPLETION_DATE)->nullable();

            // Additional info
            $table->text(CashoutColumns::DESCRIPTION)->nullable();
            $table->text(CashoutColumns::NOTES)->nullable();

            // Payment information
            $table->enum(CashoutColumns::PAYMENT_METHOD, [
                CashoutColumns::METHOD_BANK_TRANSFER,
                CashoutColumns::METHOD_CASH,
                CashoutColumns::METHOD_E_WALLET,
            ])->default(CashoutColumns::METHOD_BANK_TRANSFER);
            $table->string(CashoutColumns::BANK_ACCOUNT, 50)->nullable();

            // Admin reference
            $table->unsignedBigInteger(CashoutColumns::APPROVED_BY)->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes for better query performance
            $table->index(CashoutColumns::USER_ACCOUNT_ID);
            $table->index(CashoutColumns::STATUS);
            $table->index(CashoutColumns::REQUEST_DATE);
            $table->index(CashoutColumns::COMPLETION_DATE);
            $table->index([CashoutColumns::STATUS, CashoutColumns::REQUEST_DATE]);
            $table->index([CashoutColumns::COMPLETION_DATE, CashoutColumns::STATUS]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists($this->table);
        Schema::enableForeignKeyConstraints();
    }
};