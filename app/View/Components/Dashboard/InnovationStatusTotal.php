<?php

namespace App\View\Components\Dashboard;

use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;

class InnovationStatusTotal extends Component
{
    public $totals;
    public $percentages;
    public $isSuperadmin;
    public $userCompanyCode;

    public function __construct($isSuperadmin, $userCompanyCode)
    {
        $this->isSuperadmin = $isSuperadmin;
        $this->userCompanyCode = $userCompanyCode;
        $this->calculateTotals();
    }

    private function calculateTotals()
    {
        $query = DB::table('papers')
            ->select('papers.status_inovasi', DB::raw('count(*) as total'))
            ->join('teams', 'papers.team_id', '=', 'teams.id')
            ->whereIn('papers.status_inovasi', ['Not Implemented', 'Implemented', 'Progress']);

        if (!$this->isSuperadmin) {
            $query->where('teams.company_code', $this->userCompanyCode);
        }

        $results = $query->groupBy('papers.status_inovasi')
            ->pluck('total', 'status_inovasi')
            ->toArray();

        // Menghitung persentase
        $totalPapers = array_sum($results);
        $this->totals = [];
        $this->percentages = [];

        $defaultStatuses = ['Not Implemented', 'Implemented', 'Progress'];
        foreach ($defaultStatuses as $status) {
            $total = $results[$status] ?? 0;
            $this->totals[$status] = $total;
            $this->percentages[$status] = $totalPapers > 0
                ? round(($total / $totalPapers) * 100, 1)
                : 0;
        }
    }

    public function render()
    {
        return view('components.dashboard.innovation-status-total');
    }
}
