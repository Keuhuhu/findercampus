<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKlaimRequest;
use App\Models\Klaim;
use App\Models\Laporan;
use App\Models\ActivityLog;
use App\Notifications\KlaimDiajukanNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class KlaimController extends Controller
{
    public function create(Laporan $laporan) 
    {
        // Kita load relasinya secara manual karena menggunakan Route Model Binding
    $laporan->load(['user', 'kategori', 'lokasi', 'fotos']);

    if ($laporan->user_id === Auth::id()) {
        return redirect('/dashboard')->with('error', 'Anda tidak dapat mengklaim barang Anda sendiri.');
    }

    if ($laporan->status !== 'aktif') {
        return back()->with('error', 'Barang ini sudah tidak tersedia untuk diklaim.');
    }

    $klaimAda = Klaim::where('laporan_id', $laporan->id)
        ->where('user_id', Auth::id())
        ->whereIn('status', ['pending', 'disetujui'])
        ->exists();
        
    if ($klaimAda) {
        return back()->with('error', 'Anda sudah mengajukan klaim untuk barang ini.');
    }

    return view('klaim.create', compact('laporan'));
    }

    public function store(StoreKlaimRequest $request)
    {
        $laporan = Laporan::findOrFail($request->laporan_id);
        
        $klaimData = [
            'laporan_id' => $laporan->id,
            'user_id' => Auth::id(),
            'bukti_kepemilikan' => $request->bukti_kepemilikan,
            'status' => 'pending',
        ];

        if ($request->hasFile('foto_bukti')) {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($request->file('foto_bukti'));
            $image->scaleDown(1200, 1200);

            $path = 'bukti/' . $laporan->id . '_' . Auth::id() . '_' . time() . '.jpg';
            Storage::disk('public')->put($path, $image->toJpeg(80));

            $klaimData['foto_bukti'] = $path;
        }

        $klaim = Klaim::create($klaimData);

        $laporan->update(['status' => 'diklaim']);

        ActivityLog::create([
            'laporan_id' => $laporan->id,
            'user_id' => Auth::id(),
            'aksi' => 'klaim_diajukan',
            'deskripsi' => 'Klaim kepemilikan diajukan oleh ' . Auth::user()->name,
        ]);

        $laporan->user->notify(new KlaimDiajukanNotification($klaim));

        return redirect('/klaim/' . $klaim->id)->with('success', 'Klaim berhasil diajukan dan sedang menunggu verifikasi.');
    }   

    public function show($id)
    {
        $klaim = Klaim::with(['laporan.fotos', 'laporan.user', 'user', 'verifikasi.admin', 'riwayatPengembalian'])
            ->findOrFail($id);

        if (Auth::id() !== $klaim->user_id && Auth::id() !== $klaim->laporan->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        return view('klaim.show', compact('klaim'));
    }
}

