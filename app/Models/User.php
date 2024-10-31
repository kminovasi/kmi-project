<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_id',
        'username',
        'password',
        'name',
        'email',
        'position_title',
        'company_code',
        'company_name',
        'directorate_name',
        'group_function_name',
        'department_name',
        'unit_name',
        'section_name',
        'sub_section_of',
        'date_of_birth',
        'gender',
        'job_level',
        'contract_type',
        'home_company',
        'manager_id',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    public function atasan()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
    public function bawahan()
    {
        return $this->hasMany(User::class, 'manager_id');
    }
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'pvt_members');
    }
    public function pvtMembers()
    {
        return $this->hasMany(PvtMember::class, 'employee_id', 'employee_id');
    }
    // Relasi ke model Judge
    public function judges()
    {
        return $this->hasMany(Judge::class, 'employee_id');
    }
}
