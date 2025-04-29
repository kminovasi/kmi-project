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
        'max_user',
    ];

    // This function is used to get the template paper steps associated with this metodologi paper
    // It defines a one-to-many relationship between the MetodologiPaper and TemplatePaperStep models
    function templatePaperStep()
    {
        return $this->hasMany(TemplatePaperStep::class, 'metodologi_paper_id', 'id');
    }
}