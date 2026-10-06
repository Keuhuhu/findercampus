<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\KategoriBarang;
use App\Models\LokasiKampus;
use App\Services\SmartMatchingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PencarianController extends Controller
{
    protected SmartMatchingService $smartService;

    public function __construct(SmartMatchingService $smartService)
    {
        $this->smartService = $smartService;
    }

    public function index(Request $request)
    {
        $laporanSaya = Laporan::where('user_id', Auth::id())
            ->where('tipe', 'hilang')
            ->where('status', 'aktif')
            ->get();

        $kategoris = KategoriBarang::all();
        $lokasis = LokasiKampus::all();
        $hasilPencocokan = null;
        $laporanReferensi = null;

        if ($request->filled('laporan_id')) {
            $laporanReferensi = Laporan::find($request->laporan_id);
            if ($laporanReferensi && $laporanReferensi->user_id === Auth::id()) {
                $hasilPencocokan = $this->smartService->cariKecocokanUntuk($laporanReferensi);
                
                if ($request->filled('kategori')) {
                    $hasilPencocokan = $hasilPencocokan->where('kategori_id', $request->kategori);
                }
                if ($request->filled('lokasi')) {
                    $hasilPencocokan = $hasilPencocokan->where('lokasi_id', $request->lokasi);
                }
            }
        }

        return view('pencarian.index', compact('laporanSaya', 'laporanReferensi', 'hasilPencocokan', 'kategoris', 'lokasis'));
    }
}

