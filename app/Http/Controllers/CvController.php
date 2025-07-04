<?php

namespace App\Http\Controllers;

use App\Models\Paper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
                'events.date_end as event_end',
                DB::raw('(SELECT company_name FROM companies
                      JOIN company_event ON companies.id = company_event.company_id
                      WHERE company_event.event_id = events.id
                      LIMIT 1) as company_name'),
                'certificates.template_path as certificate',
                'certificates.badge_rank_1 as badge_rank_1',
                'certificates.badge_rank_2 as badge_rank_2',
                'certificates.badge_rank_3 as badge_rank_3',
                'pvt_event_teams.status as status',
                'themes.theme_name',
                'pvt_event_teams.is_best_of_the_best',
                'pvt_event_teams.is_honorable_winner',
                'pvt_event_teams.event_id',
                'pvt_members.status as member_status'
            )
            ->leftJoin('teams', 'pvt_members.team_id', '=', 'teams.id')
            ->leftJoin('papers', 'teams.id', '=', 'papers.team_id')
            ->leftJoin('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->leftJoin('events', 'pvt_event_teams.event_id', '=', 'events.id')
            ->leftJoin('certificates', 'events.id', '=', 'certificates.event_id')
            ->leftJoin('themes', 'teams.theme_id', '=', 'themes.id')
            ->leftJoin('categories', 'teams.category_id', '=', 'categories.id')
            ->where('pvt_members.employee_id', $employee->employee_id)
            ->where('events.status', 'finish')
            ->distinct('papers.id');

        $innovations = $innovations->paginate(10);

        $teamRanks = DB::table('teams')
            ->select(
                'teams.id as team_id',
                'categories.category_name',
                'events.event_name',
                'events.year',
                DB::raw('COALESCE(pvt_event_teams.final_score, 0) as score'),
                DB::raw('(
                    SELECT COUNT(*) + 1
                    FROM teams AS t
                    JOIN pvt_event_teams AS pet2 ON t.id = pet2.team_id
                    WHERE t.category_id = teams.category_id
                        AND pet2.event_id = events.id
                        AND COALESCE(pet2.final_score, 0) > COALESCE(pvt_event_teams.final_score, 0)
                ) AS rank')
            )
            ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->join('categories', 'teams.category_id', '=', 'categories.id')
            ->join('events', 'pvt_event_teams.event_id', '=', 'events.id')
            ->join('pvt_members', 'teams.id', '=', 'pvt_members.team_id')
            ->where('pvt_members.employee_id', $employee->employee_id)
            ->get()
            ->keyBy('team_id');

        return view('auth.admin.dokumentasi.cv.index', compact('innovations', 'employee', 'teamRanks'));
    }


    public function generateCertificate(Request $request)
    {

        // Ambil data dari request
        $inovasi = json_decode($request->input('inovasi'), true);
        $employee = json_decode($request->input('employee'), true);
        $teamRanks = json_decode($request->input('team_rank'), true);
        $certificateType = $request->input('certificate_type');

        $judgeEvents = DB::table('judges')
            ->join('events', 'judges.event_id', '=', 'events.id')
            ->join('pvt_event_teams', 'events.id', '=', 'pvt_event_teams.event_id')
            ->join('teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            ->leftJoin('certificates', 'events.id', '=', 'certificates.event_id') 
            ->select('events.event_name', 'teams.team_name', 'certificates.template_path')
            ->where('judges.employee_id', $employee['employee_id'])
            ->where('judges.status', 'active')
            ->where('events.status', 'finish')
            ->first();
        
        $bodData = DB::table('bod_events')
            ->leftJoin('users', 'users.employee_id', '=', 'bod_events.employee_id')
            ->where('bod_events.event_id', $inovasi['event_id'])
            ->select('users.name as bod_name', 'users.position_title as title')
            ->first();
            
        Carbon::setLocale('id');

        if(Auth::user()->role == 'Juri' && $judgeEvents){
            // View Digunakan
            $view = 'auth.user.profile.judge-certificate';
            // Data yang akan ditampilkan pada view sertifikat
            $data = [
                'user_name' => $employee['name'],
                'team_name' => $inovasi['team_name'],
                'company_name' => $employee['company_name'],
                'event_name' => $inovasi['event_name'],
                'template_path' => $inovasi['template_path'],
                'event_end_date' => $inovasi['event_end'],
            ];
            $certificateName = $employee['name'];
        } else {
            if($certificateType == 'participant') {
                $view = 'auth.admin.dokumentasi.cv.participant-certificate';
                $data = [
                    'user_name' => $employee['name'],
                    'team_name' => $inovasi['team_name'],
                    'company_name' => $employee['company_name'],
                    'category_name' => $inovasi['category'],
                    'template_path' => $inovasi['certificate'],
                    'team_rank' => $teamRanks,             
                    'member_status' => $inovasi['member_status'],
                    'event_end_date' => $inovasi['event_end'],
                    'bodName' => $bodData->bod_name,
                    'bodTitle' => $bodData->title
                ];
                $certificateName = $employee['name'];
            } else if ($certificateType == 'team') {
                $view = 'auth.admin.dokumentasi.cv.team-certificate';
                $data = [
                    'innovation_title' => $inovasi['innovation_title'],
                    'team_name' => $inovasi['team_name'],
                    'company_name' => $employee['company_name'],
                    'category_name' => $inovasi['category'],
                    'template_path' => $inovasi['certificate'],
                    'team_rank' => $teamRanks,
                    'event_end_date' => $inovasi['event_end'],
                    'bodName' => $bodData->bod_name,
                    'bodTitle' => $bodData->title,
                    'badge_1' => $inovasi['badge_rank_1'],
                    'badge_2' => $inovasi['badge_rank_2'],
                    'badge_3' => $inovasi['badge_rank_3'],
                ];
                $certificateName = $inovasi['team_name'];
            }
        }


        // Generate PDF menggunakan dompdf dan view certificate, dengan ukuran A4
        $pdf = Pdf::loadView($view, $data)
            ->setPaper('A4', 'landscape');  // Atur ukuran kertas A4, mode portrait

        // Return PDF ke browser untuk di-download
        return $pdf->download('Sertifikat - ' . $certificateName . '.pdf');
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