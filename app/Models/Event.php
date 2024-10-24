<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $table = 'events';

    protected $fillable = [
        'event_name',
        'company_code',
        'date_start',
        'date_end',
        'status',
        'year',
        'description'
    ];
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_code', 'company_code');
    }

    public function teams()
    {
        return $this->hasMany(Team::class, 'foreign_key', 'company_code');
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }
}
