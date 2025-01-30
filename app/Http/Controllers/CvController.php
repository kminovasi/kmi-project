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
    public function index()
{
    // get current user employee_id
    $employee = Auth::user();

    $innovations = DB::table('pvt_members')
        ->leftJoin('teams', 'pvt_members.team_id', '=', 'teams.id')
        ->leftJoin('papers', 'teams.id', '=', 'papers.team_id')
        ->leftJoin('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
        ->leftJoin('events', 'pvt_event_teams.event_id', '=', 'events.id')
        ->leftJoin('company_event', 'events.id', '=', 'company_event.event_id')
        ->leftJoin('companies', 'company_event.company_id', '=', 'companies.id')
        ->leftJoin('certificates', 'events.id', '=', 'certificates.event_id')
        ->leftJoin('themes', 'teams.theme_id', '=', 'themes.id')
        ->leftJoin('categories', 'teams.category_id', '=', 'categories.id')
        ->where('pvt_members.employee_id', $employee->employee_id)
        ->where('pvt_event_teams.status', '=', 'Juara')
        ->select(
            'papers.*',
            'teams.id as team_id',
            'teams.team_name',
            'teams.status_lomba',
            'categories.category_name as category',
            'events.event_name',
            'events.year',
            'companies.company_name',
            'certificates.template_path as certificate',
            'pvt_event_teams.status as event_status',
            'themes.id',
            'themes.theme_name',
            'pvt_event_teams.status',
            'pvt_event_teams.is_best_of_the_best',
        );

    $innovations = $innovations->paginate(10);

    return view('auth.admin.dokumentasi.cv.index', compact('innovations', 'employee'));
}

    public function generateCertificate(Request $request)
    {

        // Ambil data dari request
        $userName = $request->input('user_name');
        $teamName = $request->input('team_name');
        $category = $request->input('category');
        $templatePath = $request->input('template_path');

        // Data yang akan ditampilkan pada view sertifikat
        $data = [
            'user_name' => $userName,
            'team_name' => $teamName,
            'category' => $category,
            'template_path' => $templatePath,
        ];

        // Generate PDF menggunakan dompdf dan view certificate, dengan ukuran A4
        $pdf = Pdf::loadView('auth.admin.dokumentasi.cv.certificate', $data)
            ->setPaper('A4', 'landscape');  // Atur ukuran kertas A4, mode portrait

        // Return PDF ke browser untuk di-download
        return $pdf->download('Sertifikat - ' . $userName . '.pdf');
    }

    function detail($id)
    {

        $team = Team::findOrFail($id);

        // Ambil tim berdasarkan team_id
        $papers = DB::table('teams')
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
}
