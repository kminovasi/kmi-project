<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prosedur extends Model
{
    protected $table = 'procedures';

    protected $fillable = ['employee_id','title','file_path'];

    protected $casts = [
        'file_path' => 'array', 
    ];

    public function getFilesAttribute(): array
    {
        return is_array($this->file_path) ? $this->file_path : [];
    }
}
