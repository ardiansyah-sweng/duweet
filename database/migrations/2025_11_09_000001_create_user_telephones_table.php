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
        $this->table = config('db_tables.user_telephone', 'user_telephones');
        $this->userTable = config('db_tables.user', 'users');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            
            // Foreign key ke tabel users
            $table->foreignId('user_id')
                  ->constrained($this->userTable)
                  ->onDelete('cascade');
            
            $table->string('number', 20)->nullable();
            
            $table->timestamps();
            
            // Index untuk performa
            $table->index('user_id');
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
