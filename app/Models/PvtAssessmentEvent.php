<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PvtAssessmentEvent extends Model
{
    use HasFactory;
    protected $table = 'pvt_assessment_events';
    protected $primaryKey = 'id';
    protected $fillable = [
        'event_id',
        'point',
        'detail_point',
        'pdca',
        'score_max',
        'stage',
        'category',
        'status_point'
    ];
}
