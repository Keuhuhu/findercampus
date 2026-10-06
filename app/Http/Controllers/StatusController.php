<?php

namespace App\Http\Controllers;

use App\Models\Klaim;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatusController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'laporan');
        
        $laporans = collect();
        $klaims = collect();
        $klaimMasuk = collect();

        if ($tab === 'laporan') {
            $laporans = Laporan::with(['fotos', 'lokasi', 'kategori'])
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } elseif ($tab === 'klaim') {
            $klaims = Klaim::with(['laporan.fotos', 'laporan.kategori', 'laporan.user'])
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } elseif ($tab === 'klaim_masuk') {
            $klaimMasuk = Klaim::with(['laporan.fotos', 'user'])
                ->whereHas('laporan', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('status.index', compact('tab', 'laporans', 'klaims', 'klaimMasuk'));
    }
}

