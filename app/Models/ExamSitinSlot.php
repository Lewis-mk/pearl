<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSitinSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'slot_datetime',
        'venue',
        'capacity',
        'booked_count',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'slot_datetime' => 'datetime',
            'capacity' => 'integer',
            'booked_count' => 'integer',
        ];
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(ExamSubmission::class, 'sitin_slot_id');
    }

    public function hasAvailableSeats(): bool
    {
        return $this->booked_count < $this->capacity && $this->status === 'open';
    }
}
