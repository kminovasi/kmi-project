<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Event;
use App\Models\Paper;
use App\Models\Team;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Log;

class DashboardController extends Controller
{
    public function showDashboard(Request $request)
    {
        $year = $request->input('year') ?? date('Y');
        $userCompanyCode = Auth::user()->company_code;
        $isSuperadmin = Auth::user()->role === "Superadmin";

        // Fungsi helper untuk menambahkan filter berdasarkan company_code jika bukan superadmin
        $addCompanyFilter = function ($query) use ($isSuperadmin, $userCompanyCode) {
            return $query->when(!$isSuperadmin, function ($q) use ($userCompanyCode) {
                $q->where('teams.company_code', $userCompanyCode);
            });
        };

        // Menghitung jumlah tim berdasarkan kategori utama
        $breakthroughInnovation = $addCompanyFilter(
            DB::table('teams')
                ->join('categories', 'categories.id', '=', 'teams.category_id')
                ->where('categories.category_parent', 'BREAKTHROUGH INNOVATION')
        )->count();

        $incrementalInnovation = $addCompanyFilter(
            DB::table('teams')
                ->join('categories', 'categories.id', '=', 'teams.category_id')
                ->where('categories.category_parent', 'INCREMENTAL INNOVATION')
        )->count();

        $ideaBox = $addCompanyFilter(
            DB::table('teams')
                ->join('categories', 'categories.id', '=', 'teams.category_id')
                ->where('categories.category_parent', 'IDEA BOX')
        )->count();

        // Menghitung jumlah tim berdasarkan kategori detail dalam "Breakthrough Innovation"
        $detailBreakthroughInnovationPBB = $addCompanyFilter(
            DB::table('teams')
                ->join('categories', 'categories.id', '=', 'teams.category_id')
                ->where('categories.category_name', 'PRODUK DAN BAHAN BAKU')
        )->count();

        $detailBreakthroughInnovationTPP = $addCompanyFilter(
            DB::table('teams')
                ->join('categories', 'categories.id', '=', 'teams.category_id')
                ->where('categories.category_name', 'TEKHNOLOGY & PROSES PRODUKSI')
        )->count();

        $detailBreakthroughInnovationManagement = $addCompanyFilter(
            DB::table('teams')
                ->join('categories', 'categories.id', '=', 'teams.category_id')
                ->where('categories.category_name', 'MANAGEMENT')
        )->count();

        // Menghitung jumlah tim berdasarkan kategori detail dalam "Incremental Innovation"
        $detailIncrementalInnovationGKMPlant = $addCompanyFilter(
            DB::table('teams')
                ->join('categories', 'categories.id', '=', 'teams.category_id')
                ->where('categories.category_name', 'GKM PLANT')
        )->count();

        $detailIncrementalInnovationGKMOffice = $addCompanyFilter(
            DB::table('teams')
                ->join('categories', 'categories.id', '=', 'teams.category_id')
                ->where('categories.category_name', 'GKM OFFICE')
        )->count();

        $detailIncrementalInnovationPKMPlant = $addCompanyFilter(
            DB::table('teams')
                ->join('categories', 'categories.id', '=', 'teams.category_id')
                ->where('categories.category_name', 'PKM PLANT')
        )->count();

        $detailIncrementalInnovationPKMOffice = $addCompanyFilter(
            DB::table('teams')
                ->join('categories', 'categories.id', '=', 'teams.category_id')
                ->where('categories.category_name', 'PKM OFFICE')
        )->count();

        $detailIncrementalInnovationSSPlant = $addCompanyFilter(
            DB::table('teams')
                ->join('categories', 'categories.id', '=', 'teams.category_id')
                ->where('categories.category_name', 'SS PLANT')
        )->count();

        $detailIncrementalInnovationSSOffice = $addCompanyFilter(
            DB::table('teams')
                ->join('categories', 'categories.id', '=', 'teams.category_id')
                ->where('categories.category_name', 'SS OFFICE')
        )->count();

        // Menghitung jumlah tim berdasarkan kategori detail dalam "Idea Box"
        $detailIdeaBoxIdea = $addCompanyFilter(
            DB::table('teams')
                ->join('categories', 'categories.id', '=', 'teams.category_id')
                ->where('categories.category_name', 'IDEA')
        )->count();

        $availableYears = Event::select('year')
            ->groupBy('year')
            ->orderBy('year', 'DESC')
            ->pluck('year')
            ->toArray();

        $totalInnovatorsMale = DB::table('pvt_members')
            ->join('users', 'users.employee_id', '=', 'pvt_members.employee_id')
            ->when(!$isSuperadmin, function ($query) use ($userCompanyCode) {
                $query->join('teams', 'teams.id', '=', 'pvt_members.team_id')
                    ->where('teams.company_code', $userCompanyCode);
            })
            ->whereYear('pvt_members.created_at', $year)
            ->where('users.gender', 'Male')
            ->where(function ($query) {
                $query->where('pvt_members.status', 'leader')
                    ->orWhere('pvt_members.status', 'member');
            })
            ->distinct('pvt_members.employee_id')
            ->count();

        $totalInnovatorsFemale = DB::table('pvt_members')
            ->join('users', 'users.employee_id', '=', 'pvt_members.employee_id')
            ->when(!$isSuperadmin, function ($query) use ($userCompanyCode) {
                $query->join('teams', 'teams.id', '=', 'pvt_members.team_id')
                    ->where('teams.company_code', $userCompanyCode);
            })
            ->whereYear('pvt_members.created_at', $year)
            ->where('users.gender', 'Female')
            ->where(function ($query) {
                $query->where('pvt_members.status', 'leader')
                    ->orWhere('pvt_members.status', 'member');
            })
            ->distinct('pvt_members.employee_id')
            ->count();

        $totalInnovators = $totalInnovatorsMale + $totalInnovatorsFemale;

        return view('auth.user.home', compact(
            'breakthroughInnovation',
            'incrementalInnovation',
            'ideaBox',
            'detailBreakthroughInnovationPBB',
            'detailBreakthroughInnovationTPP',
            'detailBreakthroughInnovationManagement',
            'detailIncrementalInnovationGKMPlant',
            'detailIncrementalInnovationGKMOffice',
            'detailIncrementalInnovationPKMPlant',
            'detailIncrementalInnovationPKMOffice',
            'detailIncrementalInnovationSSPlant',
            'detailIncrementalInnovationSSOffice',
            'detailIdeaBoxIdea',
            'year',
            'availableYears',
            'totalInnovators',
            'totalInnovatorsMale',
            'totalInnovatorsFemale',
            'isSuperadmin',
            'userCompanyCode'
        ));
    }

