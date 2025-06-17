<?php

namespace App\View\Components\Dashboard;

use Illuminate\View\Component;
use App\Models\Company;
use Carbon\Carbon;

class TotalCompanyInnovatorChart extends Component
{
    public $chartData;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->chartData = $this->generateChartData();
    }

    /**
     * Generate chart data for the component.
     *
     * @return array
     */
    private function generateChartData()
    {
        // Tahun sekarang dan 4 tahun terakhir
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 4, $currentYear);

        // Ambil perusahaan beserta jumlah inovator
        $companies = Company::with(['teams.pvtMembers'])->get();

        $chartData = [
            'labels' => [], // Nama perusahaan
            'datasets' => [], // Data inovator per tahun
            'logos' => [] // Path logo perusahaan
        ];

        foreach ($years as $index => $year) {
            $chartData['datasets'][] = [
                'label' => $year,
                'backgroundColor' => ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF"][$index % 5],
                'data' => []
            ];
        }

        foreach ($companies as $company) {
            // Tambahkan nama perusahaan
            $chartData['labels'][] = $company->company_name;

            // Logo perusahaan
            $sanitizedCompanyName = preg_replace('/[^a-zA-Z0-9_()]+/', '_', strtolower($company->company_name));
            $logoPath = public_path('assets/logos/' . $sanitizedCompanyName . '.png');
            if (!file_exists($logoPath)) {
                $logoPath = asset('assets/logos/pt_semen_indonesia_tbk.png'); // Logo default
            } else {
                $logoPath = asset('assets/logos/' . $sanitizedCompanyName . '.png');
            }
            $chartData['logos'][] = $logoPath;

            // Hitung inovator unik per tahun
            foreach ($years as $index => $year) {
                $innovatorCount = $company->teams
                    ->flatMap(fn($team) => $team->pvtMembers)
                    ->filter(fn($member) => $member->created_at->year == $year)
                    ->pluck('employee_id')
                    ->unique()
                    ->count();

                $chartData['datasets'][$index]['data'][] = $innovatorCount;
            }
        }

        return $chartData;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dashboard.total-company-innovator-chart', [
            'chartData' => $this->chartData,
        ]);
    }
}