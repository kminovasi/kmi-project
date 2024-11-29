<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $table = 'companies';

    protected $fillable = [
        'company_code',
        'company_name',
    ];
    public function events()
    {
        return $this->hasMany(Event::class, 'company_code', 'company_code');
    }

    // Define the relationship to the Team model
    public function teams()
    {
        return $this->hasMany(Team::class, 'company_code', 'company_code');
    }
}
