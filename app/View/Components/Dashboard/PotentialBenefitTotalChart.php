<?php

namespace App\View\Components\Dashboard;

use App\Models\Company;
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

        $companies = Company::with(['teams.paper' => function ($query) use ($years) {
            $query->where('status', 'accepted by innovation admin')
                ->whereBetween('created_at', [
                    now()->subYears(4)->startOfYear(),
                    now()->endOfYear()
                ]);
        }])->get();

        $this->chartData = [
            'labels' => [], // Nama perusahaan
            'datasets' => [], // Dataset untuk setiap tahun
            'logos' => [] // Path logo perusahaan
        ];

        // Warna untuk setiap tahun
        $colors = ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF"];

        foreach ($years as $index => $year) {
            $this->chartData['datasets'][] = [
                'label' => $year,
                'backgroundColor' => $colors[$index % count($colors)],
                'data' => []
            ];
        }

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
            $financialPerYear = [];
            foreach ($years as $year) {
                $financialPerYear[$year] = $company->teams->reduce(function ($carry, $team) use ($year) {
                    // Gunakan relasi papers
                    $teamFinancial = $team->papers->whereBetween('created_at', [
                        "$year-01-01",
                        "$year-12-31"
                    ])->sum('potential_benefit');

                    return $carry + $teamFinancial;
                }, 0);
            }


            // Tambahkan data ke dataset per tahun
            foreach ($years as $index => $year) {
                $this->chartData['datasets'][$index]['data'][] = $financialPerYear[$year] ?? 0;
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
