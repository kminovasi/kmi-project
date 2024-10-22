<?php

namespace App\Http\Controllers;

use App\Models\Paper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Team;
use Illuminate\Support\Facades\DB;

class CvController extends Controller
{
    function index()
    {

        // get current user employee_id
        $employee_id = Auth::user()->employee_id;

        $innovations = \DB::table('pvt_members')
            ->join('teams', 'pvt_members.team_id', '=', 'teams.id')
            ->join('papers', 'teams.id', '=', 'papers.team_id')
            ->where('pvt_members.employee_id', $employee_id)
            ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->join('events', 'pvt_event_teams.event_id', '=', 'events.id')
            ->join('themes', 'teams.theme_id', '=', 'themes.id')
            ->join('categories', 'teams.category_id', '=', 'categories.id')
            ->select(
                'papers.*',
                'teams.team_name',
                'teams.status_lomba',
                'categories.category_name as category',
                'events.event_name',
                'events.year',
                'pvt_event_teams.status as event_status',
                'themes.id',
                'themes.theme_name',
                'pvt_event_teams.status',
                'pvt_event_teams.is_best_of_the_best',
            );

        $innovations = $innovations->paginate(10);

        return view('auth.admin.dokumentasi.cv.index', compact('innovations'));
    }

    function detail($id)
    {

        $team = Team::findOrFail($id);

        // Ambil tim berdasarkan team_id
        $papers = \DB::table('teams')
            ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->join('papers', 'teams.id', '=', 'papers.team_id')
            ->join('events', 'pvt_event_teams.event_id', '=', 'events.id')
            ->join('themes', 'teams.theme_id', '=', 'themes.id')
            ->leftJoin('document_supportings', 'papers.id', '=', 'document_supportings.paper_id')
            ->where('teams.id', $id)
            ->select(
                'papers.*',
                'pvt_event_teams.status as team_status',
                'pvt_event_teams.total_score_on_desk',
                'pvt_event_teams.total_score_presentation',
                'pvt_event_teams.total_score_caucus',
                'pvt_event_teams.final_score',
                'pvt_event_teams.is_best_of_the_best',
                'themes.theme_name',
                'events.event_name',
                'document_supportings.path'
            )
            ->get();

        // dd($papers);
        // mendapatkan data member berdasarkan id team
        $teamMember = $team->pvtMembers()->with('user')->get();

        return view('auth.admin.dokumentasi.cv.detail', compact('teamMember', 'papers'));
    }

    public function generateCertificate($team_id)
    {
        $employee = Auth::user();
        $employeeName = $employee->name;

        $data = \DB::table('pvt_members')
            ->join('teams', 'pvt_members.team_id', '=', 'teams.id')
            ->join('categories', 'teams.category_id', '=', 'categories.id')
            ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->join('events', 'events.id', '=', 'pvt_event_teams.event_id')
            ->join('companies', 'events.company_code', '=', 'companies.company_code')
            ->join('certificates', 'events.id', '=', 'certificates.event_id')
            ->where('teams.id', $team_id)
            ->where('pvt_members.employee_id', $employee->employee_id)
            ->select(
                'teams.team_name',
                'events.event_name',
                'events.year',
                'categories.category_name',
                'companies.company_name',
                'certificates.template_path as certificate',
                'pvt_event_teams.status as pvt_status',
                'pvt_event_teams.is_best_of_the_best',
                \DB::raw("'$employeeName' as employee_name")
            )
            ->first();

        // Generate PDF dari view Blade
        $pdf = Pdf::loadView('auth.admin.dokumentasi.cv.certificate', compact('data'))
            ->setPaper('a4', 'landscape');

        // Download PDF
        return $pdf->download('certificate.pdf');
    }
}
