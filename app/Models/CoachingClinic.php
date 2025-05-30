<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoachingClinic extends Model
{
    use HasFactory;
    protected $table = 'coaching_clinics';
    protected $fillable = [
        'person_in_charge',
        'company_code',
        'team_id',
        'coaching_date',
        'evidence',
        'coaching_duration',
        'status',
    ];

    protected $casts = [
        'coaching_date' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_code', 'company_code');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'person_in_charge', 'employee_id');
    }
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }
}