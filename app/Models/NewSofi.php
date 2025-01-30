<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewSofi extends Model
{
    use HasFactory;
    protected $table = 'new_sofi';
    protected $fillable = [
        'event_team_id',
        'recommend_category',
        'strength',
        'opportunity_for_improvement',
        'suggestion_for_benefit',
        'last_stage',
    ];

    public function eventTeam()
    {
        return $this->belongsTo(PvtEventTeam::class, 'event_team_id', 'id');
    }
}
