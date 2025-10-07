<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paper extends Model
{
    use HasFactory;
    protected $table = 'papers';
    protected $fillable = [
        'innovation_title',
        'inovasi_lokasi',
        'step_1',
        'step_2',
        'step_3',
        'step_4',
        'step_5',
        'step_6',
        'step_7',
        'step_8',
        'full_paper',
        'financial',
        'potential_benefit',
        'file_review',
        'non_financial',
        'team_id',
        'metodologi_paper_id', 
        'abstract',
        'problem',
        'main_cause',
        'solution',
        'innovation_photo',
        'proof_idea',
        'status_inovasi',
        'potensi_replikasi',
        'status',
        'status_event',
        'rejection_comments',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'financial'         => 'string',
        'potential_benefit' => 'string',
        'non_financial'     => 'string',
    ];

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($paper) {
            if ($paper->isDirty('full_paper')) {
                $paper->full_paper_updated_at = now();
            }
        });
    }

    public function updateAndHistory(array $datas = [], $activity = null)
    {

        if ($activity == null) {
            $flag_create_or_update = '0';
            $keys = array_keys($datas);
            foreach ($datas as $key_data => $data) {
                if (empty($this->$key_data))
                    $flag_create_or_update = 1;
                elseif (!$flag_create_or_update)
                    $flag_create_or_update = 0;

                $this->$key_data = $data;
            }

            if ($flag_create_or_update)
                $status = 'created';
            else
                $status = 'updated';

            $activity = $status;
            foreach ($keys as $key) {
                $activity .= " " . $key;
            }
        } else {
            $status = explode(" ", $activity)[0];
        }

        $this->update();

        History::create([
            'team_id'  => $this->team_id,
            'activity' => $activity,
            'status'   => $status
        ]);
    }

    public function setFinancialAttribute($value)
    {
        if ($value === null || $value === '') {
            $this->attributes['financial'] = null;
            return;
        }
        $digits = preg_replace('/\D+/', '', (string)$value); 
        $this->attributes['financial'] = ($digits === '') ? null : $digits; 
    }

    public function setPotentialBenefitAttribute($value)
    {
        if ($value === null || $value === '') {
            $this->attributes['potential_benefit'] = null;
            return;
        }
        $digits = preg_replace('/\D+/', '', (string)$value);
        $this->attributes['potential_benefit'] = ($digits === '') ? null : $digits; // STRING
    }

    private function formatRibuanString(?string $digits): string
    {
        if ($digits === null || $digits === '') return '';
        $digits = preg_replace('/\D+/', '', $digits);
        if ($digits === '') return '';

        $rev = strrev($digits);
        $chunks = str_split($rev, 3);
        return strrev(implode('.', $chunks)); 
    }

    public function getFinancialFormattedAttribute()
    {
        $raw = $this->attributes['financial'] ?? null; 
        return $this->formatRibuanString($raw);
    }

    public function getPotentialBenefitFormattedAttribute()
    {
        $raw = $this->attributes['potential_benefit'] ?? null; 
        return $this->formatRibuanString($raw);
    }

    public function documentSupport()
    {
        return $this->hasMany(DocumentSupport::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function customBenefits()
    {
        return $this->hasMany(PvtCustomBenefit::class);
    }

    public function metodologiPaper()
    {
        return $this->belongsTo(MetodologiPaper::class);
    }

    // This function is used to get the paten associated with this paper
    // It defines a one-to-one relationship between the Paper and Paten models
    public function paten()
    {
        return $this->hasOne(Patent::class, 'paper_id', 'id');
    }
}
