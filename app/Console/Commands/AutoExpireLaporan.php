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
    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $batasWaktu = now()->subDays($days);

        $count = Laporan::where('status', 'aktif')
            ->where('created_at', '<=', $batasWaktu)
            ->update([
                'status' => 'expired',
                'expired_at' => now(),
                'updated_at' => now(),
            ]);

        if ($count === 0) {
            $this->info('Tidak ada laporan yang perlu di-expire.');

            return Command::SUCCESS;
        }

        $this->info("Berhasil menandai {$count} laporan sebagai expired (lebih dari {$days} hari).");

        return Command::SUCCESS;
    }
}
