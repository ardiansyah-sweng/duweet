<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\UserAccountColumns;
use App\Constants\UserColumns;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId(UserAccountColumns::ID_USER)
                  ->constrained('users', UserColumns::ID)
                  ->cascadeOnDelete();
            $table->string(UserAccountColumns::USERNAME)->unique();
            $table->string(UserAccountColumns::EMAIL)->unique();
            $table->string(UserAccountColumns::PASSWORD);
            $table->timestamp(UserAccountColumns::VERIFIED_AT)->nullable();
            $table->boolean(UserAccountColumns::IS_ACTIVE)->default(true);
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_accounts');
    }
};
