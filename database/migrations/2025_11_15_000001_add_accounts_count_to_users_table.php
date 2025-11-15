<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Default to 'users' to keep this file usable outside Laravel's helper context
    protected string $table = 'users';

    public function __construct()
    {
        // Only call the Laravel `config()` helper when it's available (prevents static analysis errors)
        if (function_exists('config')) {
            $this->table = config('db_tables.user');
        }
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table($this->table, function (Blueprint $table) {
            // Add a denormalized count of user accounts. Default 0 to avoid null handling.
            $table->unsignedInteger('accounts_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropColumn('accounts_count');
        });
    }
};

