<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected string $table;

    public function __construct()
    {
        $this->table = config('db_tables.user_account');
    }

    public function up(): void
    {
        Schema::table($this->table, function (Blueprint $table) {
            // index for faster join on id_user
            $table->index('id_user', 'idx_user_accounts_id_user');
        });
    }

    public function down(): void
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropIndex('idx_user_accounts_id_user');
        });
    }
};
