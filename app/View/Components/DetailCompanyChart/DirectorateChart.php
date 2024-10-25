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

    public function __construct($companyId)
    {
        $this->companyId = $companyId;
        $getCompanyCode = Company::select('company_code')->where('id', $companyId)->first();
        $this->directorateData = $this->getDirectorateData($getCompanyCode->company_code);
    }

    public function render()
    {
        return view('components.detail-company-chart.directorate-chart', [
            'directorateData' => $this->directorateData
        ]);
    }

    private function getDirectorateData($companyCode)
    {
        $allDirectorates = User::where('company_code', $companyCode)
            ->distinct()
            ->pluck('directorate_name');

        $ideaCounts = $this->getCountsByStatus($companyCode, 'Not Implemented');
        $innovationCounts = $this->getCountsByStatus($companyCode, ['Implemented', 'Progress']);

        return $allDirectorates->map(function ($directorate) use ($ideaCounts, $innovationCounts) {
            return (object)[
                'directorate_name' => $directorate,
                'total_ideas' => $ideaCounts->get($directorate, 0),
                'total_innovations' => $innovationCounts->get($directorate, 0)
            ];
        });
    }

    private function getCountsByStatus($companyCode, $status)
    {
        return DB::table('users')
            ->join('pvt_members', 'users.employee_id', '=', 'pvt_members.employee_id')
            ->join('teams', 'pvt_members.team_id', '=', 'teams.id')
            ->join('papers', 'teams.id', '=', 'papers.team_id')
            ->where('users.company_code', $companyCode)
            ->whereIn('papers.status_inovasi', (array)$status)
            ->groupBy('users.directorate_name')
            ->select('users.directorate_name', DB::raw('COUNT(DISTINCT papers.id) as total'))
            ->pluck('total', 'directorate_name');
    }
}
