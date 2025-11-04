<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
<<<<<<< HEAD

return new class extends Migration
{
    protected string $table = 'financial_accounts';

=======
use App\Constants\AccountColumns;

return new class extends Migration
{
    protected string $table;

    public function __construct()
    {
        $this->table = config('db_tables.financial_account');
    }
    
>>>>>>> 6f3325679e06485059f2a5f2fe38054cb01314bf
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
<<<<<<< HEAD
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('name', 100);
            $table->enum('type', ['IN', 'EX', 'SP', 'LI', 'AS']);
            $table->bigInteger('balance')->default(0);
            $table->bigInteger('initial_balance')->default(0);
            $table->boolean('is_group')->default(false);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->tinyInteger('sort_order')->default(0);
            $table->tinyInteger('level')->default(0); // 0 = root, 1 = child, 2 = grandchild
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('parent_id')
                ->references('id')
                ->on($this->table)
                ->onDelete('cascade');

            // Indexes for performance
            $table->index(['parent_id', 'sort_order']);
            $table->index(['type', 'is_active']);
            $table->index('level');
=======
            $table->unsignedBigInteger(AccountColumns::PARENT_ID)->nullable();
            $table->string(AccountColumns::NAME, 100);
            $table->enum(AccountColumns::TYPE, ['IN', 'EX', 'SP', 'LI', 'AS']);
            $table->bigInteger(AccountColumns::BALANCE)->default(0);
            $table->bigInteger(AccountColumns::INITIAL_BALANCE)->default(0);
            $table->boolean(AccountColumns::IS_GROUP)->default(false);
            $table->text(AccountColumns::DESCRIPTION)->nullable();
            $table->boolean(AccountColumns::IS_ACTIVE)->default(true);
            // $table->string('color', 7)->nullable(); // hex color code
            // $table->string('icon', 50)->nullable();
            $table->tinyInteger(AccountColumns::SORT_ORDER)->default(0);
            $table->tinyInteger(AccountColumns::LEVEL)->default(0); // 0 = root, 1 = child, 2 = grandchild
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign(AccountColumns::PARENT_ID)->references(AccountColumns::ID)->on($this->table)->onDelete('cascade');

            // Indexes for performance
            $table->index([AccountColumns::PARENT_ID, AccountColumns::SORT_ORDER]);
            $table->index([AccountColumns::TYPE, AccountColumns::IS_ACTIVE]);
            $table->index(AccountColumns::LEVEL);
>>>>>>> 6f3325679e06485059f2a5f2fe38054cb01314bf
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
