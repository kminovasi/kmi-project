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
                            'assessment_points_id',
                            'event_id',
                            'point',
                            'detail_point',
                            'pdca',
                            'score_max',
                            'year',
                            'stage',
                            'category'
                        ];
}
