<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MisconductReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_code',
        'reporter_user_id',
        'reporter_role',
        'reported_user_id',
        'reported_party_name',
        'reported_party_type',
        'category',
        'subject_summary',
        'incident_details',
        'incident_date',
        'location',
        'evidence_file_path',
        'hide_reporter_from_subject',
        'is_escalated_to_secondary_contact',
        'secondary_contact_notified_to',
        'status',
        'resolution_summary',
        'resolved_by_user_id',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'incident_date' => 'date',
            'resolved_at' => 'datetime',
            'hide_reporter_from_subject' => 'boolean',
            'is_escalated_to_secondary_contact' => 'boolean',
        ];
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_user_id');
    }

    public function reportedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by_user_id');
    }
}
