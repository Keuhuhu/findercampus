<?php

namespace App\Notifications;

use App\Models\Laporan;
use Illuminate\Notifications\Notification;

class MatchDitemukanNotification extends Notification
{
    public function __construct(
        private readonly Laporan $laporan,
        private readonly Laporan $laporanCocok,
        private readonly int $skor,
        private readonly string $matchKey,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'match_ditemukan',
            'title' => 'Potensi kecocokan baru',
            'message' => sprintf(
                'Laporan %s cocok dengan laporan %s dengan skor %d%%.',
                $this->laporan->kode_laporan,
                $this->laporanCocok->kode_laporan,
                $this->skor,
            ),
            'url' => route('laporan.show', $this->laporanCocok->id, false),
            'match_key' => $this->matchKey,
            'laporan_id' => $this->laporan->id,
            'matched_laporan_id' => $this->laporanCocok->id,
            'score' => $this->skor,
        ];
    }
}
