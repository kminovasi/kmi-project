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
        $selectedYear = (int) $request->input('year', Carbon::now()->year);
        $companies = Company::select('id', 'company_name', 'company_code')->get();

        foreach ($companies as $company) {
            $sanitizedCompanyName = preg_replace('/[^a-zA-Z0-9_()]+/', '_', strtolower($company->company_name));
            $sanitizedCompanyName = preg_replace('/_+/', '_', $sanitizedCompanyName);
            $sanitizedCompanyName = trim($sanitizedCompanyName, '_');

            $logoPath = public_path('assets/logos/' . $sanitizedCompanyName . '.png');
            $company->logo_url = file_exists($logoPath)
                ? asset('assets/logos/' . $sanitizedCompanyName . '.png')
                : asset('assets/logos/pt_semen_indonesia_tbk.png');

            $company->total_innovators = DB::table('pvt_members')
                ->join('teams', 'pvt_members.team_id', '=', 'teams.id')
                ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
                ->join('events', 'events.id', '=', 'pvt_event_teams.event_id')
                ->join('papers', 'papers.team_id', '=', 'teams.id')
                ->leftJoin('users', 'users.employee_id', '=', 'pvt_members.employee_id')
                ->where('teams.company_code', $company->company_code)
                ->where('events.year', '=', $selectedYear)
                ->where('pvt_members.status', '!=', 'gm')
                ->whereRaw("TRIM(LOWER(papers.status)) = 'accepted by innovation admin'")
                ->selectRaw("COUNT(DISTINCT CONCAT(pvt_members.employee_id, '-', teams.id)) AS c")
                ->value('c');
        }

        $availableYears = Event::select('year')
            ->groupBy('year')
            ->orderBy('year', 'DESC')
            ->pluck('year')
            ->toArray();

        return view('detail_company_chart.index', compact('companies', 'availableYears', 'selectedYear'));
    }

    public function show(Request $request, $companyId)
    {
        $company = Company::select('company_name', 'id', 'company_code')
            ->where('id', $companyId)
            ->first();
    
        if (!$company) {
            $company = Company::select('company_name', 'id', 'company_code')
                ->where('company_code', $companyId)
                ->firstOrFail();
        }
    
        $availableYears = Event::select('year')
            ->groupBy('year')
            ->orderBy('year', 'DESC')
            ->pluck('year')
            ->toArray();
    
        $year = (int) ($request->query('year') ?? \Carbon\Carbon::now()->year);
        $organizationUnit = $request->query('organization-unit');
    
        $targetCompanyCode = trim((string) $company->company_code);           
        $filteredCodes = in_array($targetCompanyCode, ['2000','7000'], true)  
            ? ['2000','7000']
            : [$targetCompanyCode];

        $filteredCodes = array_map(fn($c) => trim((string)$c), $filteredCodes);

    
    
        // \Log::info('--- [DEBUG detailCompanyChart] ---');
        // \Log::info('Param companyId: ' . $companyId);
        // \Log::info('Resolved Company ID: ' . $company->id);
        // \Log::info('Resolved Company Code: ' . $company->company_code);
        // \Log::info('Filtered Company Codes: ', $filteredCodes);
        // \Log::info('Selected Year (hanya untuk chart): ' . $year);
    
        $papersPerYear = \DB::table('papers')
            ->join('teams', 'papers.team_id', '=', 'teams.id')
            ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            ->join('events', 'events.id', '=', 'pvt_event_teams.event_id')
            ->whereIn('teams.company_code', $filteredCodes)
            ->whereRaw("TRIM(LOWER(papers.status)) = 'accepted by innovation admin'")
            ->selectRaw('events.year AS year, COUNT(DISTINCT papers.id) AS total')
            ->groupBy('events.year')
            ->orderBy('events.year')
            ->pluck('total', 'year')
            ->toArray();
    
        // \Log::info('[DetailCompany] papersPerYear', $papersPerYear);
    
        $totalPapersAllYears = (int) \DB::table('papers')
            ->join('teams', 'papers.team_id', '=', 'teams.id')
            ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            ->join('events', 'events.id', '=', 'pvt_event_teams.event_id')
            ->whereIn('teams.company_code', $filteredCodes)
            ->whereRaw("TRIM(LOWER(papers.status)) = 'accepted by innovation admin'")
            ->distinct('papers.id')
            ->count('papers.id');
    
        // \Log::info('[DetailCompany] totalPapersAllYears: ' . $totalPapersAllYears);
    
        $genderBuckets = \DB::table('pvt_members')
            ->join('teams', 'teams.id', '=', 'pvt_members.team_id')
            ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            ->join('events', 'events.id', '=', 'pvt_event_teams.event_id')
            ->join('papers', 'papers.team_id', '=', 'teams.id')
            ->leftJoin('users', 'users.employee_id', '=', 'pvt_members.employee_id')
            ->whereIn('teams.company_code', $filteredCodes)
            ->whereRaw("TRIM(LOWER(papers.status)) = 'accepted by innovation admin'")
            ->where('pvt_members.status', '!=', 'gm')
            ->selectRaw("
                CASE
                    WHEN users.gender IS NULL OR TRIM(users.gender) = '' THEN 'Male'
                    WHEN LOWER(TRIM(users.gender)) IN ('male','laki-laki','laki','m','1','unknown','0') THEN 'Male'
                    WHEN LOWER(TRIM(users.gender)) IN ('female','perempuan','perempua','f') THEN 'Female'
                    ELSE 'Male'
                END AS gender_norm,
                COUNT(DISTINCT CONCAT(pvt_members.employee_id, '-', teams.id)) AS cnt
            ")
            ->groupBy('gender_norm')
            ->pluck('cnt', 'gender_norm');
    
        $maleCount   = (int) ($genderBuckets['Male'] ?? 0);
        $femaleCount = (int) ($genderBuckets['Female'] ?? 0);
        // \Log::info('Gender Buckets (ALL YEARS):', $genderBuckets->toArray());
        // \Log::info('Male Count (ALL YEARS): ' . $maleCount);
        // \Log::info('Female Count (ALL YEARS): ' . $femaleCount);
    
        $outsourceInnovatorData = (int) \DB::table('ph2_members')
            ->join('teams', 'teams.id', '=', 'ph2_members.team_id')
            ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            ->join('events', 'events.id', '=', 'pvt_event_teams.event_id')
            ->join('papers', 'papers.team_id', '=', 'teams.id')
            ->whereIn('teams.company_code', $filteredCodes)
            // NOTE: Tidak dibatasi tahun → total seluruh tahun
            ->whereRaw("TRIM(LOWER(papers.status)) = 'accepted by innovation admin'")
            ->selectRaw("COUNT(DISTINCT CONCAT(LOWER(TRIM(ph2_members.name)), '-', teams.id)) AS cnt")
            ->value('cnt');
    
        // \Log::info('Outsource Innovators Count (ALL YEARS): ' . $outsourceInnovatorData);
    
        $totalInnovators = $maleCount + $femaleCount + $outsourceInnovatorData;
    
        $totalPotentialBenefit = \DB::query()
            ->fromSub(function ($q) use ($filteredCodes) {
                $q->from('papers as p')
                ->join('teams as t', 't.id', '=', 'p.team_id')
                ->whereIn('t.company_code', $filteredCodes)
                ->whereRaw("TRIM(LOWER(p.status)) = 'accepted by innovation admin'")
                ->whereExists(function ($qq) {
                    $qq->select(\DB::raw(1))
                        ->from('pvt_event_teams as pet')
                        ->join('events as e', 'e.id', '=', 'pet.event_id')
                        ->whereColumn('pet.team_id', 't.id')
                        ->where('e.status', 'finish');
                })
                ->select('p.id', 'p.potential_benefit')
                ->groupBy('p.id', 'p.potential_benefit');
            }, 'once_papers')
            ->sum('once_papers.potential_benefit');

        $formattedTotalPotentialBenefit = number_format((float) $totalPotentialBenefit, 0, ',', '.');
        // \Log::info('[DetailCompany] PotentialBenefit TOTAL (ALL YEARS): ' . $totalPotentialBenefit);
    
    
        $totalFinancialBenefit = \DB::query()
            ->fromSub(function ($q) use ($filteredCodes) {
                $q->from('papers as p')
                ->join('teams as t', 't.id', '=', 'p.team_id')
                ->whereIn('t.company_code', $filteredCodes)
                ->whereRaw("TRIM(LOWER(p.status)) = 'accepted by innovation admin'")
                ->whereExists(function ($qq) {
                    $qq->select(\DB::raw(1))
                        ->from('pvt_event_teams as pet')
                        ->join('events as e', 'e.id', '=', 'pet.event_id')
                        ->whereColumn('pet.team_id', 't.id')
                        ->where('e.status', 'finish');
                })
                ->select('p.id', 'p.financial')
                ->groupBy('p.id', 'p.financial'); 
            }, 'once_papers')
            ->sum('once_papers.financial');

        $formattedTotalFinancialBenefit = number_format((float) $totalFinancialBenefit, 0, ',', '.');
        // \Log::info('[DetailCompany] FinancialBenefit TOTAL (ALL YEARS): ' . $totalFinancialBenefit);
        // \Log::info('--- [END DEBUG detailCompanyChart] ---');
    
    
        return view('detail_company_chart.show', [
            'company'                          => $company,
            'availableYears'                   => $availableYears,
            'year'                             => $year,
            'papersPerYear'                    => $papersPerYear,
            'totalPapersAllYears'              => $totalPapersAllYears,
            'totalInnovators'                  => $totalInnovators,
            'maleCount'                        => $maleCount,
            'femaleCount'                      => $femaleCount,
            'outsourceInnovatorData'           => $outsourceInnovatorData,
            'formattedTotalPotentialBenefit'   => $formattedTotalPotentialBenefit,
            'formattedTotalFinancialBenefit'   => $formattedTotalFinancialBenefit,
            'organizationUnit'                 => $organizationUnit,
        ]);
    }



}
