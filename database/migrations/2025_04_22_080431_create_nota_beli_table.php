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
        Schema::create('nota_beli', function (Blueprint $table) {
            $table->char('NoNota', 11)->primary();
            $table->timestamp('Tanggal');
            $table->integer('KodeSupplier')->unsigned();
            $table->integer('id_pegawai')->unsigned();
            $table->date('TanggalJatuhTempo');
            $table->date('TanggalBayar');
            $table->float('Diskon');
            $table->timestamps();
        
            $table->foreign('KodeSupplier')->references('KodeSupplier')->on('supplier');
            $table->foreign('id_pegawai')->references('id')->on('users');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nota_beli');
    }
};
