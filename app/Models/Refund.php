<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'refund_number',
        'enrollment_id',
        'payment_id',
        'refund_amount',
        'forfeited_deposit_amount',
        'reason',
        'status',
        'processed_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'refund_amount' => 'decimal:2',
            'forfeited_deposit_amount' => 'decimal:2',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by_user_id');
    }
}
