<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Klaim;
use App\Models\Laporan;
use App\Models\RiwayatPengembalian;
use App\Models\Verifikasi;
use App\Notifications\KlaimStatusUpdatedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VerifikasiController extends Controller
{
    public function show(int $klaim_id): View
    {
        $klaim = Klaim::with(['laporan.fotos', 'laporan.user', 'laporan.kategori', 'laporan.lokasi', 'user', 'verifikasi'])
            ->findOrFail($klaim_id);

        return view('admin.verifikasi-show', compact('klaim'));
    }

    public function approve(Request $request, int $klaim_id): RedirectResponse
    {
        $validated = $request->validate([
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($klaim_id, $validated): void {
            $klaim = Klaim::query()->lockForUpdate()->findOrFail($klaim_id);
            abort_unless($klaim->status === 'pending', 409, 'Klaim ini sudah diputuskan.');

            $laporan = Laporan::query()->lockForUpdate()->findOrFail($klaim->laporan_id);
            abort_unless($laporan->status === 'diklaim', 409, 'Status laporan tidak sesuai untuk menyetujui klaim.');

            $catatan = $validated['catatan'] ?? 'Klaim telah diverifikasi dan disetujui oleh admin.';

            $klaim->update(['status' => 'disetujui']);

            Verifikasi::create([
                'klaim_id' => $klaim->id,
                'admin_id' => Auth::id(),
                'hasil' => 'disetujui',
                'catatan' => $catatan,
            ]);

            $laporan->update(['status' => 'selesai']);

            RiwayatPengembalian::create([
                'klaim_id' => $klaim->id,
                'dikonfirmasi_oleh' => Auth::id(),
                'tanggal_pengembalian' => now(),
                'metode_pengembalian' => 'langsung',
                'catatan' => 'Barang dikembalikan setelah klaim disetujui.',
            ]);

            ActivityLog::create([
                'laporan_id' => $laporan->id,
                'user_id' => Auth::id(),
                'aksi' => 'klaim_disetujui',
                'deskripsi' => 'Klaim disetujui oleh admin '.Auth::user()->name,
            ]);

            $klaim->user->notify(new KlaimStatusUpdatedNotification($klaim, 'disetujui', $catatan));
        }, 3);

        return redirect()->route('admin.verifikasi.show', $klaim_id)->with('success', 'Klaim berhasil disetujui dan barang ditandai sebagai selesai.');
    }

    public function reject(Request $request, int $klaim_id): RedirectResponse
    {
        $validated = $request->validate([
            'catatan' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        DB::transaction(function () use ($klaim_id, $validated): void {
            $klaim = Klaim::query()->lockForUpdate()->findOrFail($klaim_id);
            abort_unless($klaim->status === 'pending', 409, 'Klaim ini sudah diputuskan.');

            $laporan = Laporan::query()->lockForUpdate()->findOrFail($klaim->laporan_id);
            abort_unless($laporan->status === 'diklaim', 409, 'Status laporan tidak sesuai untuk menolak klaim.');

            $catatan = $validated['catatan'];

            $klaim->update(['status' => 'ditolak']);

            Verifikasi::create([
                'klaim_id' => $klaim->id,
                'admin_id' => Auth::id(),
                'hasil' => 'ditolak',
                'catatan' => $catatan,
            ]);

            $laporan->update(['status' => 'aktif']);

            ActivityLog::create([
                'laporan_id' => $laporan->id,
                'user_id' => Auth::id(),
                'aksi' => 'klaim_ditolak',
                'deskripsi' => 'Klaim ditolak oleh admin: '.$catatan,
            ]);

            $klaim->user->notify(new KlaimStatusUpdatedNotification($klaim, 'ditolak', $catatan));
        }, 3);

        return redirect()->route('admin.verifikasi.show', $klaim_id)->with('success', 'Klaim ditolak.');
    }
}
