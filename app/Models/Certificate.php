<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $table = 'certificates';

    protected $fillable = [
        'event_id',
        'template_path',
        'badge_rank_1',
        'badge_rank_2',
        'badge_rank_3',
        'special_template_path',
        'certificate_date',
    ];

    protected $casts = ['certificate_date' => 'date'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
