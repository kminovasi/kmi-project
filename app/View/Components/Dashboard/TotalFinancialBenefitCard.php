<?php

namespace App\View\Components\Dashboard;

use Illuminate\View\Component;
use App\Models\Paper;
use Carbon\Carbon;

class TotalFinancialBenefitCard extends Component
{
    public $financialBenefits;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->financialBenefits = $this->getTotalFinancialBenefitPerYear();
    }

    /**
     * Get total financial benefit per year for the last 4 years
     *
     * @return array
     */
    private function getTotalFinancialBenefitPerYear()
    {
        $currentYear = Carbon::now()->year;
        $benefits = [];

        for ($year = $currentYear - 3; $year <= $currentYear; $year++) {
            $totalBenefit = Paper::where('status', 'accepted by innovation admin')
                ->whereYear('created_at', $year)
                ->sum('financial');

            $benefits[] = [
                'year' => $year,
                'total' => number_format($totalBenefit, 0, ',', '.')
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
            'financialBenefits' => $this->financialBenefits
        ]);
    }
}
