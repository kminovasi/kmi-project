<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BeritaAcara;
use App\Models\Event;
use App\Models\Team;
use App\Models\Category;
use App\Models\PvtEventTeam;
use App\Models\PvtMember;
use App\Models\PvtAssessmentEvent;
use App\Models\Evidence;
use App\Models\BodEvent;
use Mpdf\Mpdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;

use Illuminate\Support\Facades\DB;

class BeritaAcaraController extends Controller
{
    public function index(){
        $data = BeritaAcara::join('events', 'berita_acaras.event_id', 'events.id')
                            ->select('berita_acaras.*', 'events.id as eventID', 'events.event_name', 'events.event_name', 'events.year', 'events.date_start', 'events.date_end')
                            ->get();
        $event = Event::where('status', 'active')->get();
        return view('auth.admin.berita-acara.index', ['data' => $data, 'event' => $event]);
    }
    public function store(Request $request){

        try {
            DB::beginTransaction();
            BeritaAcara::create([
                'event_id' => $request->input('event_id'),
                'no_surat' => $request->input('no_surat'),
                'jenis_event' => $request->input('jenis_event'),
                'penetapan_juara' => $request->input('penetapan_juara')
            ]);

            Event::where('id', $request->input('event_id'))
                ->update([
                    'status' => 'finish'
                ]);

            $assessment_event_poin_bi = PvtAssessmentEvent::where('event_id', $request->input('event_id'))
                                                    ->where('category', 'BI/II')
                                                    ->where('status_point', 'active')
                                                    ->limit(1)
                                                    ->pluck('id')
                                                    ->toArray();

            $assessment_event_poin_idea = PvtAssessmentEvent::where('event_id', $request->input('event_id'))
                                                    ->where('category', 'IDEA')
                                                    ->where('status_point', 'active')
                                                    ->limit(1)
                                                    ->pluck('id')
                                                    ->toArray();

            // dd($assessment_event_poin_bi);
            $categoryID_list = Category::whereNot('category_parent', 'IDEA BOX')->pluck('id')->toArray();

            if(!empty($assessment_event_poin_bi)){
                foreach($categoryID_list as $categoryID){
                    $category_name = Category::where('id', '=', $categoryID)->pluck('category_name')[0];
                    $id_teams = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                            ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                            ->where('teams.category_id', '=', $categoryID)
                            ->where('pvt_event_teams.event_id', '=', $request->input('event_id'))
                            ->where('pvt_assesment_team_judges.stage', 'presentation')
                            ->groupBy('pvt_event_teams.id', 'teams.id')
                            ->select('teams.id')
                            ->orderByRaw('ROUND(ROUND(SUM(pvt_assesment_team_judges.score), 2) / COUNT(CASE WHEN pvt_assesment_team_judges.assessment_event_id = ? THEN pvt_assesment_team_judges.assessment_event_id END), 2) DESC', [$assessment_event_poin_bi])
                            ->get()
                            ->toArray();

                    if(!empty($id_teams)){
                        $no = 0;
                        foreach($id_teams as $id_team){
                            $idMembers = PvtMember::where('team_id', '=', $id_team)
                                                    ->pluck('id')
                                                    ->toArray();
                            $no++;
                            $prestasi = '';
                            if($no == 1)
                                $prestasi = 'Juara 1';
                            elseif($no == 2)
                                $prestasi = 'Juara 2';
                            elseif($no == 3)
                                $prestasi = 'Juara 3';
                            else
                                $prestasi = 'Peserta';

                            foreach($idMembers as $idMember){
                                Evidence::create([
                                    'member_id' => $idMember,
                                    'team_id' => $id_team['id'],
                                    'event_name' => Event::where('id', $request->input('event_id'))->pluck('event_name')->first(),
                                    'prestasi' =>  $prestasi,
                                    'year'  => Event::where('id', $request->input('event_id'))->pluck('year')->first()
                                ]);

                            }
                        }
                    }
                }
            }

            if(!empty($assessment_event_poin_idea)){
                $id_teams_idea = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                            ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                            ->join('categories', 'teams.category_id', '=', 'categories.id')
                            ->where('categories.category_parent', '=', 'IDEA BOX')
                            ->where('pvt_event_teams.event_id', '=', $request->input('event_id'))
                            ->where('pvt_assesment_team_judges.stage', 'presentation')
                            ->groupBy('pvt_event_teams.id', 'teams.id')
                            ->select('teams.id')
                            ->orderByRaw('ROUND(ROUND(SUM(pvt_assesment_team_judges.score), 2) / COUNT(CASE WHEN pvt_assesment_team_judges.assessment_event_id = ? THEN pvt_assesment_team_judges.assessment_event_id END), 2) DESC', [$assessment_event_poin_idea])
                            ->get()
                            ->toArray();

                if(!empty($id_teams_idea)){
                    $no = 0;
                    foreach($id_teams_idea as $id_team_idea){
                        $idMembers = PvtMember::where('team_id', '=', $id_team_idea)
                                                ->pluck('id')
                                                ->toArray();
                        $no++;
                        $prestasi = '';
                        if($no == 1)
                            $prestasi = 'Juara 1';
                        elseif($no == 2)
                            $prestasi = 'Juara 2';
                        elseif($no == 3)
                            $prestasi = 'Juara 3';
                        else
                            $prestasi = 'Peserta';
                        foreach($idMembers as $idMember){
                            Evidence::create([
                                'member_id' => $idMember,
                                'team_id' => $id_team_idea['id'],
                                'event_name' => Event::where('id', $request->input('event_id'))->pluck('event_name')->first(),
                                'prestasi' =>  $prestasi,
                                'year'  => Event::where('id', $request->input('event_id'))->pluck('year')->first()
                            ]);
                        }
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollback();
            return redirect()->route('assessment.penetapanJuara')->withErrors('Error: ' .$e->getMessage());
        }
        return redirect()->route('assessment.penetapanJuara')->with('success', 'Data Berhasil disimpan');
    }
    public function showPDF($id)
    {
             // Get the year
        $category = Category::all();
        $data = BeritaAcara::join('events', 'berita_acaras.event_id', 'events.id')
                ->where('berita_acaras.id', $id)
                ->select('berita_acaras.*', 'events.id as eventID', 'events.event_name', 'events.event_name', 'events.year', 'events.date_start', 'events.date_end')
                ->first();
        $idEvent = $data->eventID;

        $carbonInstance =  Carbon::parse($data->penetapan_juara);
        setlocale(LC_TIME, 'id_ID');
        // dd($carbonInstance->isoFormat('DD'));
        $day = $carbonInstance->isoFormat('dddd');
        $date = numberToWords($carbonInstance->isoFormat('D'));
        $month = $carbonInstance->isoFormat('MMMM');
        $year = numberToWords($carbonInstance->isoFormat('YYYY'));

        $carbonInstance_startDate = Carbon::parse($data->date_start);
        $carbonInstance_endDate = Carbon::parse($data->date_end);

        $categoryID_list = Category::whereNot('category_parent', 'IDEA BOX')->pluck('id')->toArray();

        $assessment_event_poin_bi = PvtAssessmentEvent::where('event_id', $idEvent)
                                                    ->where('category', 'BI/II')
                                                    ->where('status_point', 'active')
                                                    ->limit(1)
                                                    ->pluck('id')
                                                    ->toArray();

        $assessment_event_poin_idea = PvtAssessmentEvent::where('event_id', $idEvent)
                                                    ->where('category', 'IDEA')
                                                    ->where('status_point', 'active')
                                                    ->limit(1)
                                                    ->pluck('id')
                                                    ->toArray();


        // dd($assessment_event_poin_bi);
        $juara = [];
        foreach($categoryID_list as $categoryID){
            $category_name = Category::where('id', '=', $categoryID)->pluck('category_name')[0];
            if($assessment_event_poin_bi){
                $juara[$category_name] = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                                        ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                                        ->join('papers', 'papers.team_id', '=', 'teams.id')
                                        ->join('companies', 'companies.company_code', 'teams.company_code')
                                        ->where('teams.category_id', '=', $categoryID)
                                        ->where('pvt_event_teams.status', '=', 'Juara')
                                        ->where('pvt_event_teams.event_id', '=', $idEvent)
                                        ->where('pvt_assesment_team_judges.stage', 'presentation')
                                        ->groupBy('pvt_event_teams.id', 'teams.team_name' , 'papers.innovation_title', 'companies.company_name')
                                        ->select('teams.team_name as teamname', 'papers.innovation_title', 'companies.company_name')
                                        ->orderByRaw('ROUND(ROUND(SUM(pvt_assesment_team_judges.score), 2) / COUNT(CASE WHEN pvt_assesment_team_judges.assessment_event_id = ? THEN pvt_assesment_team_judges.assessment_event_id END), 2) DESC', [$assessment_event_poin_bi])
                                        ->take(3)
                                        ->get()
                                        ->toArray();
            }else{
                $juara[$category_name] = [];
            }

        }
        if($assessment_event_poin_idea){
            $juara["IDEA"] = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                        ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                        ->join('categories', 'teams.category_id', '=', 'categories.id')
                        ->join('papers', 'papers.team_id', '=', 'teams.id')
                        ->join('companies', 'companies.company_code', 'teams.company_code')
                        ->where('categories.category_parent', '=', 'IDEA BOX')
                        ->where('pvt_event_teams.status', '=', 'Juara')
                        ->where('pvt_event_teams.event_id', '=', $idEvent)
                        ->where('pvt_assesment_team_judges.stage', 'presentation')
                        ->groupBy('pvt_event_teams.id', 'teams.team_name', 'papers.innovation_title', 'companies.company_name')
                        ->select('teams.team_name as teamname', 'papers.innovation_title', 'companies.company_name')
                        ->orderByRaw('ROUND(ROUND(SUM(pvt_assesment_team_judges.score), 2) / COUNT(CASE WHEN pvt_assesment_team_judges.assessment_event_id = ? THEN pvt_assesment_team_judges.assessment_event_id END), 2) DESC', [$assessment_event_poin_idea])
                        ->take(3)
                        ->get()
                        ->toArray();
        }else{
            $juara["IDEA"] = [];
        }

        if($assessment_event_poin_bi){
            $juara['Best Of The Best'] = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                                ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                                ->join('papers', 'papers.team_id', '=', 'teams.id')
                                ->join('companies', 'companies.company_code', 'teams.company_code')
                                ->where('pvt_event_teams.status', '=', 'Juara')
                                ->where('pvt_event_teams.event_id', '=', $idEvent)
                                ->where('pvt_assesment_team_judges.stage', 'presentation')
                                ->groupBy('pvt_event_teams.id', 'teams.team_name' , 'papers.innovation_title', 'companies.company_name')
                                ->select('teams.team_name as teamname', 'papers.innovation_title', 'companies.company_name')
                                ->orderByRaw('ROUND(ROUND(SUM(pvt_assesment_team_judges.score), 2) / COUNT(CASE WHEN pvt_assesment_team_judges.assessment_event_id = ? THEN pvt_assesment_team_judges.assessment_event_id END), 2) DESC', [$assessment_event_poin_bi])
                                ->take(1)
                                ->get()
                                ->toArray();
                            // dd($juara['Best Of The Best']);
        }else{
            $juara['Best Of The Best'] = [];
        }

        $bods = BodEvent::join('users', 'users.employee_id', '=', 'bod_events.employee_id')
                        ->where('event_id', '=', $idEvent)
                        ->select('users.name', 'users.position_title')
                        ->get()
                        ->toArray();


        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);
        $html = view('auth.admin.berita-acara.pdf', compact(
                        'data', 'day', 'date', 'month', 'year', 'carbonInstance', 'juara', 'category', 'bods', 'carbonInstance_startDate', 'carbonInstance_endDate'))->render();

        $mpdf->WriteHTML($html);
        $content = $mpdf->Output('', 'S');

        return response($content)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="filename.pdf"');
    }
    public function downloadPDF($id)
    {
        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);

        $html = view('auth.admin.berita-acara.pdf')->render();

        $mpdf->WriteHTML($html);

        $mpdf->Output('filename.pdf', 'D');
    }
}
