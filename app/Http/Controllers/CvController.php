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
        $employee = Auth::user();

        $innovations = DB::table('pvt_members')
            ->select(
                'papers.id',
                'papers.innovation_title',
                'papers.inovasi_lokasi',
                'papers.potensi_replikasi',
                'teams.id as team_id',
                'teams.team_name',
                'teams.status_lomba',
                'categories.category_name as category',
                'events.event_name',
                'events.year',
                DB::raw('(SELECT company_name FROM companies
                      JOIN company_event ON companies.id = company_event.company_id
                      WHERE company_event.event_id = events.id
                      LIMIT 1) as company_name'),
                'certificates.template_path as certificate',
                'pvt_event_teams.status as status',
                'themes.theme_name',
                'pvt_event_teams.is_best_of_the_best',
                'pvt_members.status as member_status',
            )
            ->leftJoin('teams', 'pvt_members.team_id', '=', 'teams.id')
            ->leftJoin('papers', 'teams.id', '=', 'papers.team_id')
            ->leftJoin('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->leftJoin('events', 'pvt_event_teams.event_id', '=', 'events.id')
            ->leftJoin('certificates', 'events.id', '=', 'certificates.event_id')
            ->leftJoin('themes', 'teams.theme_id', '=', 'themes.id')
            ->leftJoin('categories', 'teams.category_id', '=', 'categories.id')
            ->where('pvt_members.employee_id', $employee->employee_id)
            ->where('pvt_event_teams.status', '=', 'Juara')
            ->distinct('papers.id');

        $innovations = $innovations->paginate(10);

        $teamRanks = DB::table('teams')
            ->select('teams.id', 'categories.category_name', 'events.event_name', 
                    DB::raw('(SELECT COUNT(*) + 1 FROM teams AS t
                            JOIN pvt_event_teams AS pet ON t.id = pet.team_id
                            WHERE t.category_id = teams.category_id AND pet.event_id = events.id
                            AND pet.status = pvt_event_teams.status AND t.id < teams.id) as rank'))
            ->leftJoin('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->leftJoin('categories', 'teams.category_id', '=', 'categories.id')
            ->leftJoin('events', 'pvt_event_teams.event_id', '=', 'events.id')
            ->leftJoin('papers', 'teams.id', '=', 'papers.team_id')
            ->leftJoin('pvt_members', 'teams.id', '=', 'pvt_members.team_id')
            ->where('pvt_members.employee_id', $employee->employee_id)
            ->first();

        return view('auth.admin.dokumentasi.cv.index', compact('innovations', 'employee', 'teamRanks'));
    }


    public function generateCertificate(Request $request)
    {

        // Ambil data dari request
        $inovasi = json_decode($request->input('inovasi'), true);
        $employee = json_decode($request->input('employee'), true);
        $teamRanks = json_decode($request->input('team_rank'), true);
        $certificateType = $request->input('certificate_type');

        if(Auth::user()->role == 'Juri'){
            // View Digunakan
            $view = 'auth.admin.dokumentasi.cv.judge-certificate';
            // Data yang akan ditampilkan pada view sertifikat
            $data = [
                'user_name' => $employee['name'],
                'team_name' => $inovasi['team_name'],
                'company_name' => $employee['company_name'],
                'event' => $inovasi['event_name'],
                'template_path' => $inovasi['certificate'],
                'team_rank' => $teamRanks['rank'],
            ];
        } else {
            if($certificateType == 'participant') {
                $view = 'auth.admin.dokumentasi.cv.participant-certificate';
                $data = [
                    'user_name' => $employee['name'],
                    'team_name' => $inovasi['team_name'],
                    'company_name' => $employee['company_name'],
                    'category_name' => $inovasi['theme_name'],
                    'template_path' => $inovasi['certificate'],
                    'team_rank' => $teamRanks['rank'],             
                    'member_status' => $inovasi['member_status'], 
                ];
            } else if ($certificateType == 'team') {
                $view = 'auth.admin.dokumentasi.cv.team-certificate';
                $data = [
                    'innovation_title' => $inovasi['innovation_title'],
                    'team_name' => $inovasi['team_name'],
                    'company_name' => $employee['company_name'],
                    'category_name' => $inovasi['theme_name'],
                    'template_path' => $inovasi['certificate'],
                    'team_rank' => $teamRanks['rank'],
                ];
            }
        }


        // Generate PDF menggunakan dompdf dan view certificate, dengan ukuran A4
        $pdf = Pdf::loadView($view, $data)
            ->setPaper('A4', 'landscape');  // Atur ukuran kertas A4, mode portrait

        // Return PDF ke browser untuk di-download
        return $pdf->download('Sertifikat - ' . $employee['name'] . '.pdf');
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
            ->limit(1)
            ->get();

        // dd($papers);
        // mendapatkan data member berdasarkan id team
        $teamMember = $team->pvtMembers()->with('user')->get();

        return view('auth.admin.dokumentasi.cv.detail', compact('teamMember', 'papers'));
    }
}