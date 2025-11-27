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
        Schema::table('nota_jual_detil', function (Blueprint $table) {
            $table->decimal('Total', 10, 2)->after('Jumlah');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nota_jual_detil', function (Blueprint $table) {
            $table->dropColumn('Total');
        });
    }
};
