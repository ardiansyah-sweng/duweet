<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
<<<<<<< HEAD

use App\Constants\FinancialFinancialAccountColumns;
=======
use App\Constants\FinancialAccountColumns;
>>>>>>> 67ca2acde21fe1fe26c12698d415a790fdf6cbc0

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
<<<<<<< HEAD
            $table->unsignedBigInteger(FinancialFinancialAccountColumns::PARENT_ID)->nullable();
            $table->string(FinancialFinancialAccountColumns::NAME, 100);
            $table->enum(FinancialFinancialAccountColumns::TYPE, ['IN', 'EX', 'SP', 'LI', 'AS']);
            $table->bigInteger(FinancialFinancialAccountColumns::BALANCE)->default(0);
            $table->bigInteger(FinancialFinancialAccountColumns::INITIAL_BALANCE)->default(0);
            $table->boolean(FinancialFinancialAccountColumns::IS_GROUP)->default(false);
            $table->text(FinancialFinancialAccountColumns::DESCRIPTION)->nullable();
            $table->boolean(FinancialFinancialAccountColumns::IS_ACTIVE)->default(true);
=======
            $table->unsignedBigInteger(FinancialAccountColumns::PARENT_ID)->nullable();
            $table->string(FinancialAccountColumns::NAME, 100);
            $table->enum(FinancialAccountColumns::TYPE, ['IN', 'EX', 'SP', 'LI', 'AS']);
            $table->bigInteger(FinancialAccountColumns::BALANCE)->default(0);
            $table->bigInteger(FinancialAccountColumns::INITIAL_BALANCE)->default(0);
            $table->boolean(FinancialAccountColumns::IS_GROUP)->default(false);
            $table->text(FinancialAccountColumns::DESCRIPTION)->nullable();
            $table->boolean(FinancialAccountColumns::IS_ACTIVE)->default(true);
>>>>>>> 67ca2acde21fe1fe26c12698d415a790fdf6cbc0
            
            //$table->string('color', 7)->nullable(); // hex color code
            //$table->string('icon', 50)->nullable();
            
<<<<<<< HEAD
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
=======
            $table->tinyInteger(FinancialAccountColumns::SORT_ORDER)->default(0);
            $table->tinyInteger(FinancialAccountColumns::LEVEL)->default(0); // 0 = root, 1 = child, 2 = grandchild
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign(FinancialAccountColumns::PARENT_ID)->references(FinancialAccountColumns::ID)->on($this->table)->onDelete('cascade');

            // Indexes for performance
            $table->index([FinancialAccountColumns::PARENT_ID, FinancialAccountColumns::SORT_ORDER]);
            $table->index([FinancialAccountColumns::TYPE,FinancialAccountColumns::IS_ACTIVE]);
            $table->index(FinancialAccountColumns::LEVEL);
>>>>>>> 67ca2acde21fe1fe26c12698d415a790fdf6cbc0
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
