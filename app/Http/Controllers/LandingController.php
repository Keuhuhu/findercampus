<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\User;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $laporanTerbaru = Laporan::with(['fotos', 'kategori', 'lokasi'])
            ->where('status', 'aktif')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();
            
        // Gunakan nama `$stats` DAN `$statistik` agar tidak error kalau tertimpa
        $stats = [
            'total_laporan'       => Laporan::count(),
            'barang_dikembalikan' => Laporan::where('status', 'selesai')->count(),
            'user_aktif'          => User::where('role', 'user')->count(),
            'temuan_aktif'        => Laporan::where('tipe', 'ditemukan')->where('status', 'aktif')->count(),
        ];
        
        $statistik = $stats;

        return view('landing', compact('laporanTerbaru', 'stats', 'statistik'));
    }
}
