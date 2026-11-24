<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKlaimRequest;
use App\Models\ActivityLog;
use App\Models\Klaim;
use App\Models\Laporan;
use App\Notifications\KlaimDiajukanNotification;
use App\Services\AssetStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class KlaimController extends Controller
{
    public function create(Laporan $laporan): View|RedirectResponse
    {
        $laporan->load(['user', 'kategori', 'lokasi', 'fotos']);

        if ($laporan->user_id === Auth::id()) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak dapat mengklaim barang Anda sendiri.');
        }

        if ($laporan->tipe !== 'ditemukan' || $laporan->status !== 'aktif') {
            return redirect()->route('dashboard')->with('error', 'Barang ini tidak tersedia untuk diklaim.');
        }

        $klaimAktifAda = $laporan->klaims()->whereIn('status', ['pending', 'disetujui'])->exists();

        if ($klaimAktifAda) {
            return redirect()->route('dashboard')->with('error', 'Barang ini sedang memiliki klaim aktif.');
        }

        return view('klaim.create', compact('laporan'));
    }

    public function store(StoreKlaimRequest $request, AssetStorage $assetStorage): RedirectResponse
    {
        $validated = $request->validated();
        $storedEvidencePath = null;

        try {
            $klaim = DB::transaction(function () use ($request, $validated, $assetStorage, &$storedEvidencePath): Klaim {
                $laporan = Laporan::query()->lockForUpdate()->findOrFail($validated['laporan_id']);

                if ($laporan->tipe !== 'ditemukan') {
                    throw ValidationException::withMessages(['laporan_id' => 'Klaim hanya dapat diajukan untuk barang temuan.']);
                }

                if ($laporan->status !== 'aktif') {
                    throw ValidationException::withMessages(['laporan_id' => 'Barang ini sudah tidak aktif untuk diklaim.']);
                }

                if ($laporan->user_id === $request->user()->id) {
                    throw ValidationException::withMessages(['laporan_id' => 'Anda tidak dapat mengklaim barang milik sendiri.']);
                }

                $klaimAktifAda = $laporan->klaims()->whereIn('status', ['pending', 'disetujui'])->exists();

                if ($klaimAktifAda) {
                    throw ValidationException::withMessages(['laporan_id' => 'Barang ini sedang memiliki klaim aktif.']);
                }

                if ($request->hasFile('foto_bukti')) {
                    $manager = new ImageManager(new Driver);
                    $image = $manager->decode($request->file('foto_bukti'));
                    $image->scaleDown(1200, 1200);

                    $storedEvidencePath = 'claims/evidence/'.Str::uuid().'.jpg';
                    $saved = $assetStorage->putPrivate(
                        $storedEvidencePath,
                        (string) $image->encodeUsingMediaType('image/jpeg', quality: 80),
                        'image/jpeg',
                    );

                    if (! $saved) {
                        throw new RuntimeException('Foto bukti klaim gagal disimpan.');
                    }
                }

                $klaim = Klaim::create([
                    'laporan_id' => $laporan->id,
                    'user_id' => $request->user()->id,
                    'bukti_kepemilikan' => $validated['bukti_kepemilikan'],
                    'foto_bukti' => $storedEvidencePath,
                    'status' => 'pending',
                ]);

                $laporan->update(['status' => 'diklaim']);

                ActivityLog::create([
                    'laporan_id' => $laporan->id,
                    'user_id' => $request->user()->id,
                    'aksi' => 'klaim_diajukan',
                    'deskripsi' => 'Klaim kepemilikan diajukan oleh '.$request->user()->name,
                ]);

                $laporan->user->notify(new KlaimDiajukanNotification($klaim));

                return $klaim;
            }, 3);
        } catch (Throwable $exception) {
            if ($storedEvidencePath !== null) {
                try {
                    $assetStorage->deletePrivate($storedEvidencePath);
                } catch (Throwable $cleanupException) {
                    report($cleanupException);
                }
            }

            throw $exception;
        }

        return redirect()->route('klaim.show', $klaim)->with('success', 'Klaim berhasil diajukan dan sedang menunggu verifikasi.');
    }

    public function show(int $id): View
    {
        $klaim = Klaim::with(['laporan.fotos', 'laporan.user', 'user', 'verifikasi.admin', 'riwayatPengembalian'])
            ->findOrFail($id);

        $user = Auth::user();

        abort_unless(
            $user->id === $klaim->user_id || $user->id === $klaim->laporan->user_id || $user->isAdmin(),
            403,
            'Akses ditolak.',
        );

        return view('klaim.show', compact('klaim'));
    }

    public function evidence(int $id, AssetStorage $assetStorage): Response
    {
        $klaim = Klaim::with('laporan')->findOrFail($id);
        $user = Auth::user();

        abort_unless(
            $user->id === $klaim->user_id || $user->id === $klaim->laporan->user_id || $user->isAdmin(),
            403,
            'Akses ditolak.',
        );
        abort_if(! $klaim->foto_bukti, 404);

        return $assetStorage->privateResponse($klaim->foto_bukti);
    }
}
