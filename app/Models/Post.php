<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'post';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'cover_image',
        'views',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
