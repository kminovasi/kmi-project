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
        'group',
        'sort_order',
    ];

    public function events()
    {
        return $this->belongsToMany(Event::class, 'company_event', 'company_id', 'event_id');
    }


    // Define the relationship to the Team model
    public function teams()
    {
        return $this->hasMany(Team::class, 'company_code', 'company_code');
    }
}