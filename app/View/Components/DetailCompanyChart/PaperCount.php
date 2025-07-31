<?php

namespace App\View\Components\DetailCompanyChart;

use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\Event;
use Illuminate\View\Component;

class PaperCount extends Component
{
    public $companyName;
    public $chartData;

    public function __construct($companyId = null)
    {
        $company = Company::select('company_name', 'company_code')->where('id', $companyId)->first();
        $this->companyName = $company->company_name;

        // Ambil tahun yang tersedia dari tabel events
        $availableYears = Event::select('year')
            ->groupBy('year')
            ->orderBy('year', 'ASC')
            ->pluck('year')
            ->toArray();

        $targetCompanyCode = $company->company_code;

        if (in_array($targetCompanyCode, [2000, 7000])) {
            $filteredCodes = [2000, 7000];
        } else {
            $filteredCodes = [$targetCompanyCode];
        }

        // Ambil data team_id, tahun, dan kategori
        $teamYears = DB::table('teams')
            ->join('papers', 'teams.id', '=', 'papers.team_id')
            ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->join('events', 'pvt_event_teams.event_id', '=', 'events.id')
            ->join('categories', 'categories.id', '=', 'teams.category_id')
            ->where('papers.status', 'accepted by innovation admin')
            ->whereIn('events.year', $availableYears)
            ->whereIn('teams.company_code', $filteredCodes)
            ->select('teams.id as team_id', 'events.year', 'categories.category_name')
            ->get();

        // Map dan kelompokkan berdasarkan tahun dan jenis kategori
        $grouped = collect($teamYears)
            ->map(fn($row) => [
                'team_id' => $row['team_id'],
                'year' => $row['year'],
                'type' => strtolower($row['category_name']) === 'idea box' ? 'idea_box' : 'implemented',
            ])
            ->unique(fn($row) => $row['team_id'] . '-' . $row['year']) // 1 tim per tahun
            ->groupBy(fn($row) => $row['year'] . '_' . $row['type']);

        // Inisialisasi hasil akhir
        $teamCountsPerYear = [];
        foreach ($availableYears as $year) {
            $ideaKey = $year . '_idea_box';
            $nonIdeaKey = $year . '_implemented';

            $teamCountsPerYear[$year] = [
                'idea_box' => count($grouped[$ideaKey] ?? []),
                'implemented' => count($grouped[$nonIdeaKey] ?? []),
            ];
        }

        // Format akhir untuk chart
        $this->chartData = json_encode([
            'years' => array_keys($teamCountsPerYear),
            'ideaBoxCounts' => array_column($teamCountsPerYear, 'idea_box'),
            'implementedCounts' => array_column($teamCountsPerYear, 'implemented'),
        ]);
    }

    public function render()
    {
        return view('components.detail-company-chart.paper-count');
    }
}