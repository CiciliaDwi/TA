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
        Schema::create('dataset_predictions', function (Blueprint $table) {
            $table->id();
            $table->string('Kode');
            $table->string('Nama Produk');
            $table->string('Kategori Produk');
            $table->string('Merek');
            $table->string('PID')->default('');
            $table->integer('Qty')->default(null);
            $table->string('Satuan')->default('PCS');
            $table->integer('Qty Konversi')->default(null);
            $table->bigInteger('Omzet');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dataset_predictions');
    }
};
