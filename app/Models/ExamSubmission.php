<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'student_id',
        'sitin_slot_id',
        'attempt_number',
        'retake_granted',
        'retake_granted_by_user_id',
        'started_at',
        'submitted_at',
        'student_answers',
        'score',
        'percentage',
        'grade_status',
        'passed',
        'trainer_feedback',
        'graded_by_user_id',
        'graded_at',
    ];

    protected function casts(): array
    {
        return [
            'attempt_number' => 'integer',
            'retake_granted' => 'boolean',
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'graded_at' => 'datetime',
            'student_answers' => 'array',
            'score' => 'decimal:2',
            'percentage' => 'decimal:2',
            'passed' => 'boolean',
        ];
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function sitinSlot(): BelongsTo
    {
        return $this->belongsTo(ExamSitinSlot::class, 'sitin_slot_id');
    }

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by_user_id');
    }

    public function retakeGrantedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'retake_granted_by_user_id');
    }
}
