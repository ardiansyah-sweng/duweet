<?php

use Illuminate\Support\Facades\Schema;
use App\Constants\UserTelephoneColumns;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration{
    protected string $table;

    public function __construct(){

    $this->table = config('db_tables.user_telephones');
    }
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id(UserTelephoneColumns::ID);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string(UserTelephoneColumns::NUMBER)
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('user_telephones');
        Schema::enableForeignKeyConstraints();
    }
};
