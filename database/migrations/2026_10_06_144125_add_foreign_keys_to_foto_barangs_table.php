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
        Schema::table('foto_barangs', function (Blueprint $table) {
            $table->foreign(['laporan_id'])->references(['id'])->on('laporans')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('foto_barangs', function (Blueprint $table) {
            $table->dropForeign('foto_barangs_laporan_id_foreign');
        });
    }
};
