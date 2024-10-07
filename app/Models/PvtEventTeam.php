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
        'year',
        'status'
    ];
}
