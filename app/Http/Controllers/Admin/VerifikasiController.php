<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Klaim;
use App\Models\Verifikasi;
use App\Models\RiwayatPengembalian;
use App\Models\ActivityLog;
use App\Notifications\KlaimStatusUpdatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifikasiController extends Controller
{
    //
    public function show($klaim_id)
    {
        $klaim = Klaim::with(['laporan.fotos', 'laporan.user', 'laporan.kategori', 'laporan.lokasi', 'user', 'verifikasi'])
            ->findOrFail($klaim_id);

        return view('admin.verifikasi-show', compact('klaim'));
    }

    public function approve(Request $request, $klaim_id)
    {
        $request->validate([
            'catatan' => 'nullable|string|max:500',
        ]);

        $klaim = Klaim::with('laporan')->findOrFail($klaim_id);

        $klaim->update(['status' => 'disetujui']);

        Verifikasi::create([
            'klaim_id' => $klaim->id,
            'admin_id' => Auth::id(),
            'hasil' => 'disetujui',
            'catatan' => $request->catatan ?? 'Klaim telah diverifikasi dan disetujui oleh admin.',
        ]);

        $klaim->laporan->update(['status' => 'selesai']);

        RiwayatPengembalian::create([
            'klaim_id' => $klaim->id,
            'dikonfirmasi_oleh' => Auth::id(),
            'tanggal_pengembalian' => now(),
            'metode_pengembalian' => 'langsung',
            'catatan' => 'Barang dikembalikan setelah klaim disetujui.',
        ]);

        ActivityLog::create([
            'laporan_id' => $klaim->laporan_id,
            'user_id' => Auth::id(),
            'aksi' => 'klaim_disetujui',
            'deskripsi' => 'Klaim disetujui oleh admin ' . Auth::user()->name,
        ]);

        $klaim->user->notify(new KlaimStatusUpdatedNotification($klaim, 'disetujui', $request->catatan));

        return redirect('/admin/verifikasi/' . $klaim->id)->with('success', 'Klaim berhasil disetujui dan barang ditandai sebagai dikembalikan.');
    }

    public function reject(Request $request, $klaim_id)
    {
        $request->validate([
            'catatan' => 'required|string|min:10|max:500',
        ]);

        $klaim = Klaim::with('laporan')->findOrFail($klaim_id);

        $klaim->update(['status' => 'ditolak']);

        Verifikasi::create([
            'klaim_id' => $klaim->id,
            'admin_id' => Auth::id(),
            'hasil' => 'ditolak',
            'catatan' => $request->catatan,
        ]);

        $klaim->laporan->update(['status' => 'aktif']);

        ActivityLog::create([
            'laporan_id' => $klaim->laporan_id,
            'user_id' => Auth::id(),
            'aksi' => 'klaim_ditolak',
            'deskripsi' => 'Klaim ditolak oleh admin: ' . $request->catatan,
        ]);

        $klaim->user->notify(new KlaimStatusUpdatedNotification($klaim, 'ditolak', $request->catatan));

        return redirect('/admin/verifikasi/' . $klaim->id)->with('success', 'Klaim ditolak.');
    }
}
