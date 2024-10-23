<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function showDashboard(Request $request)
    {
        $year = $request->input('year') ?? date('Y');
        // Menghitung jumlah tim berdasarkan kategori utama
        $breakthroughInnovation = DB::table('teams')
            ->join('categories', 'categories.id', '=', 'teams.category_id')
            ->where('categories.category_parent', 'BREAKTHROUGH INNOVATION')
            ->count();

        $incrementalInnovation = DB::table('teams')
            ->join('categories', 'categories.id', '=', 'teams.category_id')
            ->where('categories.category_parent', 'INCREMENTAL INNOVATION')
            ->count();

        $ideaBox = DB::table('teams')
            ->join('categories', 'categories.id', '=', 'teams.category_id')
            ->where('categories.category_parent', 'IDEA BOX')
            ->count();

        // Menghitung jumlah tim berdasarkan kategori detail dalam "Breakthrough Innovation"
        $detailBreakthroughInnovationPBB = DB::table('teams')
            ->join('categories', 'categories.id', '=', 'teams.category_id')
            ->where('categories.category_name', 'PRODUK DAN BAHAN BAKU')
            ->count();

        $detailBreakthroughInnovationTPP = DB::table('teams')
            ->join('categories', 'categories.id', '=', 'teams.category_id')
            ->where('categories.category_name', 'TEKHNOLOGY & PROSES PRODUKSI')
            ->count();

        $detailBreakthroughInnovationManagement = DB::table('teams')
            ->join('categories', 'categories.id', '=', 'teams.category_id')
            ->where('categories.category_name', 'MANAGEMENT')
            ->count();

        // Menghitung jumlah tim berdasarkan kategori detail dalam "Incremental Innovation"
        $detailIncrementalInnovationGKMPlant = DB::table('teams')
            ->join('categories', 'categories.id', '=', 'teams.category_id')
            ->where('categories.category_name', 'GKM PLANT')
            ->count();

        $detailIncrementalInnovationGKMOffice = DB::table('teams')
            ->join('categories', 'categories.id', '=', 'teams.category_id')
            ->where('categories.category_name', 'GKM OFFICE')
            ->count();

        $detailIncrementalInnovationPKMPlant = DB::table('teams')
            ->join('categories', 'categories.id', '=', 'teams.category_id')
            ->where('categories.category_name', 'PKM PLANT')
            ->count();

        $detailIncrementalInnovationPKMOffice = DB::table('teams')
            ->join('categories', 'categories.id', '=', 'teams.category_id')
            ->where('categories.category_name', 'PKM OFFICE')
            ->count();

        $detailIncrementalInnovationSSPlant = DB::table('teams')
            ->join('categories', 'categories.id', '=', 'teams.category_id')
            ->where('categories.category_name', 'SS PLANT')
            ->count();

        $detailIncrementalInnovationSSOffice = DB::table('teams')
            ->join('categories', 'categories.id', '=', 'teams.category_id')
            ->where('categories.category_name', 'SS OFFICE')
            ->count();

        // Menghitung jumlah tim berdasarkan kategori detail dalam "Idea Box"
        $detailIdeaBoxIdea = DB::table('teams')
            ->join('categories', 'categories.id', '=', 'teams.category_id')
            ->where('categories.category_name', 'IDEA')
            ->count();

        $availableYears =   Event::select('year')
            ->groupBy('year')
            ->orderBy('year', 'DESC')
            ->pluck('year')
            ->toArray();
        return view('auth.user.home', compact('breakthroughInnovation', 'incrementalInnovation', 'ideaBox', 'detailBreakthroughInnovationPBB', 'detailBreakthroughInnovationTPP', 'detailBreakthroughInnovationManagement', 'detailIncrementalInnovationGKMPlant', 'detailIncrementalInnovationGKMOffice', 'detailIncrementalInnovationPKMPlant', 'detailIncrementalInnovationPKMOffice', 'detailIncrementalInnovationSSPlant', 'detailIncrementalInnovationSSOffice', 'detailIdeaBoxIdea', 'year', 'availableYears'));
    }

}
