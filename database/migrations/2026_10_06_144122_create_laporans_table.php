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
        Schema::create('laporans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->index('laporans_user_id_foreign');
            $table->enum('tipe', ['hilang', 'ditemukan']);
            $table->string('kode_laporan')->unique();
            $table->bigInteger('kategori_id')->index('laporans_kategori_id_foreign');
            $table->string('nama_barang');
            $table->string('warna')->nullable();
            $table->text('deskripsi')->nullable();
            $table->text('ciri_khusus')->nullable();
            $table->bigInteger('lokasi_id')->index('laporans_lokasi_id_foreign');
            $table->string('detail_lokasi')->nullable();
            $table->date('tanggal_kejadian');
            $table->time('waktu_kejadian')->nullable();
            $table->enum('status', ['aktif', 'cocok', 'diklaim', 'diverifikasi', 'selesai', 'dibatalkan', 'expired'])->default('aktif');
            $table->string('qr_code_path')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporans');
    }
};
