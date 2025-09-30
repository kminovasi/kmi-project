<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Judge extends Model
{
    use HasFactory;
    protected $table = 'judges';
    protected $fillable = [
        'employee_id',
        'event_id',
        'letter_path',
        'status'
    ];

    // Relasi ke model User
    public function user()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
    public function userEmployeeId()
    {
        return $this->belongsTo(User::class, 'employee_id', 'employee_id');
    }

    // Relasi ke model Event
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_judge', 'judge_id', 'event_id');
    }
}
