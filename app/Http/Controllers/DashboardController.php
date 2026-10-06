<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Klaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $stats = [
            'laporan_aktif' => Laporan::where('user_id', $user->id)->where('status', 'aktif')->count(),
            'klaim_pending' => Klaim::where('user_id', $user->id)->where('status', 'pending')->count(),
            'barang_kembali' => Laporan::where('user_id', $user->id)->where('status', 'selesai')->count(),
        ];

        $laporanSaya = Laporan::with(['fotos', 'kategori', 'lokasi'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $laporanTerbaru = Laporan::with(['fotos', 'kategori', 'lokasi'])
            ->where('status', 'aktif')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('dashboard', compact('stats', 'laporanSaya', 'laporanTerbaru'));
    }
}

