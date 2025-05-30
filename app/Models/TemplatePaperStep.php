<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplatePaperStep extends Model
{
    use HasFactory;
    protected $table = 'template_paper_steps';
    protected $fillable = [
        'metodologi_paper_id',
        'step_1',
        'step_2',
        'step_3',
        'step_4',
        'step_5',
        'step_6',
        'step_7',
        'step_8',
    ];

    // This function is used to get the metodologi_paper associated with this template_paper_step
    // It defines a one-to-many relationship between the TemplatePaperStep and MetodologiPaper models
    function metodologiPaper()
    {
        return $this->belongsTo(MetodologiPaper::class, 'metodologi_paper_id');
    }
}