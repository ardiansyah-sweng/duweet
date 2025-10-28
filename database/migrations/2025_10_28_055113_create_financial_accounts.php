<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Constants\FinancialAccountColumns;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{

    protected string $table;

// Ini adalah Constructor (Konstruktor)
    public function __construct()
{
    // Baris ini dijalankan secara otomatis saat migrasi dibuat
    $this->table = config('db_tables.financial_accounts');
}

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
           $table->id(FinancialAccountColumns::ID);

            $table->unsignedBigInteger(FinancialAccountColumns::PARENT_ID)->nullable();
            $table->string(FinancialAccountColumns::NAME, 100);

            $table->enum(FinancialAccountColumns::TYPE, ['IN', 'EX', 'SP', 'LI', 'AS']);

            $table->bigInteger(FinancialAccountColumns::BALANCE)->default(0);
            $table->bigInteger(FinancialAccountColumns::INITIAL_BALANCE)->default(0);

            $table->boolean(FinancialAccountColumns::IS_GROUP)->default(false);
            $table->text(FinancialAccountColumns::DESCRIPTION)->nullable();
            $table->boolean(FinancialAccountColumns::IS_ACTIVE)->default(true);

            $table->string(FinancialAccountColumns::COLOR, 7)->nullable();
            $table->string(FinancialAccountColumns::ICON)->nullable();

            $table->integer(FinancialAccountColumns::SORT_ORDER)->default(0);
            $table->integer(FinancialAccountColumns::LEVEL)->default(0);

            $table->timestamps();
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('financial_accounts')
                  ->onDelete('set null'); // onDelete('restrict') juga bisa
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('financial_accounts');
        Schema::enableForeignKeyConstraints();
    }
};
