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
        $filename = 'qrcodes/'.$laporan->kode_laporan.'.svg';

        if (! $this->assetStorage->putPublic($filename, $this->svg($laporan), 'image/svg+xml')) {
            throw new RuntimeException('QR Code gagal disimpan.');
        }

        return $filename;
    }

    public function svg(Laporan $laporan): string
    {
        return QrCode::format('svg')
            ->size(300)
            ->margin(1)
            ->color(27, 46, 88)
            ->generate(route('laporan.qr.public', $laporan));
    }
}
