<?php

namespace App\Models;

use Database\Factories\CampusEmailVerificationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampusEmailVerification extends Model
{
    /** @use HasFactory<CampusEmailVerificationFactory> */
    use HasFactory;

    public const ALLOWED_EMAIL_DOMAINS = [
        'student.unsri.ac.id',
        'unsri.ac.id',
        'ilkom.unsri.ac.id',
    ];

    protected $fillable = [
        'user_id',
        'email',
        'status',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
        ];
    }

    public static function supportsEmailDomain(string $email): bool
    {
        $emailDomain = strtolower(substr(strrchr($email, '@') ?: '', 1));

        return in_array($emailDomain, self::ALLOWED_EMAIL_DOMAINS, true);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
