<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Event;
use App\Models\Paper;
use App\Models\Company;
use App\Models\PvtMember;
use Illuminate\Http\Request;

class DetailCompanyChartController extends Controller
{
    public function index(Request $request)
    {
        // Ambil nilai tahun dari request atau gunakan tahun sekarang jika null
        $selectedYear = $request->input('year', Carbon::now()->year);

        // Ambil data perusahaan
        $companies = Company::select('id', 'company_name', 'company_code')->get();

        foreach ($companies as $company) {
            // Buat nama file logo yang sudah disanitasi
            $sanitizedCompanyName = preg_replace('/[^a-zA-Z0-9_()]+/', '_', strtolower($company->company_name));
            $sanitizedCompanyName = preg_replace('/_+/', '_', $sanitizedCompanyName);
            $sanitizedCompanyName = trim($sanitizedCompanyName, '_');

            // Path logo
            $logoPath = public_path('assets/logos/' . $sanitizedCompanyName . '.png');

            // Pengecekan apakah file logo ada, jika tidak gunakan default
            if (!file_exists($logoPath)) {
                $company->logo_url = asset('assets/logos/pt_semen_indonesia_tbk.png'); // Logo default
            } else {
                $company->logo_url = asset('assets/logos/' . $sanitizedCompanyName . '.png'); // Logo perusahaan
            }

            // Ambil total innovator dari setiap perusahaan per tahunnya
            $company->total_innovators = PvtMember::whereHas('team', function ($query) use ($company, $selectedYear) {
                $query->where('company_code', $company->company_code)
                    ->whereYear('created_at', $selectedYear);
            })->whereIn('status', ['leader', 'member'])->count();
        }

        // Ambil daftar tahun yang tersedia dari tabel Event
        $availableYears = Event::select('year')
            ->groupBy('year')
            ->orderBy('year', 'DESC')
            ->pluck('year')
            ->toArray();


        return view('detail_company_chart.index', compact('companies', 'availableYears', 'selectedYear'));
    }
    
    public function show(Request $request, $companyId)
    {
        // Ambil nama perusahaan dan company_code berdasarkan ID
        $company = Company::select('company_name', 'id', 'company_code')->where('company_code', $companyId)->first();
        
        // Ambil tahun yang tersedia untuk filter dropdown
        $availableYears = Event::select('year')
            ->groupBy('year')
            ->orderBy('year', 'DESC')
            ->pluck('year')
            ->toArray();

        // Ambil tahun dari request, jika tidak ada, pakai tahun sekarang
        $year = $request->query('year') ?? Carbon::now()->year;

        // Ambil unit organisasi yang dipilih
        $organizationUnit = $request->query('organization-unit');

        // Hitung total inovator dan berdasarkan gender dengan filter tahun
       $targetCompanyCode = $company->company_code;

        // Jika company_code 2000 atau 7000, jadikan gabungan 2000+7000
        if (in_array($targetCompanyCode, [2000, 7000])) {
            $filteredCodes = [2000, 7000];
        } else {
            $filteredCodes = [$targetCompanyCode];
        }
        
        $innovatorData = DB::table('pvt_members')
            ->join('teams', 'teams.id', '=', 'pvt_members.team_id')
            ->join('papers', 'papers.team_id', '=', 'teams.id')
            ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            ->join('events', 'events.id', '=', 'pvt_event_teams.event_id')
            ->leftJoin('users', 'users.employee_id', '=', 'pvt_members.employee_id')
            ->whereIn('teams.company_code', $filteredCodes)
            ->where('pvt_members.status', '!=', 'gm')
            ->select('users.gender', 'pvt_members.employee_id', 'teams.id as team_id')
            ->distinct()
            ->get()
            ->groupBy('gender');
        
        // Hitung total inovator pria & wanita unik berdasarkan employee_id
        $maleCount = count($innovatorData['Male'] ?? []);
        $femaleCount = count($innovatorData['Female'] ?? []);
        $totalInnovators = $maleCount + $femaleCount;

        // Hitung total manfaat inovasi dengan filter tahun
        $totalPotentialBenefit = Paper::join('teams', 'papers.team_id', '=', 'teams.id')
            ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            ->join('events', 'events.id', '=', 'pvt_event_teams.event_id')
            ->whereIn('teams.company_code', $filteredCodes)
            ->where('papers.status', 'accepted by innovation admin')
            ->where('events.status', 'finish')
            ->sum('papers.potential_benefit');
        $formattedTotalPotentialBenefit = number_format($totalPotentialBenefit, 0, ',', '.');

        $totalFinancialBenefit = Paper::join('teams', 'papers.team_id', '=', 'teams.id')
            ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            ->join('events', 'events.id', '=', 'pvt_event_teams.event_id')
            ->whereIn('teams.company_code', $filteredCodes)
            ->where('papers.status', 'accepted by innovation admin')
            ->where('events.status', 'finish')
            ->sum('papers.financial');
        $formattedTotalFinancialBenefit = number_format($totalFinancialBenefit, 0, ',', '.');

        return view('detail_company_chart.show', compact(
            'company',
            'totalInnovators',
            'maleCount',
            'femaleCount',
            'organizationUnit',
            'availableYears',
            'year',
            'formattedTotalPotentialBenefit',
            'formattedTotalFinancialBenefit'
        ));
    }
}