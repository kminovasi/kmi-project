<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetodologiPaper extends Model
{
    use HasFactory;

    protected $table = 'metodologi_papers';

    protected $fillable = [
        'name',
        'step',
        'max_user'
    ];
}
