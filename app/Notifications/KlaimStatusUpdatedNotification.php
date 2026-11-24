<?php

namespace App\Notifications;

use App\Models\Klaim;
use Illuminate\Notifications\Notification;

class KlaimStatusUpdatedNotification extends Notification
{
    public function __construct(
        private readonly Klaim $klaim,
        private readonly string $status,
        private readonly ?string $catatan,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $approved = $this->status === 'disetujui';

        return [
            'type' => 'klaim_status',
            'title' => $approved ? 'Klaim disetujui' : 'Klaim ditolak',
            'message' => $approved
                ? sprintf('Klaim Anda untuk %s telah disetujui.', $this->klaim->laporan->nama_barang)
                : sprintf('Klaim Anda untuk %s ditolak.%s', $this->klaim->laporan->nama_barang, $this->catatan ? ' Catatan: '.$this->catatan : ''),
            'url' => route('klaim.show', $this->klaim->id, false),
            'klaim_id' => $this->klaim->id,
            'status' => $this->status,
            'catatan' => $this->catatan,
        ];
    }
}
