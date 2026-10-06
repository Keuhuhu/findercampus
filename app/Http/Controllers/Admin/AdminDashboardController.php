<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Klaim;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_laporan' => Laporan::count(),
            'laporan_aktif' => Laporan::where('status', 'aktif')->count(),
            'total_klaim' => Klaim::count(),
            'klaim_pending' => Klaim::where('status', 'pending')->count(),
            'barang_dikembalikan' => Laporan::where('status', 'selesai')->count(),
            'total_user' => User::where('role', 'user')->count(),
        ];

        $klaimPending = Klaim::with(['laporan.fotos', 'laporan.kategori', 'user'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $laporanTerbaru = Laporan::with(['fotos', 'kategori', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'klaimPending', 'laporanTerbaru'));
    }

    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function laporans()
    {
        $laporans = Laporan::with(['fotos', 'kategori', 'lokasi', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('admin.laporans', compact('laporans'));
    }
}

