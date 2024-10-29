<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Paper;
use App\Models\Team;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

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
}
