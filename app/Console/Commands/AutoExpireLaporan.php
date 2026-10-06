<?php

namespace App\Console\Commands;

use App\Models\Laporan;
use Illuminate\Console\Command;

class AutoExpireLaporan extends Command
{
    /**
     * Nama dan signature command di artisan.
     */
    protected $signature = 'laporan:auto-expire {--days=30 : Jumlah hari sebelum laporan kedaluwarsa}';

    /**
     * Deskripsi command.
     */
    protected $description = 'Otomatis menandai laporan yang sudah melewati batas waktu aktif sebagai kedaluwarsa';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $batasWaktu = now()->subDays($days);

        $laporanExpired = Laporan::where('status', 'aktif')
            ->where('created_at', '<=', $batasWaktu)
            ->get();

        if ($laporanExpired->isEmpty()) {
            $this->info('Tidak ada laporan yang perlu di-expire.');
            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($laporanExpired as $laporan) {
            $laporan->update(['status' => 'kedaluwarsa']);
            $count++;
        }

        $this->info("Berhasil menandai {$count} laporan sebagai kedaluwarsa (lebih dari {$days} hari).");

        return Command::SUCCESS;
    }
}
