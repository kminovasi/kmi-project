<?php

namespace App\View\Components\Dashboard;

use Illuminate\View\Component;
use App\Models\Team;
use App\Models\PvtMember;
use App\Models\Category;
use App\Models\Event;

class Semen extends Component
{
    public $charts = [];
    public $logos = [];
    public $categories = [];
    public $colors = [];
    public $availableYears = [];
    public $year;

    public function __construct($year = null)
    {
        $this->availableYears =   Event::select('year')
        ->groupBy('year')
        ->orderBy('year', 'DESC')
        ->pluck('year')
        ->toArray();
        $this->year = $year;
        // Ambil tahun terbaru dari data pendaftar
        $latestYear = Team::join('pvt_members', 'teams.id', '=', 'pvt_members.team_id')
            ->selectRaw("DATE_PART('year', teams.created_at) as year")
            ->groupBy('year')
            ->orderBy('year', 'DESC')
            ->first()->year ?? null;

        // Ambil semua kategori dari tabel kategori
        $allCategories = Category::all();

        // Warna untuk setiap kategori
        $this->colors = [
            'rgba(75, 192, 192, 1)',    // Biru muda
            'rgba(255, 99, 132, 1)',    // Merah muda
            'rgba(255, 206, 86, 1)',    // Kuning
            'rgba(54, 162, 235, 1)',    // Biru
            'rgba(153, 102, 255, 1)',   // Ungu
            'rgba(255, 159, 64, 1)',    // Oranye
            'rgba(255, 87, 51, 1)',     // Merah oranye
            'rgba(144, 238, 144, 1)',   // Hijau muda
            'rgba(255, 140, 0, 1)',     // Oranye tua
            'rgba(0, 255, 127, 1)',     // Hijau laut
            'rgba(0, 191, 255, 1)',     // Biru langit
            'rgba(238, 130, 238, 1)',   // Violet
            'rgba(255, 105, 180, 1)',   // Pink cerah
            'rgba(220, 20, 60, 1)',     // Crimson
        ];


        // Mengasosiasikan warna dengan kategori
        foreach ($allCategories as $index => $category) {
            $this->categories[$category->category_name] = $this->colors[$index % count($this->colors)];
        }

        if ($latestYear) {
            // Ambil jumlah pendaftar per kategori untuk tahun terbaru
            $data = Team::join('pvt_members', 'teams.id', '=', 'pvt_members.team_id')
                ->join('companies', 'teams.company_code', '=', 'companies.company_code')
                ->join('categories', 'teams.category_id', '=', 'categories.id')
                ->selectRaw("companies.company_name, categories.category_name, COUNT(pvt_members.id) as total_innovators")
                ->whereYear('teams.created_at', $latestYear)
                ->groupBy('companies.company_name', 'categories.category_name')
                ->orderBy('companies.company_name')
                ->get();

            // Memisahkan data berdasarkan perusahaan
            $groupedData = $data->groupBy('company_name');

            // Menyiapkan dataset dan membuat chart per perusahaan
            foreach ($groupedData as $company => $dataPerCompany) {
                $categories = $dataPerCompany->pluck('category_name')->toArray();
                $totalsPerCategory = $dataPerCompany->pluck('total_innovators')->toArray();

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

                // Simpan path logo
                $this->logos[] = $logoPath;

                // Menyiapkan data untuk Chart.js
                $this->charts[] = [
                    'company' => $company,
                    'categories' => $categories,
                    'data' => $totalsPerCategory,
                ];
            }
        }
    }

    public function render()
    {
        return view('components.dashboard.semen', [
            'charts' => $this->charts,
            'logos' => $this->logos,
            'categories' => $this->categories,
            'availableYears' => $this->availableYears,
            'year' => $this->year
        ]);
    }
}