    public function showTotalTeamChart()
    {
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 4, $currentYear);

        $teams = Company::with(['teams' => function ($query) {
            $query->whereHas('paper', function ($subQuery) {
                $subQuery->where('status', 'accepted by innovation admin');
            });
        }])->get();

        $chartData = [
            'labels' => [], // Logo perusahaan
            'datasets' => [],
            'logos' => [] // Path logo untuk digunakan pada JavaScript
        ];

        foreach ($years as $index => $year) {
            $chartData['datasets'][] = [
                'label' => $year,
                'backgroundColor' => ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF"][$index % 5],
                'data' => []
            ];
        }

        foreach ($teams as $company) {
            // Sanitize nama perusahaan untuk mencocokkan nama file logo
            $sanitizedCompanyName = preg_replace('/[^a-zA-Z0-9_()]+/', '_', strtolower($company->company_name));
            $sanitizedCompanyName = preg_replace('/_+/', '_', $sanitizedCompanyName);
            $sanitizedCompanyName = trim($sanitizedCompanyName, '_');
            $logoPath = public_path('assets/logos/' . $sanitizedCompanyName . '.png');

            // Pengecekan apakah file logo ada, jika tidak gunakan logo default
            if (!file_exists($logoPath)) {
                $logoPath = asset('assets/logos/pt_semen_indonesia_tbk.png'); // Logo default
            } else {
                $logoPath = asset('assets/logos/' . $sanitizedCompanyName . '.png');
            }

            // Tambahkan logo ke labels
            $chartData['labels'][] = $company->company_name;
            $chartData['logos'][] = $logoPath;

            $teamCounts = [];
            foreach ($years as $year) {
                $teamCounts[$year] = $company->teams
                    ->whereBetween('created_at', ["$year-01-01", "$year-12-31"])
                    ->count();
            }

            foreach ($years as $index => $year) {
                $chartData['datasets'][$index]['data'][] = $teamCounts[$year];
            }
        }

