<?php

use App\Constants\UserAccountColumns;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_accounts', function (Blueprint $table) {
            $table->id(UserAccountColumns::ID);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string(UserAccountColumns::USERNAME)->unique();
            $table->string(UserAccountColumns::EMAIL)->unique();
            $table->string(UserAccountColumns::PASSWORD);

            $table->timestamp(UserAccountColumns::EMAIL_VERIFIED_AT)->nullable();

            $table->boolean(UserAccountColumns::IS_ACTIVE)->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('user_accounts');
        Schema::enableForeignKeyConstraints();
    }
};
