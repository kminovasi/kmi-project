<?php

namespace App\View\Components\Dashboard;

use Log;
use App\Models\Paper;
use Illuminate\Support\Carbon;
use Illuminate\View\Component;

class Benefit extends Component
{
    public $year;
    public $charts = [
        'labels' => [],
        'data' => [],
        'logos' => []
    ];
    public $isSuperadmin;
    public $userCompanyCode;
    public $availableYears;

    public function __construct($year = null, $isSuperadmin, $userCompanyCode)
    {
        $this->year = $year ?? date('Y');
        $this->isSuperadmin = $isSuperadmin;
        $this->userCompanyCode = $userCompanyCode;
        $yearNow = Carbon::now()->year;

        // Status benefit yang sudah disetujui
        $acceptedStatuses = [
            'accepted by innovation admin',
        ];

        // Mengambil data total benefit untuk setiap perusahaan berdasarkan status yang sudah disetujui
        $query = Paper::join('teams', 'papers.team_id', '=', 'teams.id')
            ->join('companies', 'teams.company_code', '=', 'companies.company_code')
            ->selectRaw('companies.company_name, SUM(papers.financial + papers.potential_benefit) as total_benefit')
            ->whereIn('papers.status', $acceptedStatuses)
            ->whereYear('papers.created_at', $yearNow)
            ->groupBy('companies.company_name')
            ->orderBy('total_benefit', 'DESC');

        // Filter data based on user's company code if not a superadmin
        if (!$this->isSuperadmin) {
            $query->where('teams.company_code', $this->userCompanyCode);
        }

        $data = $query->get();

        // Menyiapkan data untuk Chart.js
        foreach ($data as $row) {
            $company = $row->company_name;

            // Sanitasi nama perusahaan untuk dijadikan nama file logo
            $sanitizedCompanyName = preg_replace('/[^a-zA-Z0-9_()]+/', '_', strtolower($company));
            $sanitizedCompanyName = preg_replace('/_+/', '_', $sanitizedCompanyName);
            $sanitizedCompanyName = trim($sanitizedCompanyName, '_');

            $logoPath = public_path('assets/logos/' . $sanitizedCompanyName . '.png');

            // Pengecekan apakah file logo ada, jika tidak gunakan default
            if (!file_exists($logoPath)) {
                $logoPath = asset('assets/logos/pt_semen_indonesia_tbk.png'); // Logo default
            } else {
                $logoPath = asset('assets/logos/' . $sanitizedCompanyName . '.png'); // Logo perusahaan
            }

            // Simpan data label dan total benefit
            $this->charts['labels'][] = $company;
            $this->charts['data'][] = $row->total_benefit;

            // Simpan path logo
            $this->charts['logos'][] = $logoPath;
        }

        // Get available years for the dropdown
        $this->availableYears = Paper::selectRaw('EXTRACT(YEAR FROM created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
    }

    public function render()
    {
        return view('components.dashboard.benefit', [
            'charts' => $this->charts,
            'year' => $this->year,
            'availableYears' => $this->availableYears,
        ]);
    }
}