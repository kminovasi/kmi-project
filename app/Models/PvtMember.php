<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PvtMember extends Model
{
    use HasFactory;

    protected $table = 'pvt_members';

    protected $primaryKey = 'id';

    protected $fillable = [
        'team_id',
        'employee_id',
        'status',
        'created_at'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'employee_id', 'employee_id');
    }
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}
