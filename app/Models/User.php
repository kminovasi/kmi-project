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
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            if ($user->role === 'Superadmin' && User::where('role', 'Superadmin')->count() === 1) {
                throw new \Exception("Tidak dapat menghapus satu-satunya pengguna Superadmin.");
            }
        });


        static::updating(function ($user) {
            if ($user->isDirty('role') && $user->role === 'Superadmin' && User::where('role', 'Superadmin')->count() === 1) {
                throw new \Exception("Tidak dapat mengubah peran satu-satunya pengguna Superadmin.");
            }
            // Atribut yang ingin dilacak perubahan
            $attributesToTrack = [
                'directorate_name',
                'group_function_name',
                'department_name',
                'unit_name',
                'section_name',
                'sub_section_of',
            ];

            // Ambil data yang berubah
            $changes = $user->getDirty();

            foreach ($attributesToTrack as $attribute) {
                if (array_key_exists($attribute, $changes)) {
                    // Cari histori dengan tanggal efektif sama
                    $existingHistories = UserHierarchyHistory::where('user_id', $user->id)
                        ->whereDate('effective_start_date', now()->toDateString())
                        ->get();

                    // Jika ada histori dengan tanggal sama
                    if ($existingHistories->isNotEmpty()) {
                        foreach ($existingHistories as $history) {
                            // Perbarui histori dengan data baru
                            $history->update([
                                'directorate_name' => $user->directorate_name,
                                'group_function_name' => $user->group_function_name,
                                'department_name' => $user->department_name,
                                'unit_name' => $user->unit_name,
                                'section_name' => $user->section_name,
                                'sub_section_of' => $user->sub_section_of,
                            ]);
                        }
                    } else {
                        // Tutup histori lama (jika ada)
                        UserHierarchyHistory::where('user_id', $user->id)
                            ->whereNull('effective_end_date') // Hanya yang masih aktif
                            ->update(['effective_end_date' => now()]);

                        // Buat histori baru untuk perubahan
                        UserHierarchyHistory::create([
                            'user_id' => $user->id,
                            'directorate_name' => $user->directorate_name,
                            'group_function_name' => $user->group_function_name,
                            'department_name' => $user->department_name,
                            'unit_name' => $user->unit_name,
                            'section_name' => $user->section_name,
                            'sub_section_of' => $user->sub_section_of,
                            'effective_start_date' => now(),
                            'effective_end_date' => null, // Biarkan null untuk aktif
                        ]);
                    }
                }
            }
        });
    }

    public function atasan()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
    public function bawahan()
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    public function atasan__()
    {
        return $this->belongsTo(User::class, 'manager_id')
            ->withDefault([
                'name' => 'No Manager',
                'position_title' => '-'
            ]);
    }

    public function bawahan__()
    {
        return $this->hasMany(User::class, 'manager_id')
            ->whereRaw('manager_id::integer = ?', [$this->id]);
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

    public function hierarchyHistories()
    {
        return $this->hasMany(UserHierarchyHistory::class);
    }
    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

}
