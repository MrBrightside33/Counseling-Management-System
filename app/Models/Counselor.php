<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Counselor extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'specialization',
        'availability',
        'total_sessions',
    ];

    public function avatarUrl(): ?string
    {
        if (! $this->avatar) {
            return null;
        }

        return asset('storage/'.$this->avatar);
    }

    public function initials(): string
    {
        return collect(explode(' ', $this->name))
            ->filter()
            ->map(fn ($part) => strtoupper($part[0] ?? ''))
            ->take(2)
            ->implode('');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
