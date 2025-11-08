<?php

//untuk menambahkan kolom last_login_at pada tabel user_accounts

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE user_accounts ADD COLUMN last_login_at TIMESTAMP NULL;'); 
            //
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE user_accounts DROP COLUMN last_login_at;');
            //
 }
};