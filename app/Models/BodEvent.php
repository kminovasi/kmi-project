<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BodEvent extends Model
{
    use HasFactory;
    protected $table = 'bod_events';
    protected $fillable = ['employee_id', 'event_id', 'status'];

    // Relasi ke tabel users (mengganti employee)
    public function user()
    {
        return $this->belongsTo(User::class, 'employee_id', 'employee_id');
    }

    // Relasi ke tabel events
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    // Periksa apakah aktif
    public function isActive()
    {
        return $this->status === 'active';
    }

    // Toggle status aktif/non-aktif
    public function toggleActive()
    {
        $this->status = $this->isActive() ? 'nonactive' : 'active';
        $this->save();
    }
}
