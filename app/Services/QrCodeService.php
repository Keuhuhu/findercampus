<?php

namespace App\Services;

use App\Models\Laporan;
use RuntimeException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    public function __construct(private readonly AssetStorage $assetStorage) {}

    public function generate(Laporan $laporan): string
    {
        $url = url('/laporan/'.$laporan->id);
        $filename = 'qrcodes/'.$laporan->kode_laporan.'.svg';

        $qrImage = QrCode::format('svg')
            ->size(300)
            ->margin(1)
            ->color(27, 46, 88)
            ->generate($url);

        if (! $this->assetStorage->putPublic($filename, $qrImage, 'image/svg+xml')) {
            throw new RuntimeException('QR Code gagal disimpan.');
        }

        return $filename;
    }
}
