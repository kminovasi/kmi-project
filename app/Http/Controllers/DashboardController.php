<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Team;
use App\Models\Event;
use App\Models\Paper;
use App\Models\Company;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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

        // Fetch all categories and count teams for each category
        $categories = Category::withCount(['teams' => function ($query) use ($addCompanyFilter) {
            $addCompanyFilter($query);
        }])->get();

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
        $totalActiveEvents = Event::where('status', 'active')->count();

        return view('auth.user.home', compact(
            'totalActiveEvents',
            'categories',
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
        $years = range($currentYear - 3, $currentYear);

        $teams = Company::with(['teams' => function ($query) {
                $query->whereHas('paper', function ($subQuery) {
                    $subQuery->where('status', 'accepted by innovation admin');
                });
            }])
            ->withCount(['teams as accepted_papers_count' => function ($query) {
                $query->whereHas('paper', function ($subQuery) {
                    $subQuery->where('status', 'accepted by innovation admin');
                });
            }])
            ->orderByDesc('accepted_papers_count')
            ->get();

        $chartData = [
            'labels' => [], // Logo perusahaan
            'datasets' => [],
            'logos' => [] // Path logo untuk digunakan pada JavaScript
        ];

        foreach ($years as $index => $year) {
            $chartData['datasets'][] = [
                'label' => $year,
                'backgroundColor' => ["#36A2EB", "#a20006", "#4BC0C0", "#38507a"][$index % 4],
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
            $chartData['company_id'][] = $company->id;

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
        $isSuperadmin = Auth::user()->role === 'Superadmin';
        $company_code = Auth::user()->company_code;

        $companiesQuery = Company::with(['teams.paper' => function ($query) use ($currentYear) {
            $query->where('status', 'accepted by innovation admin')
                ->whereBetween('created_at', [
                    now()->startOfYear(),
                    now()->endOfYear()
                ]);
        }]);

        if (!$isSuperadmin) {
            $companiesQuery->where('company_code', $company_code);
        }

        $companies = $companiesQuery->get();

        $chartData = [
            'labels' => [], // Nama perusahaan
            'datasets' => [], // Dataset untuk tahun ini saja
            'logos' => [], // Path logo perusahaan
            'isSuperadmin' => $isSuperadmin
        ];

        // Warna untuk tahun ini
        $color = "#009e61";

        $chartData['datasets'][] = [
            'label' => $currentYear,
            'backgroundColor' => $color,
            'data' => [],
        ];

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

            // Hitung total financial benefit untuk tahun ini saja
            $financialThisYear = $company->teams->reduce(function ($carry, $team) use ($currentYear) {
                // Gunakan relasi papers
                $teamFinancial = $team->papers->whereBetween('created_at', [
                    "$currentYear-01-01",
                    "$currentYear-12-31"
                ])->sum('financial');

                return $carry + $teamFinancial;
            }, 0);

            // Tambahkan data ke dataset tahun ini
            $chartData['datasets'][0]['data'][] = $financialThisYear;
        }

        return view('dashboard.total-financial-benefit-chart', [
            'chartDataTotalBenefit' => $chartData, 
            'isSuperadmin' => $isSuperadmin
        ]);
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

    public function getFinancialBenefitsByCompany()
    {
        $companies = Company::with(['teams.paper'])->get();
        $currentYear = now()->year;
        $years = range($currentYear - 3, $currentYear);

        $financialData = [];

        foreach ($companies as $company) {
            $dataPerYear = [];
            foreach ($years as $year) {
                $totalFinancial = $company->teams->flatMap(function ($team) use ($year) {
                    return $team->papers->filter(function ($paper) use ($year) {
                        return $paper->created_at->year == $year;
                    });
                })->sum('financial');

                $dataPerYear[$year] = $totalFinancial;
            }

            $financialData[] = [
                'company_name' => $company->company_name,
                'financials' => $dataPerYear,
            ];
        }

        return response()->json($financialData);
    }

    public function showTotalTeamChartCompany($company_code)
    {
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 4, $currentYear);

        $teams = Company::where('company_code', $company_code)->with(['teams' => function ($query) {
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

        return view('dashboard.internal.total-team-chart', ['chartDataTotalTeam' => $chartData]);
    }

    public function getBenefitChartData(Request $request)
    {
        $startYear = (int) $request->input('startYear', date('Y'));
        $endYear = (int) $request->input('endYear', date('Y'));

        $isSuperadmin = Auth::user()->role === "Superadmin";
        $userCompanyCode = Auth::user()->company_code;

        $acceptedStatuses = ['accepted by innovation admin'];

        $query = Paper::join('teams', 'papers.team_id', '=', 'teams.id')
            ->join('companies', 'teams.company_code', '=', 'companies.company_code')
            ->selectRaw('companies.company_name, SUM(papers.financial + papers.potential_benefit) as total_benefit')
            ->whereIn('papers.status', $acceptedStatuses)
            ->whereYear('papers.created_at', '>=', $startYear)
            ->whereYear('papers.created_at', '<=', $endYear)
            ->groupBy('companies.company_name')
            ->orderBy('total_benefit', 'DESC');

        if (!$isSuperadmin) {
            $query->where('teams.company_code', $userCompanyCode);
        }

        $data = $query->get();

        $charts = [
            'labels' => [],
            'data' => [],
            'logos' => [],
        ];

        foreach ($data as $row) {
            $company = $row->company_name;
            $sanitizedCompanyName = preg_replace('/[^a-zA-Z0-9_()]+/', '_', strtolower($company));
            $sanitizedCompanyName = preg_replace('/_+/', '_', $sanitizedCompanyName);
            $sanitizedCompanyName = trim($sanitizedCompanyName, '_');

            $logoPath = public_path('assets/logos/' . $sanitizedCompanyName . '.png');
            if (!file_exists($logoPath)) {
                $logoPath = asset('assets/logos/pt_semen_indonesia_tbk.png');
            } else {
                $logoPath = asset('assets/logos/' . $sanitizedCompanyName . '.png');
            }

            $charts['labels'][] = $company;
            $charts['data'][] = $row->total_benefit;
            $charts['logos'][] = $logoPath;
        }

        return response()->json($charts);
    }
}