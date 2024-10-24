<?php

namespace App\View\Components\DetailCompanyChart;

use App\Models\Company;
use App\Models\Event;
use App\Models\Paper;
use Illuminate\View\Component;

class PaperCount extends Component
{
    public $companyName;
    public $chartData;

    public function __construct($companyId = null)
    {
        $company = Company::select('company_name', 'company_code')->where('id', $companyId)->first();
        $this->companyName = $company->company_name;

        $availableYears = Event::select('year')
            ->groupBy('year')
            ->orderBy('year', 'ASC')
            ->pluck('year')
            ->toArray();

        $yearlyPapers = [];

        foreach ($availableYears as $year) {
            $totalPapers = Paper::whereHas('team', function ($query) use ($company) {
                $query->where('company_code', $company->company_code);
            })
                ->whereYear('created_at', $year)
                ->count();

            $yearlyPapers[$year] = $totalPapers;
        }

        $this->chartData = json_encode([
            'years' => array_keys($yearlyPapers),
            'paperCounts' => array_values($yearlyPapers),
        ]);
    }

    public function render()
    {
        return view('components.detail-company-chart.paper-count');
    }
}
