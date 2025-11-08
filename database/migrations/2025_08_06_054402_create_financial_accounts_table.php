<?php

use App\Enums\AccountType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parent_id')->nullable()->constrained('financial_accounts')->onDelete('cascade');

            $table->string('name', 100);

            $table->enum('type', array_column(AccountType::cases(), 'value'));

            $table->bigInteger('balance')->default(0);
            $table->bigInteger('initial_balance')->default(0);
            $table->boolean('is_group')->default(false);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('color', 7)->nullable();
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('level')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_accounts');
    }
};