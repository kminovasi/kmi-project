<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';
    protected $fillable = ['category_name','category_parent'];

    public function teams()
    {
        return $this->hasMany(Team::class,'foreign_key', 'category_id');
    }
}
