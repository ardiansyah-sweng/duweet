<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('middle_name')->nullable()->after('first_name');
            $table->string('last_name')->nullable()->after('middle_name');
            $table->tinyInteger('tanggal_lahir')->unsigned()->nullable()->after('last_name');
            $table->tinyInteger('bulan_lahir')->unsigned()->nullable()->after('tanggal_lahir');
            $table->smallInteger('tahun_lahir')->unsigned()->nullable()->after('bulan_lahir');
            $table->integer('usia')->unsigned()->nullable()->after('tahun_lahir');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'middle_name',
                'last_name',
                'tanggal_lahir',
                'bulan_lahir',
                'tahun_lahir',
                'usia',
            ]);
        });
    }
};
