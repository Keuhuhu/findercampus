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
        Schema::create('riwayat_pengembalians', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('klaim_id')->index('riwayat_pengembalians_klaim_id_foreign');
            $table->timestamp('tanggal_pengembalian')->useCurrent();
            $table->enum('metode_pengembalian', ['langsung', 'via_posko'])->default('langsung');
            $table->text('catatan')->nullable();
            $table->bigInteger('dikonfirmasi_oleh')->index('riwayat_pengembalians_dikonfirmasi_oleh_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_pengembalians');
    }
};
