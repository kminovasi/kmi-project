<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReplicationInnovation extends Model
{
    use HasFactory;
    protected $table = 'replication_innovations';
    protected $fillable = [
        'paper_id',
        'person_in_charge',
    ];
}