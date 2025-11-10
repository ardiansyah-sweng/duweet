<?php

use Illuminate\Support\Facades\Schema;
use App\Constants\UserTelephoneColumns;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Table name for user telephones.
     * Defaults to 'user_telephones' when config key is missing.
     *
     * @var string
     */
    protected string $table = 'user_telephones';

    public function __construct()
    {
        $this->table = config('db_tables.user_telephones', $this->table);
    }
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id(UserTelephoneColumns::ID);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string(UserTelephoneColumns::NUMBER)->nullable();
            $table->timestamps();
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
