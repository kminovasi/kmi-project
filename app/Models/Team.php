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
        'phone_number'
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
            ->where('type', 'AP');
    }
}
