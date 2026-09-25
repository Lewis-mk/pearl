<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'cohort_id',
        'course_id',
        'status',
        'guardian_name',
        'guardian_phone',
        'guardian_relationship',
        'fee_total',
        'fee_paid',
        'fee_balance',
        'completion_percentage',
        'admitted_by_user_id',
        'admitted_at',
        'admin_notes',
        'withdrawal_reason',
    ];

    protected function casts(): array
    {
        return [
            'fee_total' => 'decimal:2',
            'fee_paid' => 'decimal:2',
            'fee_balance' => 'decimal:2',
            'completion_percentage' => 'integer',
            'admitted_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function admittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admitted_by_user_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class);
    }

    public function updateFinancials(): void
    {
        $paid = $this->payments()->where('status', 'completed')->sum('amount');
        $this->fee_paid = $paid;
        $this->fee_balance = max(0, (float)$this->fee_total - (float)$paid);
        $this->save();
    }
}
