<?php

namespace App\View\Components\Dashboard;

use Illuminate\View\Component;
use App\Models\Paper;
use Carbon\Carbon;

class TotalFinancialBenefitCard extends Component
{
    public $financialBenefits;
    public $potentialBenefits;
    public $isSuperadmin;
    public $userCompanyCode;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($isSuperadmin, $userCompanyCode)
    {
        $this->isSuperadmin = $isSuperadmin;
        $this->userCompanyCode = $userCompanyCode;
        $this->financialBenefits = $this->getTotalBenefitPerYear('financial');
        $this->potentialBenefits = $this->getTotalBenefitPerYear('potential_benefit');
    }

    /**
     * Get total benefit per year for the last 4 years
     *
     * @param string $benefitType
     * @return array
     */
    private function getTotalBenefitPerYear($benefitType)
    {
        $currentYear = Carbon::now()->year;
        $benefits = [];

        for ($year = $currentYear - 3; $year <= $currentYear; $year++) {
            $query = Paper::join('teams', 'papers.team_id', '=', 'teams.id')
                ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                ->join('events', 'pvt_event_teams.event_id', '=', 'events.id')
                ->where('papers.status', 'accepted by innovation admin')
                ->where('events.year', $year)
                ->where('events.status', 'finish');

            // Filter data based on user's company code if not a superadmin
            if (!$this->isSuperadmin) {
                $query->whereHas('team', function ($q) {
                    $q->where('company_code', $this->userCompanyCode);
                });
            }

            $totalBenefit = $query->sum($benefitType);

            $benefits[] = [
                'year' => $year,
                'total' => $totalBenefit
            ];
        }

        return $benefits;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dashboard.total-financial-benefit-card', [
            'financialBenefits' => $this->financialBenefits,
            'potentialBenefits' => $this->potentialBenefits
        ]);
    }
}