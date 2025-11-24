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
        Schema::table('nota_jual', function (Blueprint $table) {
            $table->enum('metode_pembayaran', ['Cash', 'Debit', 'Kredit'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nota_jual', function (Blueprint $table) {
            $table->dropColumn('metode_pembayaran');
        });
    }
};
