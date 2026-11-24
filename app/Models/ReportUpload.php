<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportUpload extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id', 'user_id', 'file_path', 'content_type', 'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
