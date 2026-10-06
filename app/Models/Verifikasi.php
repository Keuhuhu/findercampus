<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Verifikasi extends Model
{
    protected $fillable = ['klaim_id', 'admin_id', 'status', 'catatan'];

    public function klaim()
    {
        return $this->belongsTo(Klaim::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}

