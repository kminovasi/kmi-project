<?php

namespace App\View\Components\Dashboard;

use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;

class InnovationStatusTotal extends Component
{
    public $totals;
    public $percentages;

    public function __construct()
    {
        $this->calculateTotals();
    }

    private function calculateTotals()
    {
        // Mengambil total untuk setiap status
        $this->totals = DB::table('papers')
            ->select('status_inovasi', DB::raw('count(*) as total'))
            ->whereIn('status_inovasi', ['Not Implemented', 'Implemented', 'Progress'])
            ->groupBy('status_inovasi')
            ->pluck('total', 'status_inovasi')
            ->toArray();

        // Menghitung persentase
        $totalPapers = array_sum($this->totals);
        $this->percentages = [];

        foreach ($this->totals as $status => $total) {
            $this->percentages[$status] = $totalPapers > 0
                ? round(($total / $totalPapers) * 100, 1)
                : 0;
        }

        // Memastikan semua status memiliki nilai
        $defaultStatuses = ['Not Implemented', 'Implemented', 'Progress'];
        foreach ($defaultStatuses as $status) {
            if (!isset($this->totals[$status])) {
                $this->totals[$status] = 0;
                $this->percentages[$status] = 0;
            }
        }
    }

    public function render()
    {
        return view('components.dashboard.innovation-status-total');
    }
}
