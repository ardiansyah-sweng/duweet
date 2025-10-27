<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\AccountColumns;

return new class extends Migration
{
    protected string $table;

    public function __construct()
    {
        $this->table = config('db_tables.account');
    }
    
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger(AccountColumns::PARENT_ID)->nullable();
            $table->unsignedBigInteger('id_userAccount');
            $table->foreign('id_userAccount')
                  ->references('id_userAccount') // <-- Mereferensi ke PK 'user_accounts'
                  ->on('user_accounts')      // <-- Mereferensi ke tabel 'user_accounts'
                  ->onDelete('cascade');
            $table->string(AccountColumns::NAME, 100);
            
            $table->enum(AccountColumns::TYPE, ['IN', 'EX', 'SP', 'LI', 'AS']);
            $table->bigInteger(AccountColumns::BALANCE)->default(0);
            $table->bigInteger(AccountColumns::INITIAL_BALANCE)->default(0);
            $table->boolean(AccountColumns::IS_GROUP)->default(false);
            $table->text(AccountColumns::DESCRIPTION)->nullable();
            $table->boolean(AccountColumns::IS_ACTIVE)->default(true);
            $table->tinyInteger(AccountColumns::SORT_ORDER)->default(0);
            $table->tinyInteger(AccountColumns::LEVEL)->default(0); 
            $table->timestamps();
        
            $table->foreign(AccountColumns::PARENT_ID)
                  ->references(AccountColumns::ID)
                  ->on($this->table)
                  ->onDelete('set null'); // set null lebih aman dari cascade
            
            $table->index('id_userAccount'); // Index untuk FK baru
            $table->index([AccountColumns::PARENT_ID, AccountColumns::SORT_ORDER]);
            $table->index([AccountColumns::TYPE, AccountColumns::IS_ACTIVE]);
            $table->index(AccountColumns::LEVEL);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acconunts');
    }
};
