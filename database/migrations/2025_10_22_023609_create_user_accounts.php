<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\UserAccountColumns;

return new class extends Migration
{
    protected string $table;

    public function __construct()
    {
        $this->table = config('db_tables.user_account');
    }
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // No-op: superseded by 2025_11_05_101756_create_user_accounts_table.php
        // Dibiarkan kosong agar tidak bentrok saat test (sqlite in-memory)
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
