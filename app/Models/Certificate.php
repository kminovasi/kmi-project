<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'template_path',
        'is_active'
    ];

    // Tentukan tipe data pada kolom yang bertipe boolean atau date
    protected $casts = [
        'is_active' => 'boolean'
    ];
}
