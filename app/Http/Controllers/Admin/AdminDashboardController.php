<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Klaim;
use App\Models\User;
use Illuminate\Http\Request;

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

    public function laporans(Request $request)
    {
        $query = Laporan::withTrashed()->with(['fotos', 'kategori', 'lokasi', 'user']);

        if ($request->boolean('archived')) {
            $query->onlyTrashed();
        } else {
            $query->whereNull('deleted_at');
        }

        $laporans = $query
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        $archived = $request->boolean('archived');

        return view('admin.laporans', compact('laporans', 'archived'));
    }

    public function restoreLaporan(int $id)
    {
        $laporan = Laporan::withTrashed()->findOrFail($id);
        abort_unless($laporan->trashed(), 404);
        $laporan->restore();

        return redirect()->route('admin.laporans', ['archived' => true])
            ->with('success', 'Laporan berhasil dipulihkan.');
    }
}

