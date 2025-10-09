<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReplicationRequest extends Model
{
    protected $fillable = [
        'team_id','paper_id','innovation_title',
        'pic_name','pic_phone','unit_name','superior_name',
        'plant_name','area_location','planned_date',
        'status','created_by', 'financial_benefit','potential_benefit','replication_status','files',
    ];

    protected $casts = [
        'planned_date'       => 'date',
        'financial_benefit'  => 'integer', 
        'potential_benefit'  => 'integer', 
        'files'              => 'array',   
    ];

    public function team()  { return $this->belongsTo(Team::class); }
    public function paper() { return $this->belongsTo(Paper::class); }
    public function creator(){ return $this->belongsTo(User::class, 'created_by'); }
}
