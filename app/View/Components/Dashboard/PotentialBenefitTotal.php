<?php

namespace App\View\Components\Dashboard;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\View\Component;

class PotentialBenefitTotalChart extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $chartData;
    public function __construct()
    {
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 3, $currentYear);
        $isSuperadmin = Auth::user()->role === 'Superadmin';
        $company_code = Auth::user()->company_code;
    
        // Ambil perusahaan beserta teams dan papers yang diterima
        $companiesQuery = Company::with([
            'teams.papers' => function ($query) {
                $query->where('status', 'accepted by innovation admin');
            },
            'teams.events' => function ($query) use ($years) {
                $query->whereIn('events.year', $years);
                $query->where('events.status', 'finish');
            }
        ]);
    
        if (!$isSuperadmin) {
            $companiesQuery->where('company_code', $company_code);
        }
    
        $companies = $companiesQuery->get();
    
        $this->chartData = [
            'labels' => [],
            'datasets' => [],
            'logos' => [],
            'isSuperadmin' => $isSuperadmin,
        ];
    
        $colors = ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0"];
    
        foreach ($years as $i => $year) {
            $this->chartData['datasets'][] = [
                'label' => $year,
                'backgroundColor' => $colors[$i % count($colors)],
                'data' => [],
            ];
        }
    
        // Koleksi perusahaan digabung
        $mergedCompanies = collect();
    
        foreach ($companies as $company) {
            $code = $company->company_code;
            $actualCode = ($code == '7000') ? '2000' : $code;
    
            if (!$mergedCompanies->has($actualCode)) {
                $companyObj = new \stdClass();
                $companyObj->company_name = ($actualCode == '2000')
                    ? 'PT Semen Indonesia (Persero)'
                    : $company->company_name;
                $companyObj->teams = collect();
                $mergedCompanies->put($actualCode, $companyObj);
            }
    
            $mergedCompanies[$actualCode]->teams = $mergedCompanies[$actualCode]->teams->merge($company->teams);
        }
    
        $mergedCompanies = $mergedCompanies->filter(function ($company) {
            return $company->teams->reduce(function ($carry, $team) {
                return $carry + $team->papers->sum('potential_benefit');
            }, 0) > 0;
        });
    
        // Proses chartData
        foreach ($mergedCompanies as $company) {
            $companyName = $company->company_name;
            $teams = $company->teams;
    
            // Proses nama logo
            $sanitizedCompanyName = preg_replace('/[^a-zA-Z0-9_()]+/', '_', strtolower($companyName));
            $sanitizedCompanyName = preg_replace('/_+/', '_', $sanitizedCompanyName);
            $sanitizedCompanyName = trim($sanitizedCompanyName, '_');
    
            $logoPath = public_path('assets/logos/' . $sanitizedCompanyName . '.png');
            $logoPath = file_exists($logoPath)
                ? asset('assets/logos/' . $sanitizedCompanyName . '.png')
                : asset('assets/logos/pt_semen_indonesia_tbk.png');
    
            $this->chartData['labels'][] = $companyName;
            $this->chartData['logos'][] = $logoPath;
    
            foreach ($years as $index => $year) {
                $financialTotal = $teams->reduce(function ($carry, $team) use ($year) {
                    // Cek apakah tim ini ikut event pada tahun tersebut
                    $hasEventInYear = $team->events->contains(function ($event) use ($year) {
                        return $event->year == $year && $event->status == 'finish';
                    });
            
                    if (!$hasEventInYear) {
                        return $carry;
                    }
            
                    // Jika ya, tambahkan semua paper-nya
                    return $carry + $team->papers->sum('potential_benefit');
                }, 0);
            
                $this->chartData['datasets'][$index]['data'][] = $financialTotal;
            }
    
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dashboard.potential-benefit-total-chart', [
            'chartDataTotalPotentialBenefit' => $this->chartData
        ]);
    }
}