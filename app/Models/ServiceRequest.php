<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_code',
        'customer_name',
        'customer_phone',
        'customer_email',
        'service_type',
        'quantity',
        'instructions',
        'attachment_path',
        'quoted_amount',
        'paid_amount',
        'payment_status',
        'status',
        'payment_id',
        'handled_by_user_id',
        'staff_notes',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'quoted_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'quantity' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by_user_id');
    }
}
