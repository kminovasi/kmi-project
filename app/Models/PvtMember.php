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
        'position_title',
        'directorate_name',
        'group_function_name',
        'department_name',
        'unit_name',
        'section_name',
        'sub_section_of',
        'company_code',
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
