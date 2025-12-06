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
        Schema::create('nota_jual', function (Blueprint $table) {
            $table->char('NoNota', 11)->primary();
            $table->timestamp('Tanggal');
            $table->unsignedBigInteger('KodePelanggan');
            $table->unsignedBigInteger('id_pegawai');

            $table->foreign('KodePelanggan')->references('KodePelanggan')->on('pelanggan');
            $table->foreign('id_pegawai')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nota_jual');
    }
};
