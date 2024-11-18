<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PvtEventTeam extends Model
{
    use HasFactory;

    protected $table = 'pvt_event_teams';

    protected $primaryKey = 'id';

    protected $fillable = [
        'event_id',
        'team_id',
        'status',
        'total_score_on_desk',
        'total_score_presentation',
        'total_score_caucus',
        'final_score', // Jika kamu menggunakan final_score juga
        'is_best_of_the_best',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function summaryExecutives()
    {
        return $this->hasMany(SummaryExecutive::class, 'pvt_event_teams_id');
    }
}
