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
    ];

    // Relasi ke tabel Event
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
