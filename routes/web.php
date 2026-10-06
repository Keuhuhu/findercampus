<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PencarianController;
use App\Http\Controllers\KlaimController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\VerifikasiController;

// Halaman Utama (Landing Page)
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Auth Routes (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Auth Routes (Authenticated)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profil', [ProfilController::class, 'show'])->name('profil.show');
    Route::get('/profil/edit', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');
    Route::post('/profil/avatar', [ProfilController::class, 'updateAvatar'])->name('profil.avatar');
    Route::post('/profil/toggle-whatsapp', [ProfilController::class, 'toggleWhatsapp'])->name('profil.toggle-whatsapp');
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/buat', [LaporanController::class, 'create'])->name('laporan.create');
    Route::post('/laporan', [LaporanController::class, 'store'])->name('laporan.store');
    Route::get('/laporan/{id}', [LaporanController::class, 'show'])->name('laporan.show');
    Route::get('/laporan/{id}/edit', [LaporanController::class, 'edit'])->name('laporan.edit');
    Route::put('/laporan/{id}', [LaporanController::class, 'update'])->name('laporan.update');
    Route::delete('/laporan/{id}', [LaporanController::class, 'destroy'])->name('laporan.destroy');
    Route::get('/laporan/{id}/qr', [LaporanController::class, 'downloadQr'])->name('laporan.qr');
    Route::get('/pencarian', [PencarianController::class, 'index'])->name('pencarian.index');
    Route::get('/klaim/buat/{laporan}', [KlaimController::class, 'create'])->name('klaim.create');
    Route::post('/klaim', [KlaimController::class, 'store'])->name('klaim.store');
    Route::get('/klaim/{id}', [KlaimController::class, 'show'])->name('klaim.show');
    Route::get('/status', [StatusController::class, 'index'])->name('status');
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{id}/read', [NotifikasiController::class, 'markAsRead'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [NotifikasiController::class, 'markAllAsRead'])->name('notifikasi.read-all');
    Route::get('/export/bukti-pengembalian/{riwayat_id}', [ExportController::class, 'buktiPengembalian'])->name('export.bukti');
});

// Admin Routes (Phase 8)
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [AdminDashboardController::class, 'users'])->name('admin.users');
    Route::get('/laporans', [AdminDashboardController::class, 'laporans'])->name('admin.laporans');
    Route::get('/verifikasi/{klaim_id}', [VerifikasiController::class, 'show'])->name('admin.verifikasi.show');
    Route::post('/verifikasi/{klaim_id}/approve', [VerifikasiController::class, 'approve'])->name('admin.verifikasi.approve');
    Route::post('/verifikasi/{klaim_id}/reject', [VerifikasiController::class, 'reject'])->name('admin.verifikasi.reject');
});

Route::get('/syarat-ketentuan', fn() => view('pages.syarat-ketentuan'))->name('syarat-ketentuan');
Route::get('/kebijakan-privasi', fn() => view('pages.kebijakan-privasi'))->name('kebijakan-privasi');
Route::get('/panduan-smart', fn() => view('pages.panduan-smart'))->name('panduan-smart');
