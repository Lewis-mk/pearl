<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_number',
        'user_id',
        'enrollment_id',
        'amount',
        'payment_method',
        'status',
        'purpose',
        'mpesa_receipt_number',
        'phone_number',
        'merchant_request_id',
        'checkout_request_id',
        'mpesa_raw_response',
        'collected_by_user_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function collectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by_user_id');
    }

    public function refund(): HasOne
    {
        return $this->hasOne(Refund::class);
    }
}
