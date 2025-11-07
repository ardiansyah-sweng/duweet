<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\UserTelephoneColumns;

return new class extends Migration
{
    protected string $table;

    public function __construct()
    {
        $this->table = config('db_tables.user_telephone', 'user_telephones');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->foreignId(UserTelephoneColumns::USER_ID)
                ->constrained('users')
                ->onDelete('cascade');
            $table->string(UserTelephoneColumns::NUMBER)->nullable();
            
            // Index untuk performa
            $table->index(UserTelephoneColumns::USER_ID);
        });
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
