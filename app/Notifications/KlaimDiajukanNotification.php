<?php

namespace App\Notifications;

use App\Models\Klaim;
use Illuminate\Notifications\Notification;

class KlaimDiajukanNotification extends Notification
{
    public function __construct(private readonly Klaim $klaim) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'klaim_diajukan',
            'title' => 'Klaim baru diajukan',
            'message' => sprintf(
                '%s mengajukan klaim atas %s.',
                $this->klaim->user->name,
                $this->klaim->laporan->nama_barang,
            ),
            'url' => route('klaim.show', $this->klaim->id, false),
            'klaim_id' => $this->klaim->id,
        ];
    }
}
