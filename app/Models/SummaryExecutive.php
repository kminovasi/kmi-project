<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SummaryExecutive extends Model
{
    use HasFactory;
    protected $table = 'summary_executives';
    protected $fillable = [
        'pvt_event_teams_id',
        'problem_background',
        'innovation_idea',
        'benefit',
        'file_ppt',
    ];

    public function pvtEventTeam()
    {
        return $this->belongsTo(PvtEventTeam::class, 'pvt_event_teams_id');
    }
}
