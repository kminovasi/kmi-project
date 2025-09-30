<?php

namespace App\View\Components\Dashboard;

use App\Models\Company;
use App\Models\Paper;
use DB;
use Illuminate\View\Component;

class PotentialBenefitTotal extends Component
{
    public $chartData;
    public $isSuperadmin;
    public $userCompanyCode;
    public $title;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($isSuperadmin, $userCompanyCode)
    {
        $this->isSuperadmin = $isSuperadmin;
        $this->userCompanyCode = $userCompanyCode;
        $this->chartData = $this->getChartData();
        if ($isSuperadmin) {
            $this->title = "Semua perusahaan";
        } else {
            $company = Company::where('company_code', $userCompanyCode)->first();
            $companyName = $company->company_name;
            $this->title = $companyName;
        }
    }

    private function getChartData()
    {
        $query = Paper::select(
            DB::raw('EXTRACT(YEAR FROM papers.created_at) as year'),
            DB::raw('SUM(papers.potential_benefit) as total_financial')
        )
            ->join('teams', 'papers.team_id', '=', 'teams.id');

        // Filter berdasarkan company code jika bukan superadmin
        if (!$this->isSuperadmin) {
            $query->where('teams.company_code', $this->userCompanyCode);
        }

        $yearlyTotals = $query->groupBy(DB::raw('EXTRACT(YEAR FROM papers.created_at)'))
            ->orderBy('year')
            ->get();

        return [
            'labels' => $yearlyTotals->pluck('year'),
            'data' => $yearlyTotals->pluck('total_financial'),
        ];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dashboard.potential-benefit-total', [
            'chartData' => $this->chartData,
            'title' => $this->title
        ]);
    }
}
