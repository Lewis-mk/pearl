<?php

namespace App\Models;

use App\Traits\HasRolesAndPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRolesAndPermissions;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'admission_number',
        'status',
        'is_staff',
        'staff_official_email',
        'staff_email_status',
        'password',
        'deactivated_at',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'deactivated_at' => 'datetime',
            'is_staff' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'user_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    public function absenceRequests(): HasMany
    {
        return $this->hasMany(AbsenceRequest::class, 'student_id');
    }

    public function examSubmissions(): HasMany
    {
        return $this->hasMany(ExamSubmission::class, 'student_id');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class, 'student_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    public function leadCohorts(): HasMany
    {
        return $this->hasMany(Cohort::class, 'lead_trainer_id');
    }

    public function classSessions(): HasMany
    {
        return $this->hasMany(ClassSession::class, 'trainer_id');
    }

    public function misconductReportsFiled(): HasMany
    {
        return $this->hasMany(MisconductReport::class, 'reporter_user_id');
    }

    public function misconductReportsAgainst(): HasMany
    {
        return $this->hasMany(MisconductReport::class, 'reported_user_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && is_null($this->deactivated_at);
    }
}
