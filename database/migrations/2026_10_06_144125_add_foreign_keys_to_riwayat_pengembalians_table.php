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
        Schema::table('riwayat_pengembalians', function (Blueprint $table) {
            $table->foreign(['dikonfirmasi_oleh'])->references(['id'])->on('users')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['klaim_id'])->references(['id'])->on('klaims')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riwayat_pengembalians', function (Blueprint $table) {
            $table->dropForeign('riwayat_pengembalians_dikonfirmasi_oleh_foreign');
            $table->dropForeign('riwayat_pengembalians_klaim_id_foreign');
        });
    }
};
