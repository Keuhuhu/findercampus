<?php

namespace App\Services;

use App\Models\Laporan;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    public function generate(Laporan $laporan): string
    {
        $url = url('/laporan/' . $laporan->id);
        
        $filename = 'qrcodes/' . $laporan->kode_laporan . '.png';
        
        // Ensure directory exists
        if (!Storage::disk('public')->exists('qrcodes')) {
            Storage::disk('public')->makeDirectory('qrcodes');
        }

        // Generate QR Code image (format PNG)
        $qrImage = QrCode::format('png')
            ->size(300)
            ->margin(1)
            ->color(27, 46, 88) // Primary color
            ->generate($url);

        Storage::disk('public')->put($filename, $qrImage);

        return $filename;
    }
}
