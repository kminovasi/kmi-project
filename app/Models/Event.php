<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $table = 'events';

    protected $fillable = [
        'event_name',
        'date_start',
        'date_end',
        'status',
        'year',
        'description',
        'type'
    ];
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_code', 'company_code');
    }

    public function companies()
{
    return $this->belongsToMany(Company::class, 'company_event', 'event_id', 'company_id');
}


    public function teams()
    {
        return $this->hasMany(Team::class, 'foreign_key', 'company_code');
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }
    // Relasi ke model Judge
    public function judges()
    {
        return $this->hasMany(Judge::class, 'event_id');
    }

    public function pvtEventTeams()
    {
        return $this->hasMany(PvtEventTeam::class, 'event_id', 'id');
    }

    public function papers()
    {
        return $this->hasManyThrough(
            Paper::class,
            PvtEventTeam::class,
            'event_id',     // Foreign key di PvtEventTeam yang menunjuk ke Event
            'team_id',      // Foreign key di Paper yang menunjuk ke Team
            'id',           // Primary key di Event
            'team_id'       // Foreign key di PvtEventTeam yang menunjuk ke Team
        );
    }

    public function timelines()
    {
        return $this->hasMany(Timeline::class, 'event_id', 'id');
    }
}
