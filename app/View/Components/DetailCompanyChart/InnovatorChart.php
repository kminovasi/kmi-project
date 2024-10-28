<?php

namespace App\View\Components\DetailCompanyChart;

use Illuminate\View\Component;
use App\Models\PvtMember;
use App\Models\Event;
use App\Models\PvtEventTeam;
use App\Models\Team;

class InnovatorChart extends Component
{
    public $companyId;
    public $companyCode;
    public $selectedYear;
    public $chartData;
    public $chartId;

    public function __construct($companyId, $companyCode, $selectedYear)
    {
        $this->companyId = $companyId;
        $this->companyCode = $companyCode;
        $this->selectedYear = $selectedYear;
        $this->chartId = 'innovatorChart_' . $this->companyId;
        $this->chartData = $this->getChartData();
    }

    private function getChartData()
    {
        $teamIds = Team::where('company_code', $this->companyCode)
            ->whereYear('created_at', $this->selectedYear)
            ->pluck('id');

        // Hitung total inovator per kategori
        $innovatorsPerCategory = PvtMember::select('status', 'team_id')
            ->whereIn('team_id', $teamIds)
            ->whereIn('status', ['leader', 'member'])
            ->with('team.category') // Pastikan untuk memuat relasi kategori
            ->get()
            ->groupBy(function ($item) {
                return $item->team->category->category_name; // Ganti dengan nama kategori yang sesuai
            });

        // Hitung total inovator untuk setiap kategori dan siapkan warna acak
        $categoryCounts = [];
        $colors = []; // Array untuk menyimpan warna acak
        foreach ($innovatorsPerCategory as $category => $members) {
            $categoryCounts[$category] = $members->count();
            $colors[$category] = $this->generateRandomColor(); // Generate warna acak untuk setiap kategori
        }

        return [
            'labels' => array_keys($categoryCounts),
            'datasets' => [
                [
                    'label' => 'Total Innovators per Category',
                    'data' => array_values($categoryCounts),
                    'backgroundColor' => array_values($colors), // Menggunakan warna acak
                    'borderColor' => array_values($colors), // Menggunakan warna acak untuk border
                    'borderWidth' => 1
                ]
            ]
        ];
    }

    // Fungsi untuk menghasilkan warna acak dalam format rgba
    private function generateRandomColor()
    {
        $r = rand(0, 255);
        $g = rand(0, 255);
        $b = rand(0, 255);
        return "rgba($r, $g, $b)"; // Mengembalikan warna dengan alpha untuk background
    }

    public function render()
    {
        return view('components.detail-company-chart.innovator-chart');
    }
}
