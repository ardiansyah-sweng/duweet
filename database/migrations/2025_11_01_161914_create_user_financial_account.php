<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\UserFinancialAccountColumns;

return new class extends Migration
{
  protected string $table;

  public function __construct()
  {
      $this->table = config('db_tables.user_financial_account');
  }

  /**
   * Run the migrations.
   */
  public function up(): void
  {
      Schema::create($this->table, function (Blueprint $table) {
          // Primary key
          $table->id();

          // Relasi ke tabel users
          $table->foreignId(UserFinancialAccountColumns::ID_USER)
                ->constrained('users')
                ->onDelete('cascade');

          // Relasi ke tabel financial_accounts
          $table->foreignId(UserFinancialAccountColumns::FINANCIAL_ACCOUNT_ID)
                ->constrained('financial_accounts')
                ->onDelete('cascade');

          // Informasi saldo
          $table->integer(UserFinancialAccountColumns::BALANCE)->default(0);
          $table->integer(UserFinancialAccountColumns::INITIAL_BALANCE)->default(0);
          $table->boolean(UserFinancialAccountColumns::IS_ACTIVE)->default(true);

          // Waktu pembuatan & pembaruan
          $table->timestamps();

          // Cegah duplikasi user ↔ akun finansial
          $table->unique([
              UserFinancialAccountColumns::ID_USER,
              UserFinancialAccountColumns::FINANCIAL_ACCOUNT_ID,
          ]);

          // Index tambahan untuk performa
          $table->index([
              UserFinancialAccountColumns::ID_USER,
              UserFinancialAccountColumns::IS_ACTIVE,
          ]);
      });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
      Schema::dropIfExists($this->table);
  }
};