<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\KategoriBarang;
use App\Models\LokasiKampus;
use App\Models\FotoBarang;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = Laporan::with(['fotos', 'kategori', 'lokasi', 'user'])->where('status', 'aktif');

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }
        if ($request->filled('q')) {
            $query->where('nama_barang', 'like', '%' . $request->q . '%');
        }

        $laporans = $query->orderBy('created_at', 'desc')->paginate(12);
        $kategoris = KategoriBarang::all();
        $lokasis = LokasiKampus::all();

        return view('laporan.index', compact('laporans', 'kategoris', 'lokasis'));
    }

    public function create()
    {
        $kategoris = KategoriBarang::all();
        $lokasis = LokasiKampus::all();
        return view('laporan.create', compact('kategoris', 'lokasis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipe'             => 'required|in:hilang,ditemukan',
            'nama_barang'      => 'required|string|max:255',
            'kategori_id'      => 'required|exists:kategori_barangs,id',
            'lokasi_id'        => 'required|exists:lokasi_kampuses,id',
            'tanggal_kejadian' => 'required|date|before_or_equal:today',
            'warna'            => 'required|string|max:50',
            'ciri_khusus'      => 'nullable|string',
            'deskripsi'        => 'nullable|string',
            'foto'             => 'nullable|array|max:5',
            'foto.*'           => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $laporan = Laporan::create([
            'user_id'          => Auth::id(),
            'kode_laporan'     => 'FC-' . strtoupper(Str::random(8)),
            'tipe'             => $validated['tipe'],
            'nama_barang'      => $validated['nama_barang'],
            'kategori_id'      => $validated['kategori_id'],
            'lokasi_id'        => $validated['lokasi_id'],
            'tanggal_kejadian' => $validated['tanggal_kejadian'],
            'warna'            => $validated['warna'],
            'ciri_khusus'      => $validated['ciri_khusus'] ?? null,
            'deskripsi'        => $validated['deskripsi'] ?? null,
            'status'           => 'aktif',
        ]);

        if ($request->hasFile('foto')) {
            $isUtama = true;
            foreach ($request->file('foto') as $foto) {
                $path = $foto->store('images/barangs');

                FotoBarang::create([
                    'laporan_id' => $laporan->id,
                    'file_path'  => $path,
                    'is_primary' => $isUtama,
                ]);
                $isUtama = false;
            }
        }

        ActivityLog::create([
            'laporan_id' => $laporan->id,
            'user_id'    => Auth::id(),
            'aksi'       => 'laporan_dibuat',
            'deskripsi'  => 'Laporan barang ' . $laporan->tipe . ' dibuat.',
        ]);

        return redirect('/dashboard')->with('success', 'Laporan berhasil dibuat! Silakan pantau pencocokan di halaman Pencarian.');
    }

    public function show($id)
    {
        $laporan = Laporan::with(['fotos', 'user', 'kategori', 'lokasi', 'activityLogs.user'])->findOrFail($id);
        return view('laporan.show', compact('laporan'));
    }

    public function edit($id)
    {
        $laporan = Laporan::findOrFail($id);
        if ($laporan->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }
        $kategoris = KategoriBarang::all();
        $lokasis = LokasiKampus::all();
        return view('laporan.edit', compact('laporan', 'kategoris', 'lokasis'));
    }

    public function update(Request $request, $id)
    {
        $laporan = Laporan::findOrFail($id);
        if ($laporan->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'nama_barang'      => 'required|string|max:255',
            'kategori_id'      => 'required|exists:kategori_barangs,id',
            'lokasi_id'        => 'required|exists:lokasi_kampuses,id',
            'tanggal_kejadian' => 'required|date|before_or_equal:today',
            'warna'            => 'required|string|max:50',
            'ciri_khusus'      => 'nullable|string',
            'deskripsi'        => 'nullable|string',
            'status'           => 'required|in:aktif,diklaim,selesai,expired,dibatalkan',
        ]);

        $laporan->update($validated);

        return redirect('/laporan/' . $laporan->id)->with('success', 'Laporan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $laporan = Laporan::findOrFail($id);
        if ($laporan->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        foreach ($laporan->fotos as $foto) {
            \Illuminate\Support\Facades\Storage::delete($foto->file_path);
        }

        $laporan->delete();

        return redirect('/dashboard')->with('success', 'Laporan berhasil dihapus.');
    }

    public function downloadQr($id)
    {
        $laporan = Laporan::findOrFail($id);
        if (!$laporan->qr_code_path || !\Illuminate\Support\Facades\Storage::exists($laporan->qr_code_path)) {
            abort(404, 'QR Code tidak ditemukan.');
        }
        return \Illuminate\Support\Facades\Storage::download($laporan->qr_code_path, 'QR_' . $laporan->kode_laporan . '.png');
    }
}
