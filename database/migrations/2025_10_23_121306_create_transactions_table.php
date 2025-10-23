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
        Schema::create('transactions', function (Blueprint $table) {
            $table->integer('ID')->primary();
            $table->integer('ID_AKUN')->unique('id_akun');
            $table->date('TANGGAL');
            $table->text('KETERANGAN');
            $table->integer('JUMLAH');
            $table->enum('JENIS', ['debit', 'kredit']);
            $table->text('REFERENSI');
            $table->integer('SALDO_SETELAH');
            $table->date('DIBUAT_PADA');
            $table->date('DIPERBARUI_PADA');

            $table->unique(['ID_AKUN'], 'id_akun_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
