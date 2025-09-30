<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Team extends Model
{
    use HasFactory;
    protected $table = 'teams';
    protected $fillable = [
        'team_name',
        'company_code',
        'category_id',
        'theme_id',
        'status_lomba',
        'phone_number',
        'created_at'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'pvt_members');
    }

    public function categories()
    {
        return $this->belongsTo(Category::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function themes()
    {
        return $this->belongsTo(Theme::class);
    }

    public function paper()
    {
        return $this->hasOne(Paper::class, 'team_id', 'id');
    }
    public function papers()
    {
        return $this->hasMany(Paper::class, 'team_id', 'id');
    }
    public function history()
    {
        return $this->hasOne(history::class, 'team_id', 'id');
    }
    public function pvtMembers()
    {
        return $this->hasMany(PvtMember::class, 'team_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_code', 'company_code'); // Pastikan kolom yang digunakan sesuai
    }
    
    public function getRelatedCompanyAttribute()
    {
        // Ambil semua events dari team ini → lalu ambil semua company dari tiap event
        return $this->events
            ->flatMap(function ($event) {
                return $event->companies;
            })
            ->unique('id') // Optional: hilangkan duplikat
            ->first();     // Ambil satu (atau bisa return semuanya kalau mau)
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'pvt_event_teams')
            ->withPivot('status')
            ->withTimestamps();
    }

    // Di model Team
    public function internalEvents()
    {
        return $this->belongsToMany(Event::class, 'pvt_event_teams', 'team_id', 'event_id')
            ->where('type', 'internal');
    }
    public function apEvents()
    {
        return $this->belongsToMany(Event::class, 'pvt_event_teams', 'team_id', 'event_id')
            ->where('type', 'AP');
    }

    public function pvtEventTeams()
    {
        return $this->hasMany(PvtEventTeam::class, 'team_id');
    }

    public function members()
    {
        return $this->hasManyThrough(
            User::class,
            PvtMember::class,
            'team_id',       // Foreign key di tabel PvtMember
            'employee_id',   // Foreign key di tabel User
            'id',            // Local key di tabel Team
            'employee_id'    // Local key di tabel PvtMember
        );
    }
}