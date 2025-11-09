<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected string $table;
    protected string $userTable;

    public function __construct()
    {
        // Pastikan config ini ada di config/db_tables.php
        $this->table     = config('db_tables.user_account', 'user_accounts');
        $this->userTable = config('db_tables.user', 'users');
    }

    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();

            // Foreign key ke tabel users
            $table->foreignId('user_id')
                  ->constrained($this->userTable)
                  ->onDelete('cascade');

            $table->string('username', 50)->unique();
            $table->string('email', 191)->unique();
            $table->string('password', 255);

            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Index tambahan (performa)
            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->table);
    }
};
