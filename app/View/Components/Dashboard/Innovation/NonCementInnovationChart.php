<?php

namespace App\View\Components\Dashboard\Innovation;

use App\Models\Team;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;

class NonCementInnovationChart extends Component
{
    
    public $chartData = [
        'labels' => [],
        'implemented' => [],
        'idea_box' => [],
        'logos' => [],
    ];
    public $year;

    public function __construct($year = null)
    {
        $this->year = $year ?? now()->year;

        // Ambil data inovasi berdasarkan perusahaan dan status
        $data = Team::select(
                'teams.company_code',
                'companies.company_name',
                'companies.sort_order',
                DB::raw('EXTRACT(YEAR FROM papers.created_at) as year'),
                DB::raw("SUM(CASE WHEN papers.status_inovasi IN ('Implemented') THEN 1 ELSE 0 END) as implemented"),
                DB::raw("SUM(CASE WHEN papers.status_inovasi IN ('Progress', 'Not Implemented') THEN 1 ELSE 0 END) as idea_box")
            )
            ->join('companies', 'teams.company_code', '=', 'companies.company_code')
            ->join('papers', 'teams.id', '=', 'papers.team_id')
            ->whereYear('papers.created_at', $this->year)
            ->where('companies.group', 'Non Semen')
            ->groupBy('teams.company_code', 'companies.company_name', 'companies.sort_order', DB::raw('EXTRACT(YEAR FROM papers.created_at)'))
            ->orderBy('companies.sort_order')
            ->get();

        // Gabungkan data company_code 7000 ke 2000
        $company2000 = $data->firstWhere('company_code', '2000');
        $company7000 = $data->firstWhere('company_code', '7000');

        if ($company2000 && $company7000) {
            $company2000->implemented += $company7000->implemented;
            $company2000->idea_box += $company7000->idea_box;

            $data = $data->reject(fn($item) => $item->company_code === '7000');
        }

        // Siapkan chartData
        foreach ($data as $row) {
            $company = $row->company_name;

            $sanitizedCompanyName = preg_replace('/[^a-zA-Z0-9_()]+/', '_', strtolower($company));
            $sanitizedCompanyName = preg_replace('/_+/', '_', $sanitizedCompanyName);
            $sanitizedCompanyName = trim($sanitizedCompanyName, '_');

            $logoPath = public_path("assets/logos/{$sanitizedCompanyName}.png");
            if (!file_exists($logoPath)) {
                $logoPath = asset('assets/logos/pt_semen_indonesia_tbk.png');
            } else {
                $logoPath = asset("assets/logos/{$sanitizedCompanyName}.png");
            }

            $this->chartData['labels'][] = $company;
            $this->chartData['implemented'][] = $row->implemented;
            $this->chartData['idea_box'][] = $row->idea_box;
            $this->chartData['logos'][] = $logoPath;
        }
    }
    
    public function render()
    {
        return view('components.dashboard.innovation.non-cement-innovation-chart', [
            'chartData' => $this->chartData,
        ]);
    }
}