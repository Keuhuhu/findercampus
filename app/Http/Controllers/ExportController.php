<?php

namespace App\Http\Controllers;

use App\Models\RiwayatPengembalian;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ExportController extends Controller
{
    public function buktiPengembalian($riwayat_id)
    {
        $riwayat = RiwayatPengembalian::with([
            'klaim.laporan.kategori',
            'klaim.laporan.lokasi',
            'klaim.laporan.user',
            'klaim.user',
            'admin',
        ])->findOrFail($riwayat_id);

        // Hanya pengklaim, pelapor, atau admin yang bisa export
        $allowed = [
            $riwayat->klaim->laporan->user_id,
            $riwayat->klaim->user_id,
        ];
        if (! in_array(Auth::id(), $allowed) && ! Auth::user()->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        $pdf = Pdf::loadView('pdf.bukti-pengembalian', compact('riwayat'))
            ->setPaper('a4', 'portrait');

        $filename = 'Bukti_Pengembalian_'.$riwayat->klaim->laporan->kode_laporan.'.pdf';

        return $pdf->download($filename);
    }
}
