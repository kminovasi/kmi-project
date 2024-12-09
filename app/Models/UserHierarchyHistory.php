<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHierarchyHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'directorate_name',
        'group_function_name',
        'department_name',
        'unit_name',
        'section_name',
        'sub_section_of',
        'effective_start_date',
        'effective_end_date',
        'created_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
