<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Patent extends Model
{
    use HasFactory;

    protected $table = 'patent';

    protected $fillable = [
        'paper_id', 
        'registration_number',
        'person_in_charge',
        'application_status',
        'draft_paten',
        'ownership_letter',
        'statement_of_transfer_rights',
        'certificate',
        'certificate_number',
        'paten_status',
        'payment_deadline',
        'amount'
    ];

    // This function is used to filter the patents based on the user's role
    public function scopeVisibleTo($query, $user)
    {
        if ($user->role !== 'Superadmin') {
            $userId = $user->id;

            return $query->whereIn('person_in_charge', $userId);
        }

        return $query;
    }

    // This function is used to get the paper associated with this paten
    // It defines a one-to-one relationship between the Paten and Paper models
    public function paper()
    {
        return $this->belongsTo(Paper::class, 'paper_id');
    }

    // This function is used to get the user associated with this paten
    // It defines a one-to-many relationship between the Paten and User models
    public function employee()
    {
        return $this->belongsTo(User::class, 'person_in_charge');
    }

    // This function is used to get the paten maintenance records associated with this paten
    // It defines a one-to-many relationship between the Paten and PatenMaintenance models
    public function patenMaintenance()
    {
        return $this->hasMany(PatentMaintenance::class, 'patent_id', 'id');
    }

    // This function is used to know if the paten has a draft
    // It checks if the draft_paten attribute is not null
    public function hasDraft() {
        return $this->draft_paten === null;
    }

    // This function is used to know if the paten has a ownership letter
    // It checks if the ownership letter is not null
    public function hasOwnershipLetter() {
        return $this->ownership_letter === null;
    }

    // This function is used to know if the paten has a statement of transfer rights
    // It checks if the statement_of_transfer_rights attribute is not null
    public function hasStatementOfTransferRights() {
        return $this->statement_of_transfer_rights === null;
    }
}