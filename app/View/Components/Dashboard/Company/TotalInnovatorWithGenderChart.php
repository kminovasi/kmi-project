<?php
namespace App\View\Components\Dashboard\Company;

use App\Models\Company;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TotalInnovatorWithGenderChart extends Component
{
    public $chartData;

    /**
     * Create a new component instance.
     *
     * @param int $companyId
     * @return void
     */
    public function __construct($companyId)
    {
        $this->chartData = $this->fetchChartData($companyId);
    }

    /**
     * Fetch chart data for the company.
     *
     * @param int $companyId
     * @return array
     */
    private function fetchChartData($companyId)
    {
        $fourYearsAgo = now()->subYears(5)->startOfYear();
        $companyCode = Company::findOrFail($companyId)->company_code;

        return DB::table('users')
            ->join('pvt_members', 'users.employee_id', '=', 'pvt_members.employee_id')
            ->join('teams', 'pvt_members.team_id', '=', 'teams.id')
            ->join('papers', 'teams.id', '=', 'papers.team_id')
            ->where('users.company_code', $companyCode)
            ->whereIn('pvt_members.status', ['leader', 'member'])
            ->where('teams.created_at', '>=', $fourYearsAgo)
            ->where('papers.status', 'accepted by innovation admin')
            ->select(
                DB::raw('EXTRACT(YEAR FROM teams.created_at) as year'),
                DB::raw('users.gender as gender'),
                DB::raw('COUNT(DISTINCT users.id) as total')
            )
            ->groupBy('year', 'gender')
            ->orderBy('year', 'asc')
            ->get()
            ->groupBy('year')
            ->map(function ($yearData) {
                $male = $yearData->where('gender', 'Male')->sum('total');
                $female = $yearData->where('gender', 'Female')->sum('total');
                return [
                    'laki_laki' => $male,
                    'perempuan' => $female,
                    'total' => $male + $female, // Tambahkan total
                ];
            })
            ->toArray();
    }


    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dashboard.company.total-innovator-with-gender-chart', [
            'chartData' => $this->chartData,
        ]);
    }
}
