<?php

namespace App\View\Components\Dashboard;

use Illuminate\View\Component;
use Carbon\Carbon;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Log;

class TotalCompanyInnovatorChart extends Component
{
    public $chartData;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->chartData = $this->generateChartData();
    }

    /**
     * Generate chart data for the component.
     *
     * @return array
     */
    private function generateChartData()
    {
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 3, $currentYear);
    
        // Ambil data inovator dari pvt_members
        $rawData = DB::table('pvt_members')
            ->join('teams', 'teams.id', '=', 'pvt_members.team_id')
            ->join('papers', 'papers.team_id', '=', 'teams.id')
            ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            ->join('events', 'events.id', '=', 'pvt_event_teams.event_id')
            ->join('companies', 'companies.company_code', '=', 'teams.company_code')
            ->leftJoin('users', 'users.employee_id', '=', 'pvt_members.employee_id')
            ->where('papers.status', 'accepted by innovation admin')
            ->where('pvt_members.status', '!=', 'gm')
            ->whereIn(DB::raw('YEAR(events.year)'), $years)
            ->select(
                'companies.company_code',
                'companies.company_name',
                DB::raw('YEAR(events.year) as year'),
                'pvt_members.employee_id',
                'teams.id as team_id'
            )
            ->distinct()
            ->get();

              Log::debug('[TotalCompanyInnovatorChart] rawData', [
                'count' => $rawData->count(),
                'sample' => $rawData->take(5)->toArray(),
            ]);
            
        // Kelompokkan data berdasarkan perusahaan dan tahun
        $groupedData = [];
        foreach ($rawData as $row) {
            $companyKey = in_array($row->company_code, [2000, 7000]) ? 2000 : $row->company_code;
            $companyName = in_array($row->company_code, [2000, 7000]) ? 'PT Semen Indonesia (Persero) Tbk' : $row->company_name;

            $year = $row->year;
    
            if (!isset($groupedData[$companyKey])) {
                $groupedData[$companyKey] = [
                    'company_name' => $companyName,
                    'employee_keys' => []
                ];
            }
    
            if (!isset($groupedData[$companyKey]['employee_keys'][$year])) {
                $groupedData[$companyKey]['employee_keys'][$year] = [];
            }
    
            $uniqueKey = $row->employee_id . '-' . $row->team_id;
    
            if (!in_array($uniqueKey, $groupedData[$companyKey]['employee_keys'][$year])) {
                $groupedData[$companyKey]['employee_keys'][$year][] = $uniqueKey;
            }
        }
    
        // Hitung total anggota dari ph2_members yang timnya ikut event 2024
        $ph2Data = DB::table('ph2_members')
            ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'ph2_members.team_id')
            ->join('events', 'events.id', '=', 'pvt_event_teams.event_id')
            ->leftJoin('teams', 'teams.id', '=', 'ph2_members.team_id')
            ->join('papers', 'papers.team_id', '=', 'teams.id')
            ->join('companies', 'companies.company_code', '=', 'teams.company_code')
            ->where('papers.status', 'accepted by innovation admin')
            ->whereIn('events.year', $years)
            ->select(
                'events.year as year',
                'companies.company_code',
                'companies.company_name',
                'ph2_members.name',
                'ph2_members.team_id'
            )
            ->distinct()
            ->get();

        Log::debug('[TotalCompanyInnovatorChart] ph2Data', [
        'count' => $ph2Data->count(),
        'sample' => $ph2Data->take(5)->toArray(),
    ]);
            
        foreach ($ph2Data as $row) {
            $companyKey = in_array($row->company_code, [2000, 7000]) ? 2000 : $row->company_code;
            $companyName = in_array($row->company_code, [2000, 7000]) ? 'PT Semen Indonesia (Persero) Tbk' : $row->company_name;

            $year = $row->year;
            
            if (!isset($groupedData[$companyKey])) {
                $groupedData[$companyKey] = [
                    'company_name' => $companyName,
                    'employee_keys' => []
                ];
            }
            
            if (!isset($groupedData[$companyKey]['employee_keys'][$year])) {
                $groupedData[$companyKey]['employee_keys'][$year] = [];
            }
            
            $uniqueKey = $row->name . '-' . $row->team_id;
            
            if (!in_array($uniqueKey, $groupedData[$companyKey]['employee_keys'][$year])) {
                $groupedData[$companyKey]['employee_keys'][$year][] = $uniqueKey;
            }
        }
    
        // Persiapkan struktur chart
        $chartData = [
            'labels' => [],
            'datasets' => [],
            'logos' => [],
            'company_ids' => [],  // <— TAMBAHKAN INI
        ];
    
        foreach ($years as $i => $year) {
            $chartData['datasets'][] = [
                'label' => $year,
                'backgroundColor' => ["#FF6384", "#36A2EB", "#FFCE56", "#9966FF"][$i % 5],
                'data' => []
            ];
        }
    
        foreach ($groupedData as $companyCode => $companyData) {
            $chartData['labels'][] = $companyData['company_name'];
            $chartData['company_ids'][] = $companyCode; 
        
            // Sanitasi nama file logo
            $sanitizedCompanyName = preg_replace('/[^a-zA-Z0-9_()]+/', '_', strtolower($companyData['company_name']));
            $sanitizedCompanyName = preg_replace('/_+/', '_', $sanitizedCompanyName);
            $sanitizedCompanyName = trim($sanitizedCompanyName, '_');
            $logoPath = public_path('assets/logos/' . $sanitizedCompanyName . '.png');
        
            $chartData['logos'][] = file_exists($logoPath)
                ? asset('assets/logos/' . $sanitizedCompanyName . '.png')
                : asset('assets/logos/pt_semen_indonesia_tbk.png');
        
            
            foreach ($years as $i => $year) {
                $jumlah = count($companyData['employee_keys'][$year] ?? []);
                $chartData['datasets'][$i]['data'][] = $jumlah;
            }
        }
         Log::debug('[TotalCompanyInnovatorChart] chartData summary', [
        'labels_count' => count($chartData['labels']),
        'datasets_count' => count($chartData['datasets']),
        'logos_count' => count($chartData['logos']),
        'sample_labels' => array_slice($chartData['labels'], 0, 5),
        'first_dataset' => $chartData['datasets'][0]['data'] ?? [],
    ]);
        return $chartData;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
   public function render()
{
    // 🔎 log debug isi chartData
    if (empty($this->chartData) || empty($this->chartData['labels'])) {
        Log::debug('[TotalCompanyInnovatorChart][render] chartData KOSONG', [
            'chartData' => $this->chartData,
        ]);
    } else {
        Log::debug('[TotalCompanyInnovatorChart][render] chartData TERISI', [
            'labels_count'   => count($this->chartData['labels']),
            'datasets_count' => count($this->chartData['datasets']),
            'logos_count'    => count($this->chartData['logos']),
            'sample_labels'  => array_slice($this->chartData['labels'], 0, 5),
        ]);
    }

    return view('components.dashboard.total-company-innovator-chart', [
        'chartData' => $this->chartData,
    ]);
}
}