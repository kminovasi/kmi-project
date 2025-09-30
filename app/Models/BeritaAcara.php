<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeritaAcara extends Model
{
    use HasFactory;
    protected $table = 'berita_acaras';
    protected $fillable = ['event_id', 'no_surat', 'jenis_event', 'penetapan_juara', 'signed_file','rapat_juri_at','rapat_direktur_at'];
}
