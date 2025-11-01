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
            $table->unsignedBigInteger(UserAccountColumns::ID_USER); 
            $table->string(UserAccountColumns::USERNAME)->unique();
            $table->string(UserAccountColumns::EMAIL)->unique();
            $table->string(UserAccountColumns::PASSWORD);
            $table->timestamp(UserAccountColumns::VERIFIED_AT)->nullable();
            $table->boolean(UserAccountColumns::IS_ACTIVE)->default(true);           
            $table->timestamps();

            $table->foreign(UserAccountColumns::ID_USER)->references(UserAccountColumns::ID_USER)->on($this->table)->onDelete('cascade');
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
