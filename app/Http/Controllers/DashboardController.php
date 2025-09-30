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
        $listCompany = Company::all();
        
        if(in_array($userCompanyCode, [2000, 7000])) {
            $filteredCompanyCodeUser = [2000, 7000];
        } else {
            $filteredCompanyCodeUser = [$userCompanyCode];
        }

        // Status untuk masing-masing grup
        $implementedStatuses = ['Implemented'];
        $ideaBoxStatuses = ['Progress', 'Not Implemented'];

        $categories = Category::select('id', 'category_name')->with([
            'teams' => function ($query) use ($isSuperadmin, $filteredCompanyCodeUser) {
                $query->select('id', 'category_id', 'company_code'); // tambahkan company_code untuk kebutuhan filter & validasi
        
                if (!$isSuperadmin) {
                    $query->whereIn('company_code', $filteredCompanyCodeUser);
                }
        
                $query->with([
                    'papers' => function ($q) {
                        $q->select('id', 'team_id', 'status_inovasi', 'status');
                    }
                ]);
            }
        ])->get();

        $implemented = [];
        $ideaBox = [];

        $totalImplementedInnovations = 0;
        $totalIdeaBoxInnovations = 0;

        foreach ($categories as $category) {
            $implementedTeamIds = [];
            $ideaBoxTeamIds = [];

            $implementedCount = 0;
            $ideaBoxCount = 0;

            foreach ($category->teams as $team) {
                foreach ($team->papers as $paper) {
                    if (in_array($paper->status_inovasi, $implementedStatuses) && $paper->status !== 'not finish' && $category->category_name != 'IDEA BOX') {
                        $implementedTeamIds[] = $team->id;
                        $implementedCount++; // Tambah total inovasi
                        break;
                    } elseif (in_array($paper->status_inovasi, $ideaBoxStatuses) || $paper->status === 'not finish' || $category->category_name == 'IDEA BOX') {
                        $ideaBoxTeamIds[] = $team->id;
                        $ideaBoxCount++; // Tambah total inovasi
                        break;
                    }
                }
            }

            $implemented[] = [
                'category_name' => $category->category_name,
                'count' => count(array_unique($implementedTeamIds))
            ];

            $ideaBox[] = [
                'category_name' => $category->category_name,
                'count' => count(array_unique($ideaBoxTeamIds))
            ];

            $totalImplementedInnovations += $implementedCount;
            $totalIdeaBoxInnovations += $ideaBoxCount;
        }

        $availableYears = Event::select('year')
            ->groupBy('year')
            ->orderBy('year', 'DESC')
            ->pluck('year')
            ->toArray();

        $totalInnovatorsMale = DB::table('pvt_members')
            ->join('users', 'users.employee_id', '=', 'pvt_members.employee_id')
            ->join('teams', 'teams.id', '=', 'pvt_members.team_id') 
            ->join('papers', 'papers.team_id', '=', 'teams.id')
            ->when(!$isSuperadmin, function ($query) use ($filteredCompanyCodeUser) {
                $query->whereIn('teams.company_code', $filteredCompanyCodeUser);
            })
            ->where('pvt_members.status', '!=', 'gm')
            ->where('users.gender', 'Male')
            ->where('papers.status', '!=', 'rejected by innovation admin')
            ->select('pvt_members.employee_id', 'teams.id')
            ->distinct()
            ->count();

        $totalInnovatorsFemale = DB::table('pvt_members')
            ->join('users', 'users.employee_id', '=', 'pvt_members.employee_id')
            ->join('teams', 'teams.id', '=', 'pvt_members.team_id') 
            ->join('papers', 'papers.team_id', '=', 'teams.id')       
            ->when(!$isSuperadmin, function ($query) use ($filteredCompanyCodeUser) {
                $query->whereIn('teams.company_code', $filteredCompanyCodeUser);
            })
            ->where('pvt_members.status', '!=', 'gm')
            ->where('users.gender', 'Female')
            ->where('papers.status', '!=', 'rejected by innovation admin')
            ->select('pvt_members.employee_id', 'teams.id')
            ->distinct()
            ->count();
        
        $totalInnovatoresOutsource = DB::table('ph2_members')
        ->join('teams', 'teams.id', '=', 'ph2_members.team_id')
        ->join('papers', 'papers.team_id', '=', 'teams.id')
        ->when(!$isSuperadmin, function ($query) use ($filteredCompanyCodeUser) {
            $query->whereIn('teams.company_code', $filteredCompanyCodeUser);
        })
        ->where('papers.status', '!=', 'rejected by innovation admin')
        ->distinct()
        ->count();

        $totalInnovators = $totalInnovatorsMale + $totalInnovatorsFemale + $totalInnovatoresOutsource;
        $totalActiveEvents = Event::where('status', 'active')->count();

        return view('auth.user.home', compact(
            'listCompany',
            'totalActiveEvents',
            'categories',
            'year',
            'availableYears',
            'totalInnovators',
            'totalInnovatorsMale',
            'totalInnovatorsFemale',
            'totalInnovatoresOutsource',
            'isSuperadmin',
            'userCompanyCode',
            'implemented',
            'ideaBox',
            'totalImplementedInnovations',
            'totalIdeaBoxInnovations'
        ));
    }
    
    public function filterDashboardCard(Request $request)
    {
        if (Auth::user()->role != 'Superadmin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        
        $companyCode = $request->company_code;
        
        if(in_array($companyCode, [2000, 7000])) {
            $filteredCompanyCodeUser = [2000, 7000];
        } else {
            $filteredCompanyCodeUser = [$companyCode];
        }

        // Status untuk masing-masing grup
        $implementedStatuses = ['Implemented'];
        $ideaBoxStatuses = ['Progress', 'Not Implemented'];

        $categories = Category::select('id', 'category_name')->with([
            'teams' => function ($query) use ($filteredCompanyCodeUser) {
                $query->select('id', 'category_id', 'company_code'); // tambahkan company_code untuk kebutuhan filter & validasi
                $query->whereIn('teams.company_code', $filteredCompanyCodeUser);
                $query->with([
                    'papers' => function ($q) {
                        $q->select('id', 'team_id', 'status_inovasi', 'status');
                    }
                ]);
            }
        ])->get();

        $implemented = [];
        $ideaBox = [];

        $totalImplementedInnovations = 0;
        $totalIdeaBoxInnovations = 0;

        foreach ($categories as $category) {
            $implementedTeamIds = [];
            $ideaBoxTeamIds = [];

            $implementedCount = 0;
            $ideaBoxCount = 0;

            foreach ($category->teams as $team) {
                foreach ($team->papers as $paper) {
                    if (in_array($paper->status_inovasi, $implementedStatuses) && $paper->status !== 'not finish') {
                        $implementedTeamIds[] = $team->id;
                        $implementedCount++; // Tambah total inovasi
                        break;
                    } elseif (in_array($paper->status_inovasi, $ideaBoxStatuses) || $paper->status === 'not finish') {
                        $ideaBoxTeamIds[] = $team->id;
                        $ideaBoxCount++; // Tambah total inovasi
                        break;
                    }
                }
            }

            $implemented[] = [
                'category_name' => $category->category_name,
                'count' => count(array_unique($implementedTeamIds))
            ];

            $ideaBox[] = [
                'category_name' => $category->category_name,
                'count' => count(array_unique($ideaBoxTeamIds))
            ];

            $totalImplementedInnovations += $implementedCount;
            $totalIdeaBoxInnovations += $ideaBoxCount;
        }

        $availableYears = Event::select('year')
            ->groupBy('year')
            ->orderBy('year', 'DESC')
            ->pluck('year')
            ->toArray();

        $totalInnovatorsMale = DB::table('pvt_members')
            ->join('users', 'users.employee_id', '=', 'pvt_members.employee_id')
            ->join('teams', 'teams.id', '=', 'pvt_members.team_id') 
            ->join('papers', 'papers.team_id', '=', 'teams.id')
            ->whereIn('teams.company_code', $filteredCompanyCodeUser)
            ->where('pvt_members.status', '!=', 'gm')
            ->where('users.gender', 'Male')
            ->where('papers.status', '!=', 'rejected by innovation admin')
            ->select('pvt_members.employee_id', 'teams.id')
            ->distinct()
            ->count();

        $totalInnovatorsFemale = DB::table('pvt_members')
            ->join('users', 'users.employee_id', '=', 'pvt_members.employee_id')
            ->join('teams', 'teams.id', '=', 'pvt_members.team_id') 
            ->join('papers', 'papers.team_id', '=', 'teams.id')       
            ->whereIn('teams.company_code', $filteredCompanyCodeUser)
            ->where('pvt_members.status', '!=', 'gm')
            ->where('users.gender', 'Female')
            ->where('papers.status', '!=', 'rejected by innovation admin')
            ->select('pvt_members.employee_id', 'teams.id')
            ->distinct()
            ->count();
        
        $totalInnovatoresOutsource = DB::table('ph2_members')
        ->join('teams', 'teams.id', '=', 'ph2_members.team_id')
        ->join('papers', 'papers.team_id', '=', 'teams.id')
        ->whereIn('teams.company_code', $filteredCompanyCodeUser)
        ->where('papers.status', '!=', 'rejected by innovation admin')
        ->distinct()
        ->count();

        $totalInnovators = $totalInnovatorsMale + $totalInnovatorsFemale + $totalInnovatoresOutsource;
        $totalActiveEvents = Event::where('status', 'active')->count();
        
        $html = view('components.dashboard.filtered_dashboard_card', compact(
            'implemented', 'totalInnovators', 'totalInnovatorsMale', 'totalInnovatorsFemale',
            'totalInnovatoresOutsource', 'totalActiveEvents', 'ideaBox',
            'totalImplementedInnovations', 'totalIdeaBoxInnovations'
        ))->render();
    
        return response()->json(['html' => $html]);
    }

    public function showDashboardPaperList(Request $request, $category, $status)
    {
        $userCompanyCode = Auth::user()->company_code;
        $paramCompanyCode = $request->query('company_code');
    
        if ($paramCompanyCode) {
            $filteredCompanyCode = [$paramCompanyCode];
        } elseif (in_array($userCompanyCode, [2000, 7000])) {
            $filteredCompanyCode = [2000, 7000];
        } else {
            $filteredCompanyCode = [$userCompanyCode];
        }
    
        if ($status === 'implemented') {
            $innovationStatus = ['Implemented'];
        } elseif ($status === 'idea box') {
            $innovationStatus = ['Progress', 'Not Implemented', 'not finish'];
        } else {
            $innovationStatus = [];
        }
    
        $categories = Category::with([
            'teams' => function ($query) use ($filteredCompanyCode) {
                $query->select('id', 'team_name', 'category_id', 'status_lomba')
                      ->whereIn('company_code', $filteredCompanyCode);
            },
            'teams.papers' => function ($query) use ($innovationStatus) {
                $query->select('id', 'innovation_title', 'team_id', 'status', 'status_inovasi')
                      ->where(function ($q) use ($innovationStatus) {
                          $q->whereIn('status_inovasi', $innovationStatus)
                            ->orWhereIn('status', $innovationStatus);
                      });
            },
            'teams.company' => function ($query) {
                $query->select('company_code', 'company_name');
            }
        ])
        ->where('category_name', $category)
        ->get();
    
        // Cek apakah ada data
        $hasData = false;
        foreach ($categories as $item) {
            foreach ($item->teams as $team) {
                if ($team->papers->count() > 0) {
                    $hasData = true;
                    break 2;
                }
            }
        }
    
        return view('components.dashboard.list-paper', compact('categories', 'category', 'status', 'hasData'));
    }

    public function showTotalTeamChart()
    {
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 3, $currentYear);
    
        // Ambil data tim yang memiliki paper diterima, lalu join ke event dan company
        $teamIds = DB::table('pvt_event_teams')
            ->select('team_id')
            ->distinct()
            ->pluck('team_id');
        
        $rawData = DB::table('teams')
    ->join('papers', 'papers.team_id', '=', 'teams.id')
    ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
    ->join('events', 'events.id', '=', 'pvt_event_teams.event_id')
    ->join('companies', 'companies.company_code', '=', 'teams.company_code') // langsung dari tim
    ->where('papers.status', 'accepted by innovation admin')
    ->whereIn(DB::raw('YEAR(events.year)'), $years)
    ->select(
        'companies.id as company_id',
        'companies.company_name',
        'companies.company_code',
        DB::raw('YEAR(events.year) as year'),
        'teams.id as team_id'
    )
    ->groupBy('teams.id', 'companies.id', 'companies.company_name', 'companies.company_code', DB::raw('YEAR(events.year)'))
    ->get();

    
        // Kelompokkan data
        $groupedData = [];
        foreach ($rawData as $row) {
            $companyKey = $row->company_code == 7000 ? 2000 : $row->company_code;
            $companyName = $row->company_code == 7000 ? 'Gabungan 2000+7000' : $row->company_name;
            $year = $row->year;
    
            if (!isset($groupedData[$companyKey])) {
                $groupedData[$companyKey] = [
                    'company_id' => $companyKey,
                    'company_name' => $companyName,
                    'data' => [],
                ];
            }
    
            if (!isset($groupedData[$companyKey]['data'][$year])) {
                $groupedData[$companyKey]['data'][$year] = [];
            }
    
            if (!in_array($row->team_id, $groupedData[$companyKey]['data'][$year])) {
                $groupedData[$companyKey]['data'][$year][] = $row->team_id;
            }
        }
    
        // Siapkan data chart
        $chartData = [
            'labels' => [],
            'datasets' => [],
            'logos' => [],
            'company_id' => [],
        ];
    
        foreach ($years as $index => $year) {
            $chartData['datasets'][] = [
                'label' => $year,
                'backgroundColor' => ["#36A2EB", "#a20006", "#4BC0C0", "#38507a"][$index % 4],
                'data' => []
            ];
        }
    
        foreach ($groupedData as $company) {
            $chartData['labels'][] = $company['company_name'];
            $chartData['company_ids'][] = $company['company_id'];
    
            // Proses logo
            $sanitizedCompanyName = preg_replace('/[^a-zA-Z0-9_()]+/', '_', strtolower($company['company_name']));
            $sanitizedCompanyName = preg_replace('/_+/', '_', $sanitizedCompanyName);
            $sanitizedCompanyName = trim($sanitizedCompanyName, '_');
            $logoPath = public_path('assets/logos/' . $sanitizedCompanyName . '.png');
    
            $chartData['logos'][] = file_exists($logoPath)
                ? asset('assets/logos/' . $sanitizedCompanyName . '.png')
                : asset('assets/logos/pt_semen_indonesia_tbk.png');
    
            foreach ($years as $i => $year) {
                $chartData['datasets'][$i]['data'][] = count($company['data'][$year] ?? []);
            }
        }
        
        // dd($chartData);
    
        return view('dashboard.total-team-chart', ['chartDataTotalTeam' => $chartData]);
    }
    
   public function showTotalBenefitChart()
{
    $currentYear = now()->year;
    $years = range($currentYear - 3, $currentYear);
    $isSuperadmin = Auth::user()->role === 'Superadmin';

    // Pakai logic yang sudah benar (mengalikan dengan jumlah event finish)
    $rows = self::getFinancialBenefitsByCompany(); // <-- already array

    // Susun chartData => labels, datasets per tahun, logos
    $labels = array_map(fn($r) => $r['company_name'], $rows);

    // logos dari nama perusahaan (fallback ke default)
    $logos = [];
    foreach ($labels as $name) {
        $slug = preg_replace('/[^a-zA-Z0-9_()]+/','_', strtolower($name));
        $slug = preg_replace('/_+/', '_', trim($slug, '_'));
        $path = public_path("assets/logos/{$slug}.png");
        $logos[] = file_exists($path)
            ? asset("assets/logos/{$slug}.png")
            : asset('assets/logos/pt_semen_indonesia_tbk.png');
    }

    $colors = ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0"];
    $datasets = [];
    foreach ($years as $i => $y) {
        $datasets[] = [
            'label' => $y,
            'backgroundColor' => $colors[$i % count($colors)],
            'data' => array_map(fn($r) => (int)($r['financials'][$y] ?? 0), $rows),
        ];
    }

    $chartData = [
        'labels'       => $labels,
        'datasets'     => $datasets,
        'logos'        => $logos,
        'isSuperadmin' => $isSuperadmin,
    ];

    return view('dashboard.total-financial-benefit-chart', [
        'chartDataTotalBenefit' => $chartData,
        'isSuperadmin'          => $isSuperadmin,
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
            ->groupBy('companies.company_name', 'events.year')
            ->get();

        return response()->json($data);
    }

    public static function getFinancialBenefitsByCompany(): array
    {
        $currentYear = now()->year;
        $years = range($currentYear - 3, $currentYear);
    
        // total paper accepted per TIM (all-time) + merge 7000 -> 2000
        $base = DB::table('teams as t')
            ->join('papers as p', function ($j) {
                $j->on('p.team_id', '=', 't.id')
                  ->whereRaw("TRIM(LOWER(p.status)) = 'accepted by innovation admin'");
            })
            ->selectRaw("
                t.id AS team_id,
                CASE WHEN t.company_code = 7000 THEN 2000 ELSE t.company_code END AS company_code_merged,
                SUM(p.financial) AS team_financial
            ")
            ->groupByRaw("
                t.id,
                CASE WHEN t.company_code = 7000 THEN 2000 ELSE t.company_code END
            ");
    
        $perYearTotals = [];
        $allCodes = [];
    
        foreach ($years as $y) {
            // ✅ Hitung SEKALI SAJA per tim di tahun y (boolean presence), bukan jumlah event
            $finishCount = DB::table('pvt_event_teams as pet')
                ->join('events as e', 'e.id', '=', 'pet.event_id')
                ->where('e.status', 'finish')
                ->where('e.year', $y)
                ->selectRaw('pet.team_id, 1 AS has_finish')      // cukup 1 jika ada minimal 1 event finish
                ->groupBy('pet.team_id');
    
            // kontribusi per perusahaan per tahun = SUM(team_financial × has_finish[0/1])
            $rows = DB::query()
                ->fromSub($base, 'tp')
                ->joinSub($finishCount, 'fc', 'fc.team_id', '=', 'tp.team_id')
                ->selectRaw('tp.company_code_merged, SUM(tp.team_financial * fc.has_finish) AS total')
                ->groupBy('tp.company_code_merged')
                ->get();
    
            $perYearTotals[$y] = [];
            foreach ($rows as $r) {
                $perYearTotals[$y][$r->company_code_merged] = (int) $r->total;
                $allCodes[$r->company_code_merged] = true;
            }
        }
    
        // nama perusahaan (override 2000)
        $codes = array_keys($allCodes);
        $names = \App\Models\Company::whereIn('company_code', $codes)
            ->pluck('company_name', 'company_code')->toArray();
        $names['2000'] = $names['2000'] ?? 'PT Semen Indonesia (Persero)Tbk';
    
        // rakit payload untuk chart
        $out = [];
        foreach ($codes as $code) {
            $byYear = [];
            $sum = 0;
            foreach ($years as $y) {
                $v = $perYearTotals[$y][$code] ?? 0;
                $byYear[$y] = $v;
                $sum += $v;
            }
            if ($sum > 0) {
                $out[] = [
                    'company_name' => $code === '2000' ? 'PT Semen Indonesia (Persero)Tbk' : ($names[$code] ?? $code),
                    'financials'   => $byYear,
                ];
            }
        }
    
        usort($out, fn($a, $b) => array_sum($b['financials']) <=> array_sum($a['financials']));
        return $out;
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
            ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            ->join('events', 'events.id', '=', 'pvt_event_teams.event_id')
            ->selectRaw('teams.company_code, companies.company_name, companies.sort_order, SUM(papers.financial + papers.potential_benefit) as total_benefit')
            ->whereIn('papers.status', $acceptedStatuses)
            ->where('events.status', 'finish')
            ->where('events.year', '>=', $startYear)
            ->where('events.year', '<=', $endYear)
            ->groupBy('teams.company_code', 'companies.company_name', 'companies.sort_order')
            ->orderBy('companies.sort_order');

        if (!$isSuperadmin) {
            $query->where('teams.company_code', $userCompanyCode);
        }

        $data = $query->get();

        // Gabungkan data company_code 7000 ke 2000
        $company2000 = $data->firstWhere('company_code', 2000);
        $company7000 = $data->firstWhere('company_code', 7000);

        if ($company7000) {
            if ($company2000) {
                $company2000->total_benefit += $company7000->total_benefit;
            }
            // Hapus data 7000 agar tidak muncul
            $data = $data->reject(fn($item) => $item->company_code == 7000)->values();
        }

        // Urutkan ulang berdasarkan sort_order
        $data = $data->sortBy('sort_order')->values();

        // Siapkan data untuk Chart.js
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