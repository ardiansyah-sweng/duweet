<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// HAPUS: use App\Constants\AccountColumns; // <-- KITA HAPUS INI

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

            // --- INI YANG DIUBAH ---
            // Kita ganti semua 'AccountColumns::...' menjadi string biasa
            
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('name', 100);
            $table->enum('type', ['IN', 'EX', 'SP', 'LI', 'AS']);
            $table->bigInteger('balance')->default(0);
            $table->bigInteger('initial_balance')->default(0);
            $table->boolean('is_group')->default(false);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->tinyInteger('sort_order')->default(0);
            $table->tinyInteger('level')->default(0); 
            
            // -------------------------

            $table->timestamps();
            
            // Foreign key constraint
            // Ganti AccountColumns::PARENT_ID -> 'parent_id'
            // Ganti AccountColumns::ID -> 'id'
            $table->foreign('parent_id')->references('id')->on($this->table)->onDelete('cascade');

            // Indexes for performance
            $table->index(['parent_id', 'sort_order']);
            $table->index(['type', 'is_active']);
            $table->index('level');
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

