<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


class CustomBenefitFinancial extends Model
{
    use HasFactory;
    protected $table = 'custom_benefit_financials';
    protected $fillable = [
        'name_benefit',
        'is_deleted'
    ];
    protected static function booted()
    {
        static::addGlobalScope('not_deleted', function (Builder $builder) {
            $builder->where('is_deleted', false);
        });
    }
    public static function withDeleted()
    {
        return self::withoutGlobalScope('not_deleted');
    }

    public function papers()
    {
        return $this->hasMany(PvtCustomBenefit::class, 'custom_benefit_financial_id');
    }


}
