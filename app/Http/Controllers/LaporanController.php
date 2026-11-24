<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\FotoBarang;
use App\Models\KategoriBarang;
use App\Models\Laporan;
use App\Models\LokasiKampus;
use App\Services\AssetStorage;
use App\Services\QrCodeService;
use App\Services\SmartMatchingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        $query = Laporan::with(['fotos', 'kategori', 'lokasi', 'user'])->where('status', 'aktif');

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        if ($request->filled('q')) {
            $query->where('nama_barang', 'like', '%'.$request->q.'%');
        }

        $laporans = $query->orderByDesc('created_at')->paginate(12);
        $kategoris = KategoriBarang::all();
        $lokasis = LokasiKampus::all();

        return view('laporan.index', compact('laporans', 'kategoris', 'lokasis'));
    }

    public function create(): View
    {
        $kategoris = KategoriBarang::all();
        $lokasis = LokasiKampus::all();

        return view('laporan.create', compact('kategoris', 'lokasis'));
    }

    public function store(Request $request, AssetStorage $assetStorage, QrCodeService $qrCodeService, SmartMatchingService $smartMatchingService): RedirectResponse
    {
        $validated = $request->validate([
            'tipe' => ['required', 'in:hilang,ditemukan'],
            'nama_barang' => ['required', 'string', 'max:255'],
            'kategori_id' => ['required', 'exists:kategori_barangs,id'],
            'lokasi_id' => ['required', 'exists:lokasi_kampuses,id'],
            'detail_lokasi' => ['nullable', 'string', 'max:255'],
            'tanggal_kejadian' => ['required', 'date', 'before_or_equal:today'],
            'waktu_kejadian' => ['nullable', 'date_format:H:i'],
            'warna' => ['required', 'string', 'max:50'],
            'ciri_khusus' => ['nullable', 'string'],
            'deskripsi' => ['nullable', 'string'],
            'foto' => ['nullable', 'array', 'max:5'],
            'foto.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'extensions:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $storedPaths = [];

        try {
            $laporan = DB::transaction(function () use ($request, $validated, $assetStorage, $qrCodeService, $smartMatchingService, &$storedPaths): Laporan {
                $laporan = Laporan::create([
                    'user_id' => Auth::id(),
                    'kode_laporan' => 'FC-'.strtoupper(Str::random(8)),
                    'tipe' => $validated['tipe'],
                    'nama_barang' => $validated['nama_barang'],
                    'kategori_id' => $validated['kategori_id'],
                    'lokasi_id' => $validated['lokasi_id'],
                    'detail_lokasi' => $validated['detail_lokasi'] ?? null,
                    'tanggal_kejadian' => $validated['tanggal_kejadian'],
                    'waktu_kejadian' => $validated['waktu_kejadian'] ?? null,
                    'warna' => $validated['warna'],
                    'ciri_khusus' => $validated['ciri_khusus'] ?? null,
                    'deskripsi' => $validated['deskripsi'] ?? null,
                    'status' => 'aktif',
                ]);

                if ($request->hasFile('foto')) {
                    foreach ($request->file('foto') as $index => $foto) {
                        $path = $assetStorage->storePublic($foto, 'images/barangs');
                        $storedPaths[] = $path;

                        FotoBarang::create([
                            'laporan_id' => $laporan->id,
                            'file_path' => $path,
                            'is_primary' => $index === 0,
                        ]);
                    }
                }

                if ($laporan->tipe === 'ditemukan') {
                    $qrPath = $qrCodeService->generate($laporan);
                    $storedPaths[] = $qrPath;
                    $laporan->update(['qr_code_path' => $qrPath]);
                }

                ActivityLog::create([
                    'laporan_id' => $laporan->id,
                    'user_id' => Auth::id(),
                    'aksi' => 'laporan_dibuat',
                    'deskripsi' => 'Laporan barang '.$laporan->tipe.' dibuat.',
                ]);

                $smartMatchingService->notifyMatchesFor($laporan);

                return $laporan;
            }, 3);
        } catch (Throwable $exception) {
            if ($storedPaths !== []) {
                try {
                    $assetStorage->deletePublic($storedPaths);
                } catch (Throwable $cleanupException) {
                    report($cleanupException);
                }
            }

            throw $exception;
        }

        return redirect()->route('dashboard')->with('success', 'Laporan berhasil dibuat! Silakan pantau pencocokan di halaman Pencarian.');
    }

    public function show(int $id): View
    {
        $laporan = Laporan::with(['fotos', 'user', 'kategori', 'lokasi', 'activityLogs.user'])->findOrFail($id);

        return view('laporan.show', compact('laporan'));
    }

    public function edit(int $id): View
    {
        $laporan = Laporan::with('fotos')->findOrFail($id);
        $this->authorizeReportManagement($laporan);

        $kategoris = KategoriBarang::all();
        $lokasis = LokasiKampus::all();

        return view('laporan.edit', compact('laporan', 'kategoris', 'lokasis'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $laporan = Laporan::findOrFail($id);
        $this->authorizeReportManagement($laporan);

        $validated = $request->validate([
            'nama_barang' => ['required', 'string', 'max:255'],
            'kategori_id' => ['required', 'exists:kategori_barangs,id'],
            'lokasi_id' => ['required', 'exists:lokasi_kampuses,id'],
            'detail_lokasi' => ['nullable', 'string', 'max:255'],
            'tanggal_kejadian' => ['required', 'date', 'before_or_equal:today'],
            'waktu_kejadian' => ['nullable', 'date_format:H:i'],
            'warna' => ['required', 'string', 'max:50'],
            'ciri_khusus' => ['nullable', 'string'],
            'deskripsi' => ['nullable', 'string'],
            'status' => ['prohibited'],
        ]);

        $laporan->update($validated);

        return redirect()->route('laporan.show', $laporan)->with('success', 'Laporan berhasil diperbarui!');
    }

    public function destroy(int $id, AssetStorage $assetStorage): RedirectResponse
    {
        $laporan = Laporan::with('fotos')->findOrFail($id);
        $this->authorizeReportManagement($laporan);

        $assetStorage->deletePublic($laporan->fotos->pluck('file_path')->all());

        if ($laporan->qr_code_path) {
            $assetStorage->deletePublic($laporan->qr_code_path);
        }

        $laporan->delete();

        return redirect()->route('dashboard')->with('success', 'Laporan berhasil dihapus.');
    }

    public function downloadQr(int $id, AssetStorage $assetStorage)
    {
        $laporan = Laporan::findOrFail($id);
        $this->authorizeReportManagement($laporan);

        if (! $laporan->qr_code_path) {
            abort(404, 'QR Code tidak ditemukan.');
        }

        return $assetStorage->downloadPublic($laporan->qr_code_path, 'QR_'.$laporan->kode_laporan.'.svg');
    }

    private function authorizeReportManagement(Laporan $laporan): void
    {
        abort_unless($laporan->user_id === Auth::id() || Auth::user()->isAdmin(), 403);
    }
}
