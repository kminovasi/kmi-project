<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QaMessage extends Model
{
    protected $fillable = [
        'paper_id', 'event_id', 'user_id',
        'author_name', 'role', 'body',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
