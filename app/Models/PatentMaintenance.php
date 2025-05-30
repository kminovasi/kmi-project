<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatentMaintenance extends Model
{
    use HasFactory;
    protected $table = 'patent_maintenance';
    protected $fillable = [
        'patent_id',
        'payment_date',
        'amount',
        'payment_proof',
        'status',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
    ];

    public function paten()
    {
        return $this->belongsTo(Patent::class, 'patent_id');
    }
}