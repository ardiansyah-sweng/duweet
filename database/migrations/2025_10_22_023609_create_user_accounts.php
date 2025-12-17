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
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->foreignId(UserAccountColumns::ID_USER)
                ->constrained('users')
                ->onDelete('cascade');

            $table->string(UserAccountColumns::USERNAME)->unique();
            $table->string(UserAccountColumns::EMAIL)->unique();
            $table->string(UserAccountColumns::PASSWORD);
            $table->timestamp(UserAccountColumns::VERIFIED_AT)->nullable();
            $table->boolean(UserAccountColumns::IS_ACTIVE)->default(true);

            // Index untuk mempercepat pencarian user tanpa account (LEFT JOIN) dan filter status
            $table->index(UserAccountColumns::ID_USER);
            $table->index(UserAccountColumns::IS_ACTIVE);
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
