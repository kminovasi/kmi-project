<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearnShare extends Model
{
    protected $table = 'learn_share';

    protected $fillable = [
        'title',
        'job_function',
        'competency',
        'requesting_department',
        'scheduled_at',
        'objective',
        'opening_speech',
        'speakers',
        'participants',
        'employee_id',
        'status',
        'status_comment',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'speakers'     => 'array',
        'participants' => 'array',
    ];

    public const STATUS_PENDING  = 'Pending';
    public const STATUS_APPROVED = 'Approved';
    public const STATUS_REJECTED = 'Rejected';

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'bg-success',
            self::STATUS_REJECTED => 'bg-danger',
            default               => 'bg-secondary',
        };
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'employee_id', 'employee_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'employee_id', 'employee_id');
    }
}
