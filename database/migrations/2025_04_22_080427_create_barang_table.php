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
        Schema::create('barang', function (Blueprint $table) {
            $table->char('KodeBarang', 5)->primary();
            $table->string('Barcode', 13);
            $table->string('Nama', 45);
            $table->integer('HargaJual');
            $table->integer('Stok');
            $table->char('KodeKategori', 2);
            $table->timestamps();

            $table->foreign('KodeKategori')->references('KodeKategori')->on('kategori');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
