<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\FotoBarang;
use App\Models\KategoriBarang;
use App\Models\Laporan;
use App\Models\LokasiKampus;
use App\Services\AssetStorage;
use App\Services\QrCodeService;
use App\Services\ReportImageUploadService;
use App\Services\SmartMatchingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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

        if ($request->filled('lokasi')) {
            $query->where('lokasi_id', $request->lokasi);
        }

        if ($request->filled('q')) {
            $query->where('nama_barang', 'like', '%'.$request->q.'%');
        }

        $laporans = $query->orderByDesc('created_at')->paginate(12);
        $kategoris = KategoriBarang::all();
        $lokasis = LokasiKampus::all();

        return view('laporan.index', compact('laporans', 'kategoris', 'lokasis'));
    }

    public function create(Request $request): View
    {
        $kategoris = KategoriBarang::all();
        $lokasis = LokasiKampus::all();
        $tipe = in_array($request->query('tipe'), ['hilang', 'ditemukan'], true)
            ? $request->query('tipe')
            : 'hilang';

        return view('laporan.create', compact('kategoris', 'lokasis', 'tipe'));
    }

    public function store(
        Request $request,
        AssetStorage $assetStorage,
        QrCodeService $qrCodeService,
        SmartMatchingService $smartMatchingService,
        ReportImageUploadService $imageUploads,
    ): RedirectResponse {
        $photoRules = $imageUploads->supportsSignedUploads()
            ? ['foto' => ['prohibited'], 'photo_upload_ids' => ['nullable', 'array', 'max:5'], 'photo_upload_ids.*' => ['required', 'uuid', 'distinct']]
            : ['foto' => ['nullable', 'array', 'max:5'], 'foto.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'extensions:jpg,jpeg,png,webp', 'max:'.(ReportImageUploadService::MAX_FILE_BYTES / 1024)], 'photo_upload_ids' => ['prohibited']];

        $validated = $request->validate(array_merge([
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
        ], $photoRules));

        $uploadIds = $validated['photo_upload_ids'] ?? [];
        $localPhotos = $request->file('foto', []);
        if (count($uploadIds) + count($localPhotos) > ReportImageUploadService::MAX_FILES) {
            throw ValidationException::withMessages(['foto' => 'Maksimal 5 foto yang diizinkan.']);
        }

        $storedPaths = [];
        $consumedUploadIds = [];

        try {
            $laporan = DB::transaction(function () use ($request, $validated, $uploadIds, $localPhotos, $assetStorage, $qrCodeService, $smartMatchingService, $imageUploads, &$storedPaths, &$consumedUploadIds): Laporan {
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

                if ($uploadIds !== []) {
                    $result = $imageUploads->attachToReport($laporan, $request->user(), $uploadIds);
                    $storedPaths = array_merge($storedPaths, $result['public_paths']);
                    $consumedUploadIds = array_merge($consumedUploadIds, $result['upload_ids']);
                }

                foreach ($localPhotos as $foto) {
                    $path = $assetStorage->storePublic($foto, 'images/barangs');
                    $storedPaths[] = $path;
                    FotoBarang::create([
                        'laporan_id' => $laporan->id,
                        'file_path' => $path,
                        'is_primary' => ! $laporan->fotos()->where('is_primary', true)->exists(),
                    ]);
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

        try {
            $imageUploads->deleteConsumedUploads($request->user(), $consumedUploadIds);
        } catch (Throwable $cleanupException) {
            report($cleanupException);
        }

        return redirect()->route('dashboard')->with('success', 'Laporan berhasil dibuat! Silakan pantau pencocokan di halaman Pencarian.');
    }

    public function show(int $id): View
    {
        $query = Auth::user()->isAdmin() ? Laporan::withTrashed() : Laporan::query();
        $laporan = $query->with(['fotos', 'user', 'kategori', 'lokasi', 'activityLogs.user'])->findOrFail($id);

        abort_if($laporan->trashed() && ! Auth::user()->isAdmin(), 404);

        return view('laporan.show', compact('laporan'));
    }

    public function showPublicQr(int $id): View
    {
        $laporan = Laporan::query()
            ->where('tipe', 'ditemukan')
            ->where('status', 'aktif')
            ->whereNotNull('qr_code_path')
            ->with(['fotos', 'kategori', 'lokasi'])
            ->findOrFail($id);

        return view('laporan.public-qr', compact('laporan'));
    }

    public function edit(int $id): View
    {
        $laporan = Laporan::with('fotos')->findOrFail($id);
        $this->authorizeReportManagement($laporan);

        $kategoris = KategoriBarang::all();
        $lokasis = LokasiKampus::all();

        return view('laporan.edit', compact('laporan', 'kategoris', 'lokasis'));
    }

    public function update(Request $request, int $id, AssetStorage $assetStorage, ReportImageUploadService $imageUploads): RedirectResponse
    {
        $laporan = Laporan::with('fotos')->findOrFail($id);
        $this->authorizeReportManagement($laporan);

        $photoRules = $imageUploads->supportsSignedUploads()
            ? ['foto' => ['prohibited'], 'photo_upload_ids' => ['nullable', 'array', 'max:5'], 'photo_upload_ids.*' => ['required', 'uuid', 'distinct']]
            : ['foto' => ['nullable', 'array', 'max:5'], 'foto.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'extensions:jpg,jpeg,png,webp', 'max:'.(ReportImageUploadService::MAX_FILE_BYTES / 1024)], 'photo_upload_ids' => ['prohibited']];

        $validated = $request->validate(array_merge([
            'nama_barang' => ['required', 'string', 'max:255'],
            'kategori_id' => ['required', 'exists:kategori_barangs,id'],
            'lokasi_id' => ['required', 'exists:lokasi_kampuses,id'],
            'detail_lokasi' => ['nullable', 'string', 'max:255'],
            'tanggal_kejadian' => ['required', 'date', 'before_or_equal:today'],
            'waktu_kejadian' => ['nullable', 'date_format:H:i'],
            'warna' => ['required', 'string', 'max:50'],
            'ciri_khusus' => ['nullable', 'string'],
            'deskripsi' => ['nullable', 'string'],
            'remove_foto_ids' => ['nullable', 'array', 'max:5'],
            'remove_foto_ids.*' => ['required', 'integer', 'distinct', Rule::exists('foto_barangs', 'id')->where('laporan_id', $laporan->id)],
            'status' => ['prohibited'],
        ], $photoRules));

        $uploadIds = $validated['photo_upload_ids'] ?? [];
        $localPhotos = $request->file('foto', []);
        $removedIds = array_map('intval', $validated['remove_foto_ids'] ?? []);
        $remainingPhotoCount = $laporan->fotos->count() - count($removedIds);

        if ($remainingPhotoCount + count($uploadIds) + count($localPhotos) > ReportImageUploadService::MAX_FILES) {
            throw ValidationException::withMessages(['foto' => 'Maksimal 5 foto dapat disimpan pada setiap laporan.']);
        }

        $newPublicPaths = [];
        $removedPublicPaths = [];
        $consumedUploadIds = [];

        try {
            DB::transaction(function () use ($laporan, $validated, $removedIds, $uploadIds, $localPhotos, $request, $assetStorage, $imageUploads, &$newPublicPaths, &$removedPublicPaths, &$consumedUploadIds): void {
                if ($removedIds !== []) {
                    $removedPhotos = $laporan->fotos()->whereIn('id', $removedIds)->get();
                    $removedPublicPaths = $removedPhotos->pluck('file_path')->all();
                    $laporan->fotos()->whereIn('id', $removedIds)->delete();
                }

                $laporan->update(collect($validated)->except(['photo_upload_ids', 'remove_foto_ids', 'foto'])->all());

                if ($uploadIds !== []) {
                    $result = $imageUploads->attachToReport($laporan, $request->user(), $uploadIds);
                    $newPublicPaths = array_merge($newPublicPaths, $result['public_paths']);
                    $consumedUploadIds = array_merge($consumedUploadIds, $result['upload_ids']);
                }

                foreach ($localPhotos as $foto) {
                    $path = $assetStorage->storePublic($foto, 'images/barangs');
                    $newPublicPaths[] = $path;
                    FotoBarang::create([
                        'laporan_id' => $laporan->id,
                        'file_path' => $path,
                        'is_primary' => ! $laporan->fotos()->where('is_primary', true)->exists(),
                    ]);
                }

                if ($laporan->fotos()->exists() && ! $laporan->fotos()->where('is_primary', true)->exists()) {
                    $laporan->fotos()->oldest('id')->first()?->update(['is_primary' => true]);
                }
            });
        } catch (Throwable $exception) {
            try {
                $assetStorage->deletePublic($newPublicPaths);
            } catch (Throwable $cleanupException) {
                report($cleanupException);
            }

            throw $exception;
        }

        try {
            $assetStorage->deletePublic($removedPublicPaths);
            $imageUploads->deleteConsumedUploads($request->user(), $consumedUploadIds);
        } catch (Throwable $cleanupException) {
            report($cleanupException);
        }

        return redirect()->route('laporan.show', $laporan)->with('success', 'Laporan berhasil diperbarui!');
    }

    public function destroy(int $id): RedirectResponse
    {
        $laporan = Laporan::findOrFail($id);
        $this->authorizeReportManagement($laporan);
        $laporan->delete();

        return redirect()->route(Auth::user()->isAdmin() ? 'admin.laporans' : 'dashboard')
            ->with('success', 'Laporan berhasil diarsipkan.');
    }

    public function qrImage(int $id, QrCodeService $qrCodeService): Response
    {
        $laporan = Laporan::query()
            ->where('tipe', 'ditemukan')
            ->where('status', 'aktif')
            ->whereNotNull('qr_code_path')
            ->findOrFail($id);

        return response($qrCodeService->svg($laporan), Response::HTTP_OK, [
            'Content-Type' => 'image/svg+xml; charset=UTF-8',
            'Content-Disposition' => 'inline; filename="QR_'.$laporan->kode_laporan.'.svg"',
            'Cache-Control' => 'public, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function downloadQr(int $id, QrCodeService $qrCodeService): Response
    {
        $query = Auth::user()->isAdmin() ? Laporan::withTrashed() : Laporan::query();
        $laporan = $query->findOrFail($id);
        $this->authorizeReportManagement($laporan);

        if ($laporan->tipe !== 'ditemukan' || ! $laporan->qr_code_path) {
            abort(404, 'QR Code tidak ditemukan.');
        }

        return response($qrCodeService->svg($laporan), Response::HTTP_OK, [
            'Content-Type' => 'image/svg+xml; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="QR_'.$laporan->kode_laporan.'.svg"',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function authorizeReportManagement(Laporan $laporan): void
    {
        abort_unless($laporan->user_id === Auth::id() || Auth::user()->isAdmin(), 403);
    }
}
