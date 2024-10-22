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
        $employee_id = Auth::user()->employee_id;

        $data = \DB::table('pvt_members')
            ->join('teams', 'pvt_members.team_id', '=', 'teams.id')
            ->join('categories', 'teams.category_id', '=', 'categories.id')
            // ->join('papers', 'teams.id', '=', 'papers.team_id')
            ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->join('events', 'events.id', '=', 'pvt_event_teams.event_id')
            ->join('certificates', 'events.id', '=', 'certificates.event_id')
            ->where('teams.id', $team_id)
            ->where('pvt_members.employee_id', $employee_id)
            ->select(
                'teams.team_name',
                'events.event_name',
                'events.year',
                'certificates.template_path as certificate'
            )
            ->get();

        dd($data);

        // Generate PDF dari view Blade
        // $pdf = Pdf::loadView('certificate_pdf', compact('data', 'image'))
        //     ->setPaper('a4', 'landscape');

        // Download PDF
        // return $pdf->download('certificate.pdf');

        return view('auth.admin.dokumentasi.cv.certificate', compact('data', 'image'));
    }
}
