<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateDocumentPatent extends Model
{
    use HasFactory;
    protected $table = 'template_document_patent';
    protected $fillable = [
        'draft_paten',
        'ownership_letter',
        'statement_of_transfer_rights'
    ];
}