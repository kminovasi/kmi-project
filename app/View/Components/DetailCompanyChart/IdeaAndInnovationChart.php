<?php

namespace App\View\Components\DetailCompanyChart;

use App\Models\Company;
use Illuminate\View\Component;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Schema;

class IdeaAndInnovationChart extends Component
{
    public $companyId;
    public $directorateData;
    public $organizationUnit;
    public $year;

    public function __construct($companyId, $organizationUnit, $year = null)
    {
        $this->companyId = $companyId;
        $this->year = $year;
        $this->organizationUnit = $organizationUnit === null ? 'directorate_name' : $organizationUnit;
        $getCompanyCode = Company::select('company_code')->where('id', $companyId)->first();
        $this->directorateData = $this->getDirectorateData($getCompanyCode->company_code, $this->organizationUnit);
    }

    public function render()
    {
        return view('components.detail-company-chart.idea-and-innovation-chart', [
            'directorateData' => $this->directorateData,
            'organizationUnit' => $this->organizationUnit,
        ]);
    }

    private function getDirectorateData($companyCode, $organizationUnit)
    {
        // Validate that the organization unit column exists
        if (!Schema::hasColumn('users', $organizationUnit)) {
            \Log::error("Column {$organizationUnit} does not exist in users table");
            return collect([]);
        }

        $allDirectorates = User::where('company_code', $companyCode)
            ->whereNotNull($organizationUnit)
            ->where($organizationUnit, '!=', '')
            ->where($organizationUnit, '!=', '-')
            ->distinct()
            ->pluck($organizationUnit, $organizationUnit);

        $ideaCounts = $this->getCountsByStatus($companyCode, 'Not Implemented', $organizationUnit);
        $innovationCounts = $this->getCountsByStatus($companyCode, ['Implemented', 'Progress'], $organizationUnit);

        return $allDirectorates->map(function ($organizationUnit) use ($ideaCounts, $innovationCounts) {
            $totalIdeas = $ideaCounts->get($organizationUnit, 0);
            $totalInnovations = $innovationCounts->get($organizationUnit, 0);

            if ($totalIdeas > 0 || $totalInnovations > 0) {
                return (object)[
                    'directorate_name' => $organizationUnit,
                    'total_ideas' => $totalIdeas,
                    'total_innovations' => $totalInnovations
                ];
            }
            return null;
        })->filter();
    }

    private function getCountsByStatus($companyCode, $status, $organizationUnit)
    {
        try {
            return DB::table('users')
                ->join('pvt_members', 'users.employee_id', '=', 'pvt_members.employee_id')
                ->join('teams', 'pvt_members.team_id', '=', 'teams.id')
                ->join('papers', 'teams.id', '=', 'papers.team_id')
                ->where('users.company_code', $companyCode)
                ->whereIn('papers.status_inovasi', (array)$status)
                ->whereYear('papers.created_at', $this->year)
                ->whereNotNull('users.' . $organizationUnit)
                ->where('users.' . $organizationUnit, '!=', '')
                ->groupBy('users.' . $organizationUnit)
                ->select('users.' . $organizationUnit, DB::raw('COUNT(DISTINCT papers.id) as total'))
                ->pluck('total', $organizationUnit);
        } catch (\Exception $e) {
            \Log::error('Error in getCountsByStatus: ' . $e->getMessage());
            return collect([]); // Return empty collection in case of error
        }
    }
}
