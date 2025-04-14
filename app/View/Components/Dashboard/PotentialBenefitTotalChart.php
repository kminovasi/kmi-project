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
        $years = range($currentYear - 4, $currentYear);
        $isSuperadmin = Auth::user()->role === 'Superadmin';
        $company_code = Auth::user()->company_code;

        $companiesQuery = Company::with(['teams.paper' => function ($query) use ($currentYear) {
            $query->where('status', 'accepted by innovation admin')
                ->whereBetween('created_at', [
                    now()->startOfYear(),
                    now()->endOfYear()
                ]);
        }]);
        if (!$isSuperadmin) {
            $companiesQuery->where('company_code', $company_code);
        }
        $companies = $companiesQuery->get();

        $this->chartData = [
            'labels' => [], // Nama perusahaan
            'datasets' => [], // Dataset untuk setiap tahun
            'logos' => [], // Path logo perusahaan
            'isSuperadmin' => $isSuperadmin
        ];

        // Warna untuk setiap tahun
        $colors = "#9966FF";

        $this->chartData['datasets'][] = [
            'label' => $currentYear,
            'backgroundColor' => $colors,
            'data' => []
        ];

        foreach ($companies as $company) {
            // Proses nama perusahaan menjadi nama file logo
            $sanitizedCompanyName = preg_replace('/[^a-zA-Z0-9_()]+/', '_', strtolower($company->company_name));
            $sanitizedCompanyName = preg_replace('/_+/', '_', $sanitizedCompanyName);
            $sanitizedCompanyName = trim($sanitizedCompanyName, '_');
            $logoPath = public_path('assets/logos/' . $sanitizedCompanyName . '.png');

            // Cek jika file logo ada, jika tidak gunakan default logo
            if (!file_exists($logoPath)) {
                $logoPath = asset('assets/logos/pt_semen_indonesia_tbk.png'); // Ganti dengan logo default
            } else {
                $logoPath = asset('assets/logos/' . $sanitizedCompanyName . '.png');
            }

            $this->chartData['labels'][] = $company->company_name; // Nama perusahaan
            $this->chartData['logos'][] = $logoPath; // Path logo perusahaan

            // Hitung total financial benefit per tahun
            $financialThisYear = [];
            $financialThisYear = $company->teams->reduce(function ($carry, $team) use ($currentYear) {
                // Gunakan relasi papers
                $teamFinancial = $team->papers->whereBetween('created_at', [
                    "$currentYear-01-01",
                    "$currentYear-12-31"
                ])->sum('potential_benefit');

                return $carry + $teamFinancial;
            }, 0);

                $this->chartData['datasets'][0]['data'][] = $financialThisYear ?? 0;
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