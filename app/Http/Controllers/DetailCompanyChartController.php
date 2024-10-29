<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Event;
use App\Models\Paper;
use App\Models\PvtMember;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

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
    public function show(Request $request, $id)
    {
        // Ambil nama perusahaan dan company_code berdasarkan ID
        $company = Company::select('company_name', 'id', 'company_code')->where('id', $id)->first();

        // Hitung total inovator dan berdasarkan gender
        $innovatorData = PvtMember::join('users', 'pvt_members.employee_id', '=', 'users.employee_id')
            ->join('teams', 'pvt_members.team_id', '=', 'teams.id')
            ->where('teams.company_code', $company->company_code)
            ->whereIn('pvt_members.status', ['leader', 'member'])
            ->select('users.gender', \DB::raw('count(distinct pvt_members.employee_id) as total'))
            ->groupBy('users.gender')
            ->get();

        // Inisialisasi variabel untuk menyimpan hasil
        $totalInnovators = 0;
        $maleCount = 0;
        $femaleCount = 0;

        // Hitung total berdasarkan gender
        foreach ($innovatorData as $data) {
            $totalInnovators += $data->total;
            if ($data->gender === 'Male') {
                $maleCount = $data->total;
            } elseif ($data->gender === 'Female') {
                $femaleCount = $data->total;
            }
        }

        $organizationUnit = $request->query('organization-unit');
        $availableYears = Event::select('year')
            ->groupBy('year')
            ->orderBy('year', 'DESC')
            ->pluck('year')
            ->toArray();


        $year = $request->query('year');
        if ($year === null) {
            $year = Carbon::now()->year;
        }

        return view('detail_company_chart.show', compact(
            'company',
            'totalInnovators',
            'maleCount',
            'femaleCount',
            'organizationUnit',
            'availableYears',
            'year'
        ));
    }
}
