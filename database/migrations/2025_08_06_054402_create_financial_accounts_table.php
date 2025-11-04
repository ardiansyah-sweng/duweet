<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\FinancialAccountColumns;

return new class extends Migration
{
    protected string $table;

    public function __construct()
    {
        $this->table = config('db_tables.financial_account');
    }
    
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger(FinancialAccountColumns::PARENT_ID)->nullable();
            $table->string(FinancialAccountColumns::NAME, 100);
            $table->enum(FinancialAccountColumns::TYPE, ['IN', 'EX', 'SP', 'LI', 'AS']);
            $table->bigInteger(FinancialAccountColumns::BALANCE)->default(0);
            $table->bigInteger(FinancialAccountColumns::INITIAL_BALANCE)->default(0);
            $table->boolean(FinancialAccountColumns::IS_GROUP)->default(false);
            $table->text(FinancialAccountColumns::DESCRIPTION)->nullable();
            $table->boolean(FinancialAccountColumns::IS_ACTIVE)->default(true);
            // $table->string('color', 7)->nullable(); // hex color code
            // $table->string('icon', 50)->nullable();
            $table->tinyInteger(FinancialAccountColumns::SORT_ORDER)->default(0);
            $table->tinyInteger(FinancialAccountColumns::LEVEL)->default(0); // 0 = root, 1 = child, 2 = grandchild
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign(FinancialAccountColumns::PARENT_ID)->references(FinancialAccountColumns::ID)->on($this->table)->onDelete('cascade');

            // Indexes for performance
            $table->index([FinancialAccountColumns::PARENT_ID, FinancialAccountColumns::SORT_ORDER]);
            $table->index([FinancialAccountColumns::TYPE, FinancialAccountColumns::IS_ACTIVE]);
            $table->index(FinancialAccountColumns::LEVEL);

            
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
