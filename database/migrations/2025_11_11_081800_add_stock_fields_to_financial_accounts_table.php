<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('financial_accounts', function (Blueprint $table) {
            $table->string('stock_symbol', 10)->nullable()->after('description');
            $table->decimal('current_price', 15, 2)->nullable()->after('stock_symbol');
            $table->timestamp('price_updated_at')->nullable()->after('current_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('financial_accounts', function (Blueprint $table) {
            $table->dropColumn(['stock_symbol', 'current_price', 'price_updated_at']);
        });
    }
};
