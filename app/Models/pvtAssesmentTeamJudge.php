<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pvtAssesmentTeamJudge extends Model
{
    use HasFactory;
    protected $table = 'pvt_assesment_team_judges';
    protected $primaryKey = 'id';
    protected $fillable = [
        'judge_id',
        'score',
        'event_team_id',
        'assessment_event_id',
        'stage'
    ];

    public function judge()
    {
        return $this->belongsTo(Judge::class, 'judge_id');
    }
}
