<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_code',
        'full_name',
        'date_of_birth',
        'gender',
        'contact_number',
        'insurance_provider',
        'insurance_id',
        'plan_type',
        'cashless_eligible',
        'referrer_id',
        'onboarding_token',
        'token_expires_at',
        'onboarding_status',
        'activated_at',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'cashless_eligible' => 'boolean',
            'token_expires_at' => 'datetime',
            'activated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class);
    }
}

