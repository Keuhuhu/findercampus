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
        Schema::table('klaims', function (Blueprint $table) {
            $table->foreign(['laporan_id'])->references(['id'])->on('laporans')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('klaims', function (Blueprint $table) {
            $table->dropForeign('klaims_laporan_id_foreign');
            $table->dropForeign('klaims_user_id_foreign');
        });
    }
};
