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
        'company_code',
        'replication_status',
        'event_news',
        'evidence',
        'financial_benefit',
        'reward',
    ];

    // This function is used to get the paper associated with this replication
    // It defines a one-to-one relationship between the Replication and Paper models
    public function paper()
    {
        return $this->belongsTo(Paper::class, 'paper_id', 'id');
    }

    // This function is used to get the user associated with this replication
    // It defines a one-to-many relationship between the Replication and User models
    public function personInCharge()
    {
        return $this->belongsTo(User::class, 'person_in_charge', 'id');
    }

    // This function is used to get the company associated with this replication
    // It defines a one-to-many relationship between the Replication and Company models
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_code', 'company_code');
    }
}