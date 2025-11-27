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
        Schema::create('nota_beli_detil', function (Blueprint $table) {
            $table->char('NoNota', 11);
            $table->char('KodeBarang', 5);
            $table->integer('Harga');
            $table->integer('Jumlah');
            $table->timestamps();

            $table->primary(['NoNota', 'KodeBarang']);
            $table->foreign('NoNota')->references('NoNota')->on('nota_beli');
            $table->foreign('KodeBarang')->references('KodeBarang')->on('barang');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nota_beli_detail');
    }
};
