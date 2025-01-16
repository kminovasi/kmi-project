<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PvtCustomBenefit extends Model
{
    use HasFactory;
    protected $table = 'pvt_custom_benefits';
    protected $fillable = [
        'custom_benefit_financial_id',
        'paper_id',
        'value',
    ];


    public function customBenefitFinancial()
    {
        return $this->belongsTo(CustomBenefitFinancial::class, 'custom_benefit_financial_id');
    }

    // Tambahkan relasi ke model Paper (jika diperlukan)
    public function paper()
    {
        return $this->belongsTo(Paper::class, 'paper_id');
    }
}
