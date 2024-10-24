<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Event;
use App\Models\Paper;
use App\Models\PvtMember;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
        $company = Company::select('company_name', 'id')->where('id', $id)->first();

        return view('detail_company_chart.show', compact('company'));
    }
}
