<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'student_id',
        'name',
        'email',
        'program',
        'year_level',
        'status',
        'last_visit',
    ];

    protected function casts(): array
    {
        return [
            'last_visit' => 'date',
        ];
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function getFormattedLastVisitAttribute(): string
    {
        return $this->last_visit?->format('Y-m-d') ?? 'N/A';
    }
}