        return view('dashboard.total-team-chart', ['chartDataTotalTeam' => $chartData]);
    }

    public function showTotalBenefitChart()
    {
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 4, $currentYear);

        $companies = Company::with(['teams.paper' => function ($query) use ($years) {
            $query->where('status', 'accepted by innovation admin')
                ->whereBetween('created_at', [
                    now()->subYears(4)->startOfYear(),
                    now()->endOfYear()
                ]);
        }])->get();

        $chartData = [
            'labels' => [], // Nama perusahaan
            'datasets' => [], // Dataset untuk setiap tahun
            'logos' => [] // Path logo perusahaan
        ];

        // Warna untuk setiap tahun
        $colors = ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF"];

        foreach ($years as $index => $year) {
            $chartData['datasets'][] = [
                'label' => $year,
                'backgroundColor' => $colors[$index % count($colors)],
                'data' => []
            ];
        }

        foreach ($companies as $company) {
            // Proses nama perusahaan menjadi nama file logo
            $sanitizedCompanyName = preg_replace('/[^a-zA-Z0-9_()]+/', '_', strtolower($company->company_name));
            $sanitizedCompanyName = preg_replace('/_+/', '_', $sanitizedCompanyName);
            $sanitizedCompanyName = trim($sanitizedCompanyName, '_');
            $logoPath = public_path('assets/logos/' . $sanitizedCompanyName . '.png');

            // Cek jika file logo ada, jika tidak gunakan default logo
            if (!file_exists($logoPath)) {
                $logoPath = asset('assets/logos/pt_semen_indonesia_tbk.png'); // Ganti dengan logo default
            } else {
                $logoPath = asset('assets/logos/' . $sanitizedCompanyName . '.png');
            }

            $chartData['labels'][] = $company->company_name; // Nama perusahaan
            $chartData['logos'][] = $logoPath; // Path logo perusahaan

            // Hitung total financial benefit per tahun
            $financialPerYear = [];
            foreach ($years as $year) {
                $financialPerYear[$year] = $company->teams->reduce(function ($carry, $team) use ($year) {
                    // Gunakan relasi papers
                    $teamFinancial = $team->paper->whereBetween('created_at', [
                        "$year-01-01",
                        "$year-12-31"
                    ])->sum('financial');

                    return $carry + $teamFinancial;
                }, 0);
            }


            // Tambahkan data ke dataset per tahun
            foreach ($years as $index => $year) {
                $chartData['datasets'][$index]['data'][] = $financialPerYear[$year] ?? 0;
            }
        }

        return view('dashboard.total-financial-benefit-chart', ['chartDataTotalBenefit' => $chartData]);
    }


    public function showTotalBenefitChartData()
    {
        // Ambil data financial benefit dari paper dengan status 'accepted by innovation admin'
        $data = DB::table('papers')
            ->join('teams', 'papers.team_id', '=', 'teams.id')
            ->join('companies', 'teams.company_code', '=', 'companies.company_code')
            ->select(
                'companies.company_name',
                DB::raw('EXTRACT(YEAR FROM papers.created_at) as year'),
                DB::raw('SUM(papers.financial) as total_financial')
            )
            ->where('papers.status', 'accepted by innovation admin')
            ->whereBetween('papers.created_at', [
                now()->subYears(4)->startOfYear(),
                now()->endOfYear()
            ])
            ->groupBy('companies.company_name', 'year')
            ->get();

        return response()->json($data);
    }
    public function showTotalPotentialBenefitChartData()
    {
        // Ambil data financial benefit dari paper dengan status 'accepted by innovation admin'
        $data = DB::table('papers')
            ->join('teams', 'papers.team_id', '=', 'teams.id')
            ->join('companies', 'teams.company_code', '=', 'companies.company_code')
            ->select(
                'companies.company_name',
                DB::raw('EXTRACT(YEAR FROM papers.created_at) as year'),
                DB::raw('SUM(papers.potential_benefit) as total_financial')
            )
            ->where('papers.status', 'accepted by innovation admin')
            ->whereBetween('papers.created_at', [
                now()->subYears(4)->startOfYear(),
                now()->endOfYear()
            ])
            ->groupBy('companies.company_name', 'year')
            ->get();

        return response()->json($data);
    }
}
