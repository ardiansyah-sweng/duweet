<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Constants\FinancialFinancialAccountColumns;

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
            $table->unsignedBigInteger(FinancialFinancialAccountColumns::PARENT_ID)->nullable();
            $table->string(FinancialFinancialAccountColumns::NAME, 100);
            $table->enum(FinancialFinancialAccountColumns::TYPE, ['IN', 'EX', 'SP', 'LI', 'AS']);
            $table->bigInteger(FinancialFinancialAccountColumns::BALANCE)->default(0);
            $table->bigInteger(FinancialFinancialAccountColumns::INITIAL_BALANCE)->default(0);
            $table->boolean(FinancialFinancialAccountColumns::IS_GROUP)->default(false);
            $table->text(FinancialFinancialAccountColumns::DESCRIPTION)->nullable();
            $table->boolean(FinancialFinancialAccountColumns::IS_ACTIVE)->default(true);
            
            //$table->string('color', 7)->nullable(); // hex color code
            //$table->string('icon', 50)->nullable();
            
            $table->tinyInteger(FinancialFinancialAccountColumns::SORT_ORDER)->default(0);
            $table->tinyInteger(FinancialFinancialAccountColumns::LEVEL)->default(0); // 0 = root, 1 = child, 2 = grandchild
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign(FinancialFinancialAccountColumns::PARENT_ID)->references(FinancialFinancialAccountColumns::ID)->on($this->table)->onDelete('cascade');

            // Unique constraint: name must be unique per level within same parent
            $table->unique([FinancialAccountColumns::PARENT_ID, FinancialAccountColumns::NAME]);

            // Indexes for performance
            $table->index([FinancialFinancialAccountColumns::PARENT_ID, FinancialFinancialAccountColumns::SORT_ORDER]);
            $table->index([FinancialFinancialAccountColumns::TYPE,FinancialFinancialAccountColumns::IS_ACTIVE]);
            $table->index(FinancialFinancialAccountColumns::LEVEL);
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
