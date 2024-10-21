<?php

namespace App\View\Components\Dashboard;

use Illuminate\View\Component;
use App\Models\Team;
use ArielMejiaDev\LarapexCharts\LarapexChart;

class Semen extends Component
{
    public $charts = [];
    public $logos = [];

    public function __construct()
    {
        // Query untuk mendapatkan jumlah innovator dari semua perusahaan setiap tahun
        $data = Team::join('pvt_members', 'teams.id', '=', 'pvt_members.team_id')
                    ->join('companies', 'teams.company_code', '=', 'companies.company_code')
                    ->selectRaw("DATE_PART('year', teams.created_at) as year, companies.company_name, COUNT(pvt_members.id) as total_innovators")
                    ->groupBy('year', 'companies.company_name')
                    ->orderBy('year', 'ASC')
                    ->get();

        // Memisahkan data berdasarkan perusahaan
        $groupedData = $data->groupBy('company_name');

        // Mendapatkan semua tahun yang ada
        $years = [];
        foreach ($groupedData as $company => $dataPerCompany) {
            $years = array_unique(array_merge($years, $dataPerCompany->pluck('year')->toArray()));
        }
        sort($years);

        // Menyiapkan dataset dan membuat chart per perusahaan
        foreach ($groupedData as $company => $dataPerCompany) {
            $totalsPerYear = [];
            foreach ($years as $year) {
                $total = $dataPerCompany->firstWhere('year', $year)->total_innovators ?? 0;
                $totalsPerYear[] = $total;
            }

            // Sanitasi nama perusahaan untuk dijadikan nama file logo
            $sanitizedCompanyName = preg_replace('/[^a-zA-Z0-9_()]+/', '_', strtolower($company)); // Mengganti karakter tidak valid
            $sanitizedCompanyName = preg_replace('/_+/', '_', $sanitizedCompanyName); // Menghapus double underscore
            $sanitizedCompanyName = trim($sanitizedCompanyName, '_');
            $logoPath = public_path('assets/logos/' . $sanitizedCompanyName . '.png');

            // Pengecekan apakah file logo ada, jika tidak gunakan default
            if (!file_exists($logoPath)) {
                $logoPath = asset('assets/logos/pt_semen_indonesia_tbk.png'); // Logo default
            } else {
                $logoPath = asset('assets/logos/' . $sanitizedCompanyName . '.png'); // Logo perusahaan
            }

            // Simpan path logo
            $this->logos[] = $logoPath;

            // Membuat chart untuk perusahaan ini
            $this->charts[] = (new LarapexChart)
                ->barChart()
                ->setXAxis($years)
                ->setDataset([
                    [
                        'name' => $company,
                        'data' => $totalsPerYear,
                    ]
                ])->setWidth(100)->setHeight(300);
        }
    }

    public function render()
    {
        return view('components.dashboard.semen', [
            'charts' => $this->charts,
            'logos' => $this->logos // Mengirimkan array logo ke tampilan
        ]);
    }
}
