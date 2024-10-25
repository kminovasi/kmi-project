<?php

namespace App\View\Components\DetailCompanyChart;

use App\Models\Company;
use Illuminate\View\Component;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DirectorateChart extends Component
{
    public $companyId;
    public $directorateData;
    public $organizationUnit;

    public function __construct($companyId, $organizationUnit)
    {
        $this->companyId = $companyId;
        $this->organizationUnit = $organizationUnit;
        $getCompanyCode = Company::select('company_code')->where('id', $companyId)->first();
        $this->directorateData = $this->getDirectorateData($getCompanyCode->company_code, $organizationUnit);
    }

    public function render()
    {
        return view('components.detail-company-chart.directorate-chart', [
            'directorateData' => $this->directorateData,
            'organizationUnit' => $this->organizationUnit
        ]);
    }

    private function getDirectorateData($companyCode, $organizationUnit)
    {
        $allDirectorates = User::where('company_code', $companyCode)
            ->whereNotNull($organizationUnit)  // Tambahkan filter ini
            ->where($organizationUnit, '!=', '') // Tambahkan filter ini
            ->where($organizationUnit, '!=', '-') // Tambahkan filter ini
            ->distinct()
            ->pluck($organizationUnit, $organizationUnit);

        $ideaCounts = $this->getCountsByStatus($companyCode, 'Not Implemented', $organizationUnit);
        $innovationCounts = $this->getCountsByStatus($companyCode, ['Implemented', 'Progress'], $organizationUnit);

        // Filter dan map data
        $filteredData = $allDirectorates->map(function ($organizationUnit) use ($ideaCounts, $innovationCounts) {
            $totalIdeas = $ideaCounts->get($organizationUnit, 0);
            $totalInnovations = $innovationCounts->get($organizationUnit, 0);

            // Hanya return data jika ada ide atau inovasi
            if ($totalIdeas > 0 || $totalInnovations > 0) {
                return (object)[
                    'directorate_name' => $organizationUnit,
                    'total_ideas' => $totalIdeas,
                    'total_innovations' => $totalInnovations
                ];
            }
            return null;
        })->filter(); // Hapus semua nilai null

        return $filteredData;
    }

    private function getCountsByStatus($companyCode, $status, $organizationUnit)
    {
        return DB::table('users')
            ->join('pvt_members', 'users.employee_id', '=', 'pvt_members.employee_id')
            ->join('teams', 'pvt_members.team_id', '=', 'teams.id')
            ->join('papers', 'teams.id', '=', 'papers.team_id')
            ->where('users.company_code', $companyCode)
            ->whereIn('papers.status_inovasi', (array)$status)
            ->groupBy('users.' . $organizationUnit)
            ->select('users.' . $organizationUnit, DB::raw('COUNT(DISTINCT papers.id) as total'))
            ->pluck('total', $organizationUnit, $organizationUnit);
    }
}
