<?php

namespace App\Http\Controllers;

use App\Models\TemplateAssessmentPoint;
use App\Models\Event;
use Illuminate\Support\Facades\Session;
use App\Models\Company;
use Exception;
use App\Models\AssessmentPoint;
use App\Models\PvtAssessmentEvent;
use App\Models\pvtAssesmentTeamJudge;
use App\Models\PvtEventTeam;
use App\Models\Team;
use App\Models\NewSofi;
use App\Models\Category;
use App\Models\SummaryExecutive;
use App\Models\MinimumscoreEvent;
use App\Models\BodEventValue;
use App\Models\BodEvent;
use App\Models\SummaryPPT;
use App\Models\BeritaAcara;
use App\Models\Judge;
use App\Models\keputusanBOD;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Mpdf\Mpdf;
use App\Http\Requests\assessmentTemplateRequests;
use App\Http\Requests\assessmentPointRequests;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AssessmentController extends Controller
{
    //
    // public function index(){
    //     return view('auth.juri.assessment');
    // }
    public function showTemplate()
    {
        $events = Event::whereNot('status', 'finish')->get();

        return view('auth.admin.assessment.template.index', compact('events'));
    }
    public function createTemplate()
    {
        // $rows = TemplateAssessmentPoint::get();
        return view('auth.admin.assessment.template.create');
    }
    public function storeTemplate(TemplateAssessmentPoint $newTemplate, assessmentTemplateRequests $request)
    {
        // dd($request->all());
        try {
            // Memulai transaksi
            DB::beginTransaction();

            $newTemplate = TemplateAssessmentPoint::UpdateOrCreate([
                'point' => $request->input('point'),
                'detail_point' => $request->input('detail_point'),
                'pdca' => $request->input('pdca'),
                'score_max' => $request->input('score_max'),
                'category' => $request->input('category'),
                'stage' => $request->input('stage')
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->route('assessment.show.template')->withErrors('Error: ' . $e->getMessage());
        }

        return redirect()->route('assessment.show.template')->with('success', 'Data successful'); // masih belom tau
    }

    public function updateTemplate(assessmentTemplateRequests $request, $id)
    {
        // dd($request->all());
        $dataTemplate = TemplateAssessmentPoint::findOrFail($id);

        $dataTemplate->update([
            'point' => $request->point,
            'detail_point' => $request->detail_point,
            'pdca' => $request->pdca,
            'score_max' => $request->score_max,
            'stage' => $request->stage
        ]);

        return redirect()->route('assessment.show.template')->with('success', 'Data berhasil diperbarui');
    }

    public function deleteTemplate($id)
    {

        $dataTemplate = TemplateAssessmentPoint::find($id);
        if (!$dataTemplate) {
            return redirect()->route('assessment.show.template')->with('error', 'Data tidak ditemukan');
        }
        $dataTemplate->delete();
        return redirect()->route('assessment.show.template')->with('success', 'Data berhasil dihapus');
    }

    public function storeAssignPoint(Request $request)
    {
        // dd($request->all());
        $assessmentPointIds = $request->input('assessment_poin_id');
        // dd($assessmentPointIds);
        $dataAssessment = TemplateAssessmentPoint::whereIn('id', $assessmentPointIds)->get();
        DB::beginTransaction();
        try {
            foreach ($dataAssessment as $data) {
                foreach ($request->events as $event) {
                    $id_assessmentPoint = PvtAssessmentEvent::UpdateOrCreate([
                        'event_id' => $event,
                        'point' => $data['point'],
                        'detail_point' => $data['detail_point'],
                        'pdca' => $data['pdca'],
                        'score_max' => $data['score_max'],
                        'stage' => $data['stage'],
                        'category' => $request->input('category')
                    ])['id'];
                }
            }

            Session::put('buttonStatus', 'none');
            // dd(session()->all());

            // Loop melalui $allEvent
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // dd($e->getMessage());
            return redirect()->route('assessment.show.template')->withErrors('Error: ' . $e->getMessage());
        }
        return redirect()->route('assessment.show.template')->with('success', 'Data has been recorded'); // masih belom tau
    }


    public function showAssessmentPoint()
    {
        $checkStatus = Auth::user()->role;
        if ($checkStatus == 'Admin') {
            $data_event = Event::where('company_code', auth()->user()->company_code)
                ->whereNot('status', 'finish')
                ->get();
        } elseif ($checkStatus == 'Superadmin') {
            $data_event = Event::whereNot('status', 'finish')
                ->get();
        }
        // $data_year = Event::distinct()->pluck('year');
        //dd($data_event);

        return view('auth.admin.assessment.assessment_point', [
            'data_event' => $data_event,
            // 'data_year' => $data_year
        ]);
    }

    public function updateAssessmentPoint(assessmentPointRequests $request, $id)
    {
        // dd($request->all());
        $dataTemplate = PvtAssessmentEvent::findOrFail($id);

        $dataTemplate->update([
            // 'point' => $request->point,
            'detail_point' => $request->detail_point,
            'score_max' => $request->score_max,
        ]);

        return redirect()->route('assessment.show.point')->with('success', 'Data berhasil diperbarui');
    }

    public function deleteAssessmentPoint($id)
    {

        $dataTemplate = PvtAssessmentEvent::findOrFail($id);
        if (!$dataTemplate) {
            return redirect()->route('assessment.show.point')->with('error', 'Data tidak ditemukan');
        }
        $dataTemplate->delete();
        return redirect()->route('assessment.show.point')->with('success', 'Data berhasil dihapus');
    }

    public function changeStatusAssessmentPoint(Request $request)
    {
        try {
            DB::beginTransaction();

            if ($request->assessment_poin_id == null)
                $request->assessment_poin_id = [];

            $event_id = $request->event;

            // // Filter out undefined values from the assessment_poin_id array
            // $assessmentPoinIds = array_filter($request->assessment_poin_id, function($value) {
            //  return $value !== 'undefined';
            // });

            $dataAssessmentPoint = PvtAssessmentEvent::where('event_id', $event_id)
                ->where('category', $request->category)
                ->where('stage', 'on desk')
                ->whereNotIn('id', $request->assessment_poin_id)
                //->whereNotIn('id', $assessmentPoinIds)
                // ->pluck('id')
                ->get();
            $dataEventTeam = PvtEventTeam::where('event_id', $event_id)
                ->pluck('id')
                ->toArray();
            // dd($request->assessment_poin_id);
            foreach ($request->assessment_poin_id as $poin_id) {
                PvtAssessmentEvent::where('id', $poin_id)->update(['status_point' => 'active']);
                foreach ($dataEventTeam as $eventTeam) {
                    $dataJudgeInPvtAssessment = pvtAssesmentTeamJudge::distinct()
                        ->where('event_team_id', $eventTeam)
                        ->select('judge_id')
                        ->get();
                    // dd($dataJudgeInPvtAssessment);
                    if ($dataJudgeInPvtAssessment->count()) {
                        foreach ($dataJudgeInPvtAssessment as $judge) {
                            pvtAssesmentTeamJudge::updateOrCreate([
                                'event_team_id' => $eventTeam,
                                'assessment_event_id' => $poin_id,
                                'judge_id'  => $judge->judge_id,
                                'stage' => 'on desk'
                            ]);
                        }
                    } else {
                        pvtAssesmentTeamJudge::updateOrCreate([
                            'event_team_id' => $eventTeam,
                            'assessment_event_id' => $poin_id,
                            'stage' => 'on desk'
                        ]);
                    }
                }
            }

            foreach ($dataAssessmentPoint as $AssessmentPoint) {
                PvtAssessmentEvent::where('id', $AssessmentPoint->id)->update(['status_point' => 'nonactive']);
                pvtAssesmentTeamJudge::where('assessment_event_id', $AssessmentPoint->id)->delete();
            }

            MinimumscoreEvent::updateOrCreate([
                'event_id' => $event_id,
                // 'year'  => $request->year
            ], [
                'score_minimum_oda' => $request->minimumscore_oda,
                'score_minimum_pa' => $request->minimumscore_pa,
                'category' => $request->category
            ]);


            DB::commit();
            // Set btnAssignStatus sesuai kondisi
            // $event = Event::findOrFail($id);
            // $event->btnAssignStatus = 'disabled'; // Atau 'enabled' sesuai logika bisnis Anda
            // $event->save();

            // Set sesi untuk menandai bahwa tombol telah diklik
            Session::put('buttonStatus', 'disabled');
            // $allSessions = Session::all();
            // dd($btnAssignStatus, $allSessions);

            return redirect()->back()->with('success', 'Data berhasil diubah');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors('Error: ' . $e->getMessage());
        }
    }

    public function assessmentValue_oda($id)
    {
        // code untuk menampilkan detail team
        $datas = PvtEventTeam::join('teams', 'teams.id', 'pvt_event_teams.team_id')
            ->join('papers', 'papers.team_id', '=', 'teams.id')
            ->join('categories', 'categories.id', '=', 'teams.category_id')
            ->where('pvt_event_teams.id', $id)
            ->select('team_name', 'innovation_title', 'category_name', 'pvt_event_teams.event_id', 'pvt_event_teams.id as event_team_id', 'pvt_event_teams.status as status_event', 'proof_idea')
            ->first();
        // dd($datas);

        $datas_juri = pvtAssesmentTeamJudge::join('judges', 'pvt_assesment_team_judges.judge_id', '=', 'judges.id')
            ->join('users', 'users.employee_id', '=', 'judges.employee_id')
            ->where('event_team_id', $id)
            ->where('pvt_assesment_team_judges.stage', 'on desk')
            ->select(['judge_id', 'users.name as name', 'users.employee_id'])
            // ->distinct()
            ->groupBy('judge_id', 'users.name', 'users.employee_id')
            // ->pluck('judge_id')
            ->get();

        //code untuk menampilkan atau membuat menjadi null pada sofi
        $sofiData = NewSofi::join('pvt_event_teams', 'pvt_event_teams.id', '=', 'new_sofi.event_team_id')
            ->where('pvt_event_teams.id', $id)->first();
        if (!isset($sofiData)) {
            $sofiData = new NewSofi();
            $sofiData->event_team_id = $id;
            $sofiData->recommend_category = null;
            $sofiData->strength = null;
            $sofiData->opportunity_for_improvement = null;
            $sofiData->save();
        }
        // dd($sofiData);
        return view('auth.juri.assessment_oda', compact('datas', 'sofiData', 'datas_juri'));
    }

    public function assessmentValue_pa($id)
    {
        // code untuk menampilkan detail team
        $datas = PvtEventTeam::join('teams', 'teams.id', 'pvt_event_teams.team_id')
            ->join('papers', 'papers.team_id', '=', 'teams.id')
            ->join('categories', 'categories.id', '=', 'teams.category_id')
            ->where('pvt_event_teams.id', $id)
            ->select('team_name', 'innovation_title', 'category_name', 'pvt_event_teams.event_id', 'pvt_event_teams.id as event_team_id', 'pvt_event_teams.status as status_event')
            ->first();
        // dd($datas);

        $datas_juri = pvtAssesmentTeamJudge::join('judges', 'pvt_assesment_team_judges.judge_id', '=', 'judges.id')
            ->join('users', 'users.employee_id', '=', 'judges.employee_id')
            ->where('event_team_id', $id)
            ->where('pvt_assesment_team_judges.stage', 'presentation')
            ->select(['judge_id', 'users.name as name', 'users.employee_id'])
            // ->distinct()
            ->groupBy('judge_id', 'users.name', 'users.employee_id')
            // ->pluck('judge_id')
            ->get();

        //code untuk menampilkan atau membuat menjadi null pada sofi
        $sofiData = NewSofi::join('pvt_event_teams', 'pvt_event_teams.id', '=', 'new_sofi.event_team_id')
            ->where('pvt_event_teams.id', $id)->first();
        if (!isset($sofiData)) {
            $sofiData = new NewSofi();
            $sofiData->event_team_id = $id;
            $sofiData->recommend_category = null;
            $sofiData->strength = null;
            $sofiData->opportunity_for_improvement = null;
            $sofiData->save();
        }
        // dd($sofiData);
        return view('auth.juri.assessment_pa', compact('datas', 'sofiData', 'datas_juri'));
    }

    public function assesmentValue_caucus($id)
    {
        // code untuk menampilkan detail team
        $datas = PvtEventTeam::join('teams', 'teams.id', 'pvt_event_teams.team_id')
            ->join('papers', 'papers.team_id', '=', 'teams.id')
            ->join('categories', 'categories.id', '=', 'teams.category_id')
            ->where('pvt_event_teams.id', $id)
            ->select('team_name', 'innovation_title', 'category_name', 'pvt_event_teams.event_id', 'pvt_event_teams.id as event_team_id', 'pvt_event_teams.status as status_event')
            ->first();
        // dd($datas);

        $datas_juri = pvtAssesmentTeamJudge::join('judges', 'pvt_assesment_team_judges.judge_id', '=', 'judges.id')
            ->join('users', 'users.employee_id', '=', 'judges.employee_id')
            ->where('event_team_id', $id)
            ->where('pvt_assesment_team_judges.stage', 'caucus')
            ->select(['judge_id', 'users.name as name', 'users.employee_id'])
            // ->distinct()
            ->groupBy('judge_id', 'users.name', 'users.employee_id')
            // ->pluck('judge_id')
            ->get();

        //code untuk menampilkan atau membuat menjadi null pada sofi
        $sofiData = NewSofi::join('pvt_event_teams', 'pvt_event_teams.id', '=', 'new_sofi.event_team_id')
            ->where('pvt_event_teams.id', $id)->first();
        if (!isset($sofiData)) {
            $sofiData = new NewSofi();
            $sofiData->event_team_id = $id;
            $sofiData->recommend_category = null;
            $sofiData->strength = null;
            $sofiData->opportunity_for_improvement = null;
            $sofiData->save();
        }

        // dd($sofiData);
        return view('auth.juri.assessment_caucus', compact('datas', 'sofiData', 'datas_juri'));
    }

    // public function scoreJuri(Request $request){
    //     // dd($request->all());
    //     $scoreData = $request->input('score');

    //     foreach ($scoreData as $keyId => $score) {
    //         // Use the key_id as a reference to update the corresponding record
    //         $data = PvtAssessmentTeam::where('id', $keyId)->first();

    //         if ($data) {
    //             $data->score = $score;
    //             $data->save();
    //         }
    //     }

    //     return redirect()->back()->with('success', 'Scores updated successfully');
    // }

    // FIXME: INI FUNGSI YG DIPAKE SUBMIT SCORE
    public function submitJuri(Request $request, $event_team_id)
    {
        try {
            foreach ($request->score as $id => $score) {
                pvtAssesmentTeamJudge::where('id', $id)
                    ->update([
                        'score' => $score,
                    ]);
            }

            NewSofi::where('event_team_id', $event_team_id)
                ->update([
                    'strength' => $request->sofi_strength,
                    'opportunity_for_improvement' => $request->sofi_opportunity,
                    'recommend_category' => $request->recommendation,
                    'suggestion_for_benefit' => $request->suggestion_for_benefit
                ]);

            return redirect()->back()->with('success', 'Submit assessment successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error: ' . $e->getMessage());
        }
    }

    public function addJuri(Request $request)
    {
        try {
            // dd($request->all());
            $category = PvtEventTeam::join('teams', 'pvt_event_teams.team_id', '=', 'teams.id')
                ->join('categories', 'categories.id', '=', 'teams.category_id')
                ->where('pvt_event_teams.id', $request->event_team_id)
                ->pluck('category_parent')
                ->first();

            if ($category == 'IDEA BOX')
                $category = 'IDEA';
            else
                $category = 'BI/II';

            $data_assessment_event = PvtEventTeam::join('pvt_assessment_events', function ($join) {
                $join->on('pvt_assessment_events.event_id', '=', 'pvt_event_teams.event_id');
                // ->on('pvt_assessment_events.year', '=', 'pvt_event_teams.year');
            })
                ->where('pvt_event_teams.id', $request->event_team_id)
                ->where('pvt_assessment_events.category', $category)
                ->where('pvt_assessment_events.status_point', 'active')
                ->where('pvt_assessment_events.stage', $request->input('stage'))
                ->pluck(
                    'pvt_assessment_events.id as assessment_event_id',
                )
                ->toArray();

            $dataAssesmentTeamJudge_null = pvtAssesmentTeamJudge::where('event_team_id', $request->event_team_id)
                ->whereIn('assessment_event_id', $data_assessment_event)
                ->where('judge_id', null)
                ->get();

            if ($dataAssesmentTeamJudge_null->count()) {
                foreach ($dataAssesmentTeamJudge_null as $assessment_event_id_null) {
                    // dd($assessment_event_id_null->id);
                    pvtAssesmentTeamJudge::updateOrCreate([
                        'id' => $assessment_event_id_null->id,
                    ], [
                        'judge_id' => $request->judge_id,
                    ]);
                }
            } else {
                foreach ($data_assessment_event as $assessment_event_id) {
                    pvtAssesmentTeamJudge::updateOrCreate([
                        'judge_id' => $request->judge_id,
                        'event_team_id' => $request->event_team_id,
                        'assessment_event_id' => $assessment_event_id,
                        'stage' => $request->input('stage')
                    ]);
                }
            }
            return redirect()->back()->with('success', 'Judge added successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error: ' . $e->getMessage());
        }
    }

    public function deleteJuri(Request $request)
    {
        try {
            pvtAssesmentTeamJudge::where('judge_id', $request->judge_id)
                ->delete();

            return redirect()->back()->with('success', 'Judge delete successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error: ' . $e->getMessage());
        }
    }

    public function on_desk()
    {
        if (auth()->user()->role == "Superadmin") {
            $data_event = Event::where('status', '=', 'active')->get();
        } elseif (auth()->user()->role == "Admin") {
            $data_event = Event::where('status', '=', 'active')
                ->where('company_code', auth()->user()->company_code)
                ->get();
        } elseif (auth()->user()->role == "Juri") {
            $judge_event_id = Judge::where('employee_id', auth()->user()->employee_id)
                ->where('status', 'active')
                ->pluck('event_id')
                ->toArray();

            $data_event = Event::whereIn('id', $judge_event_id)
                ->where('status', 'active')
                ->get();
        } elseif (auth()->user()->role == "BOD") {
            // Role BOD hanya dapat melihat data
            $data_event = Event::where('status', '=', 'active')->get();
        }
        $data_category = Category::all();

        return view('auth.user.assessment.ondesk', [
            "data_event" => $data_event,
            'data_category' => $data_category
        ]);
    }

    public function presentation()
    {
        if (auth()->user()->role == "Superadmin") {
            $data_event = Event::where('status', '=', 'active')->get();
        } elseif (auth()->user()->role == "Admin") {
            $data_event = Event::where('status', '=', 'active')
                ->where('company_code', auth()->user()->company_code)
                ->get();
        } elseif (auth()->user()->role == "Juri") {
            $judge_event_id = Judge::where('employee_id', auth()->user()->employee_id)
                ->where('status', 'active')
                ->pluck('event_id')
                ->toArray();

            $data_event = Event::whereIn('id', $judge_event_id)
                ->where('status', 'active')
                ->get();
        } elseif (auth()->user()->role == "BOD") {
            // Role BOD hanya dapat melihat data
            $data_event = Event::where('status', '=', 'active')->get();
        }
        $data_category = Category::all();

        return view('auth.user.assessment.presentation', [
            "data_event" => $data_event,
            'data_category' => $data_category
        ]);
    }
    public function showSofi_oda($id)
    {

        $dataTeam = Team::join('papers', 'papers.team_id', '=', 'teams.id')
            ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            // ->join('pvt_assesment_team_judges', 'pvt_assesment_team_judges.event_team_id', '=', 'pvt_event_teams.id')
            ->join('new_sofi', 'new_sofi.event_team_id', '=', 'pvt_event_teams.id')
            ->join('events', 'events.id', '=', 'pvt_event_teams.event_id')
            ->select('teams.id as team_id', 'pvt_event_teams.id as event_team_id', 'team_name', 'innovation_title', 'inovasi_lokasi', 'event_name', 'financial', 'potential_benefit', 'potensi_replikasi', 'recommend_category', 'strength', 'opportunity_for_improvement', 'suggestion_for_benefit')
            ->where('pvt_event_teams.id', $id)
            ->first();

        $dataNilai = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_assesment_team_judges.event_team_id', '=', 'pvt_event_teams.id')
            ->join('pvt_assessment_events', 'pvt_assessment_events.id', '=', 'pvt_assesment_team_judges.assessment_event_id')
            ->select('pvt_event_teams.id as id_event_team', 'pvt_assessment_events.pdca', 'pvt_assessment_events.id as id_point', 'point', DB::raw('ROUND(AVG(score),2) as average_score'))
            ->where('pvt_event_teams.id', $id)
            ->where('pvt_assessment_events.status_point', 'active')
            ->where('pvt_assesment_team_judges.stage', 'on desk')
            ->groupBy('pvt_event_teams.id', 'pvt_assessment_events.id')->orderByRaw("CASE
                        WHEN 'pdca' = 'Plan' THEN 1
                        WHEN 'pdca' = 'Do' THEN 2
                        WHEN 'pdca' = 'Check' THEN 3
                        WHEN 'pdca' = 'Action' THEN 4
                        ELSE 5
                    END, pvt_assessment_events.id ASC");;
        // dd($data_nilai);

        $individualResults = $dataNilai->get();
        $overallTotal = $individualResults->sum('average_score');

        // Combine the results and overall total
        $data = [
            'dataTeam' => $dataTeam,
            'individualResults' => $individualResults,
            'overallTotal' => $overallTotal,
        ];
        return view('auth.user.assessment.sofi_oda', compact('data'));
    }
    public function downloadSofi_oda($id)
    {
        $dataTeam = Team::join('papers', 'papers.team_id', '=', 'teams.id')
            ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            // ->join('pvt_assesment_team_judges', 'pvt_assesment_team_judges.event_team_id', '=', 'pvt_event_teams.id')
            ->join('new_sofi', 'new_sofi.event_team_id', '=', 'pvt_event_teams.id')
            ->join('events', 'events.id', '=', 'pvt_event_teams.event_id')
            ->select('team_name', 'innovation_title', 'inovasi_lokasi', 'event_name', 'financial', 'potential_benefit', 'potensi_replikasi', 'recommend_category', 'strength', 'opportunity_for_improvement', 'suggestion_for_benefit')
            ->where('pvt_event_teams.id', $id)
            ->first();

        $dataNilai = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_assesment_team_judges.event_team_id', '=', 'pvt_event_teams.id')
            ->join('pvt_assessment_events', 'pvt_assessment_events.id', '=', 'pvt_assesment_team_judges.assessment_event_id')
            ->select('pvt_event_teams.id as id_event_team', 'pvt_assessment_events.pdca', 'pvt_assessment_events.id as id_point', 'point', DB::raw('ROUND(AVG(score),2) as average_score'))
            ->where('pvt_event_teams.id', $id)
            ->where('pvt_assessment_events.status_point', 'active')
            ->where('pvt_assesment_team_judges.stage', 'on desk')
            ->groupBy('pvt_event_teams.id', 'pvt_assessment_events.id')->orderByRaw("CASE
                        WHEN 'pdca' = 'Plan' THEN 1
                        WHEN 'pdca' = 'Do' THEN 2
                        WHEN 'pdca' = 'Check' THEN 3
                        WHEN 'pdca' = 'Action' THEN 4
                        ELSE 5
                    END, pvt_assessment_events.id ASC");
        // dd($dataNilai);

        $individualResults = $dataNilai->get();
        $overallTotal = $individualResults->sum('average_score');
        // Combine the results and overall total

        $data = [
            'dataTeam' => $dataTeam,
            'individualResults' => $individualResults,
            'overallTotal' => $overallTotal,
        ];

        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);

        $html = view('auth.user.assessment.sofi_oda_pdf', compact('data'))->render();

        $mpdf->WriteHTML($html);

        $content = $mpdf->Output('', 'S');
        return response($content)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="sofi-oda.pdf"');
    }
    public function showSofi_pa($id)
    {

        $dataTeam = Team::join('papers', 'papers.team_id', '=', 'teams.id')
            ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            // ->join('pvt_assesment_team_judges', 'pvt_assesment_team_judges.event_team_id', '=', 'pvt_event_teams.id')
            ->join('new_sofi', 'new_sofi.event_team_id', '=', 'pvt_event_teams.id')
            ->join('events', 'events.id', '=', 'pvt_event_teams.event_id')
            ->select('pvt_event_teams.id as event_team_id', 'teams.id as team_id', 'team_name', 'innovation_title', 'inovasi_lokasi', 'event_name', 'financial', 'potential_benefit', 'potensi_replikasi', 'recommend_category', 'strength', 'opportunity_for_improvement', 'suggestion_for_benefit')
            ->where('pvt_event_teams.id', $id)
            ->first();

        $dataNilai = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_assesment_team_judges.event_team_id', '=', 'pvt_event_teams.id')
            ->join('pvt_assessment_events', 'pvt_assessment_events.id', '=', 'pvt_assesment_team_judges.assessment_event_id')
            ->select('pvt_event_teams.id as id_event_team', 'pvt_assessment_events.pdca', 'pvt_assessment_events.id as id_point', 'point', DB::raw('ROUND(AVG(score),2) as average_score'))
            ->where('pvt_event_teams.id', $id)
            ->where('pvt_assessment_events.status_point', 'active')
            ->where('pvt_assesment_team_judges.stage', 'presentation')
            ->groupBy('pvt_event_teams.id', 'pvt_assessment_events.id')
            ->orderByRaw("CASE
                        WHEN 'pdca' = 'Plan' THEN 1
                        WHEN 'pdca' = 'Do' THEN 2
                        WHEN 'pdca' = 'Check' THEN 3
                        WHEN 'pdca' = 'Action' THEN 4
                        ELSE 5
                    END, pvt_assessment_events.id ASC");


        $individualResults = $dataNilai->get();
        $overallTotal = $individualResults->sum('average_score');

        // Combine the results and overall total
        $data = [
            'dataTeam' => $dataTeam,
            'individualResults' => $individualResults,
            'overallTotal' => $overallTotal,
        ];
        return view('auth.user.assessment.sofi_pa', compact('data'));
    }
    public function showSofi_caucus($id)
    {
        // Mengambil data tim yang sama dengan fungsi presentasi
        $dataTeam = Team::join('papers', 'papers.team_id', '=', 'teams.id')
            ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            ->join('new_sofi', 'new_sofi.event_team_id', '=', 'pvt_event_teams.id')
            ->join('events', 'events.id', '=', 'pvt_event_teams.event_id')
            ->select( 'pvt_event_teams.id as event_team_id','teams.id as team_id','team_name','innovation_title','inovasi_lokasi','event_name','financial','potential_benefit','potensi_replikasi','recommend_category', 'strength', 'opportunity_for_improvement','suggestion_for_benefit')
            ->where('pvt_event_teams.id', $id)
            ->first();

        // Mengambil data penilaian untuk tahap Caucus dengan struktur yang sama dengan Presentasi
        $dataNilai = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_assesment_team_judges.event_team_id', '=', 'pvt_event_teams.id')
            ->join('pvt_assessment_events', 'pvt_assessment_events.id', '=', 'pvt_assesment_team_judges.assessment_event_id')
            ->select(
                'pvt_event_teams.id as id_event_team',
                'pvt_assessment_events.pdca',
                'pvt_assessment_events.id as id_point',
                'point',
                DB::raw('ROUND(AVG(score),2) as average_score')
            )
            ->where('pvt_event_teams.id', $id)
            ->where('pvt_assessment_events.status_point', 'active')
            ->where('pvt_assesment_team_judges.stage', 'caucus') // Mengambil data untuk stage caucus
            ->groupBy('pvt_event_teams.id', 'pvt_assessment_events.id')
            ->orderByRaw("CASE
                        WHEN 'pdca' = 'Plan' THEN 1
                        WHEN 'pdca' = 'Do' THEN 2
                        WHEN 'pdca' = 'Check' THEN 3
                        WHEN 'pdca' = 'Action' THEN 4
                        ELSE 5
                    END, pvt_assessment_events.id ASC");

        // Mendapatkan hasil nilai dari tahap Caucus
        $individualResults = $dataNilai->get();
        $overallTotal = $individualResults->sum('average_score');

        // Menggabungkan hasil dengan data tim seperti pada presentasi
        $data = [
            'dataTeam' => $dataTeam,
            'individualResults' => $individualResults, // Tetap menggunakan individualResults untuk konsistensi
            'overallTotal' => $overallTotal, // Tetap menggunakan overallTotal untuk konsistensi
        ];

        // Menampilkan view yang sama dengan tahap presentasi tetapi untuk caucus
        return view('auth.user.assessment.sofi_caucus', compact('data'));
    }

    public function downloadSofi_pa($id)
    {
        $dataTeam = Team::join('papers', 'papers.team_id', '=', 'teams.id')
            ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            // ->join('pvt_assesment_team_judges', 'pvt_assesment_team_judges.event_team_id', '=', 'pvt_event_teams.id')
            ->join('new_sofi', 'new_sofi.event_team_id', '=', 'pvt_event_teams.id')
            ->join('events', 'events.id', '=', 'pvt_event_teams.event_id')
            ->select('teams.id as team_id', 'team_name', 'innovation_title', 'inovasi_lokasi', 'event_name', 'financial', 'potential_benefit', 'potensi_replikasi', 'recommend_category', 'strength', 'opportunity_for_improvement', 'suggestion_for_benefit')
            ->where('pvt_event_teams.id', $id)
            ->first();

        $dataNilai = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_assesment_team_judges.event_team_id', '=', 'pvt_event_teams.id')
            ->join('pvt_assessment_events', 'pvt_assessment_events.id', '=', 'pvt_assesment_team_judges.assessment_event_id')
            ->select('pvt_event_teams.id as id_event_team', 'pvt_assessment_events.pdca', 'pvt_assessment_events.id as id_point', 'point', DB::raw('ROUND(AVG(score),2) as average_score'))
            ->where('pvt_event_teams.id', $id)
            ->where('pvt_assessment_events.status_point', 'active')
            ->where('pvt_assesment_team_judges.stage', 'caucus')
            ->groupBy('pvt_event_teams.id', 'pvt_assessment_events.id')
            ->orderByRaw("CASE
                        WHEN 'pdca' = 'Plan' THEN 1
                        WHEN 'pdca' = 'Do' THEN 2
                        WHEN 'pdca' = 'Check' THEN 3
                        WHEN 'pdca' = 'Action' THEN 4
                        ELSE 5
                    END, pvt_assessment_events.id ASC");

        $individualResults = $dataNilai->get();
        // dd($individualResults);
        $overallTotal = $individualResults->sum('average_score');

        // Combine the results and overall total
        $data = [
            'dataTeam' => $dataTeam,
            'individualResults' => $individualResults,
            'overallTotal' => $overallTotal,
        ];

        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);

        $html = view('auth.user.assessment.sofi_pa_pdf', compact('data'))->render();

        $mpdf->WriteHTML($html);

        $content = $mpdf->Output('', 'S');
        return response($content)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="sofi-pa.pdf"');
    }
    public function oda_fix(Request $request)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            if (isset($request->event_id)) {
                $teams_id = PvtEventTeam::where('event_id', $request->event_id)
                    ->pluck('id')
                    ->toArray();
                // dd($teams_id);
                $nilai_oda_bi = MinimumscoreEvent::where('category', 'BI/II')
                    ->where('event_id', $request->event_id)
                    ->pluck('score_minimum_oda')
                    ->toArray();

                $nilai_oda_idea = MinimumscoreEvent::where('category', 'IDEA')
                    ->where('event_id', $request->event_id)
                    ->pluck('score_minimum_oda')
                    ->toArray();

                $assessment_event_poin_bi = PvtAssessmentEvent::where('event_id', $request->event_id)
                    ->where('category', 'BI/II')
                    ->where('status_point', 'active')
                    ->where('stage', 'on desk')
                    ->limit(1)
                    ->pluck('id')
                    ->toArray();

                $assessment_event_poin_idea = PvtAssessmentEvent::where('event_id', $request->event_id)
                    ->where('category', 'IDEA')
                    ->where('status_point', 'active')
                    ->where('stage', 'on desk')
                    ->limit(1)
                    ->pluck('id')
                    ->toArray();
                // dd($assessment_event_poin);
                if (isset($nilai_oda_bi[0]) && isset($assessment_event_poin_bi[0])) {
                    $nilai_oda_bi = $nilai_oda_bi[0];
                    $assessment_event_poin_bi = $assessment_event_poin_bi[0];

                    PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                        ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                        ->join('categories', 'teams.category_id', '=', 'categories.id')
                        ->whereNot('categories.category_parent', '=', 'IDEA BOX')
                        ->where('pvt_event_teams.status', '=', 'On Desk')
                        ->whereIn('pvt_event_teams.id', $teams_id)
                        ->where('pvt_assesment_team_judges.stage', 'on desk')
                        ->groupBy('pvt_event_teams.id')
                        ->havingRaw('ROUND(ROUND(SUM(pvt_assesment_team_judges.score), 2) / COUNT(CASE WHEN pvt_assesment_team_judges.assessment_event_id = ? THEN pvt_assesment_team_judges.assessment_event_id END), 2) >= ?', [$assessment_event_poin_bi, $nilai_oda_bi])
                        ->update([
                            'pvt_event_teams.status' => 'Presentation'
                        ]);

                    PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                        ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                        ->join('categories', 'teams.category_id', '=', 'categories.id')
                        ->whereNot('categories.category_parent', '=', 'IDEA BOX')
                        ->where('pvt_event_teams.status', '=', 'On Desk')
                        ->whereIn('pvt_event_teams.id', $teams_id)
                        ->where('pvt_assesment_team_judges.stage', 'on desk')
                        ->groupBy('pvt_event_teams.id')
                        ->havingRaw('ROUND(ROUND(SUM(pvt_assesment_team_judges.score), 2) / COUNT(CASE WHEN pvt_assesment_team_judges.assessment_event_id = ? THEN pvt_assesment_team_judges.assessment_event_id END), 2) < ?', [$assessment_event_poin_bi, $nilai_oda_bi])
                        ->update([
                            'pvt_event_teams.status' => 'tidak lolos On Desk'
                        ]);
                }

                if (isset($nilai_oda_idea[0]) && isset($assessment_event_poin_idea[0])) {
                    $nilai_oda_idea = $nilai_oda_idea[0];
                    $assessment_event_poin_idea = $assessment_event_poin_idea[0];

                    PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                        ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                        ->join('categories', 'teams.category_id', '=', 'categories.id')
                        ->where('categories.category_parent', '=', 'IDEA BOX')
                        ->where('pvt_event_teams.status', '=', 'On Desk')
                        ->whereIn('pvt_event_teams.id', $teams_id)
                        ->where('pvt_assesment_team_judges.stage', 'on desk')
                        ->groupBy('pvt_event_teams.id')
                        ->havingRaw('ROUND(ROUND(SUM(pvt_assesment_team_judges.score), 2) / COUNT(CASE WHEN pvt_assesment_team_judges.assessment_event_id = ? THEN pvt_assesment_team_judges.assessment_event_id END), 2) >= ?', [$assessment_event_poin_idea, $nilai_oda_idea])
                        ->update([
                            'pvt_event_teams.status' => 'Presentation'
                        ]);

                    PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                        ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                        ->join('categories', 'teams.category_id', '=', 'categories.id')
                        ->where('categories.category_parent', '=', 'IDEA BOX')
                        ->where('pvt_event_teams.status', '=', 'On Desk')
                        ->whereIn('pvt_event_teams.id', $teams_id)
                        ->where('pvt_assesment_team_judges.stage', 'on desk')
                        ->groupBy('pvt_event_teams.id')
                        ->havingRaw('ROUND(ROUND(SUM(pvt_assesment_team_judges.score), 2) / COUNT(CASE WHEN pvt_assesment_team_judges.assessment_event_id = ? THEN pvt_assesment_team_judges.assessment_event_id END), 2) < ?', [$assessment_event_poin_idea, $nilai_oda_idea])
                        ->update([
                            'pvt_event_teams.status' => 'tidak lolos On Desk'
                        ]);
                }

                foreach ($teams_id as $team_id) {
                    $team_status = PvtEventTeam::where('id', $team_id)
                        ->pluck('status')
                        ->toArray();

                    if ($team_status[0] == 'Presentation') {
                        $data_assessment_team_judge = pvtAssesmentTeamJudge::where('event_team_id', $team_id)
                            ->where('stage', 'on desk')
                            ->select('assessment_event_id', 'judge_id')
                            ->get();
                        $set_judge = [];
                        foreach ($data_assessment_team_judge as $assessment_team_judge) {
                            pvtAssesmentTeamJudge::updateOrCreate([
                                'judge_id'  => $assessment_team_judge->judge_id,
                                'event_team_id' => $team_id,
                                'assessment_event_id'   => $assessment_team_judge->assessment_event_id,
                                'stage'     => 'presentation'
                            ]);

                            // dd($tes);

                            $set_judge[$assessment_team_judge->judge_id] = true;
                        }

                        $cat_assessment_point = PvtEventTeam::join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                            ->join('categories', 'categories.id', '=', 'teams.category_id')
                            ->pluck('category_parent') == "IDEA BOX" ? "IDEA" : "BI/II";

                        if ($cat_assessment_point == 'IDEA') {
                            $data_assessment_idea_presentation = PvtAssessmentEvent::where('event_id', $request->event_id)
                                ->where('category', 'IDEA')
                                ->where('status_point', 'active')
                                ->where('stage', 'presentation')
                                ->get();

                            foreach ($data_assessment_idea_presentation as $assessment_idea_presentation) {
                                foreach ($set_judge as $judge => $isi) {
                                    pvtAssesmentTeamJudge::updateOrCreate([
                                        'judge_id'  => $judge,
                                        'event_team_id' => $team_id,
                                        'assessment_event_id'   => $assessment_idea_presentation->id,
                                        'stage'     => 'presentation'
                                    ]);
                                }
                            }
                        } else {
                            $data_assessment_idea_presentation = PvtAssessmentEvent::where('event_id', $request->event_id)
                                ->where('category', 'BI/II')
                                ->where('status_point', 'active')
                                ->where('stage', 'presentation')
                                ->get();

                            foreach ($data_assessment_idea_presentation as $assessment_idea_presentation) {
                                foreach ($set_judge as $judge => $isi) {
                                    pvtAssesmentTeamJudge::updateOrCreate([
                                        'judge_id'  => $judge,
                                        'event_team_id' => $team_id,
                                        'assessment_event_id'   => $assessment_idea_presentation->id,
                                        'stage'     => 'presentation'
                                    ]);
                                }
                            }
                        }
                    }
                }
                DB::commit();
                return redirect()->back()->with('success', 'update successfully');
            } elseif (isset($request->pvt_event_team_id)) {
                $category = Category::where('id', $request->category)->pluck('category_parent') == "IDEA BOX" ? "IDEA" : "BI/II";
                $event_id = PvtEventTeam::where('id', $request->pvt_event_team_id[0])->pluck('event_id');
                $score_oda = (int)MinimumscoreEvent::where('event_id', $event_id)
                    ->where('category', $category)
                    ->pluck('score_minimum_oda')
                    ->toArray()[0];

                foreach ($request->pvt_event_team_id as $index => $event_team_id) {
                    $event_team = PvtEventTeam::findOrFail($event_team_id);

                    $total_score = intval($request->total_score_event[$index]);
                    if ($total_score >= $score_oda) {
                        $event_team->status = 'Presentation';
                    } else {
                        $event_team->status = 'tidak lolos On Desk';
                    }

                    $event_team->save();

                    $team_status = PvtEventTeam::where('id', $event_team_id)
                        ->pluck('status')
                        ->toArray();

                    if ($team_status[0] == 'Presentation') {
                        $data_assessment_team_judge = pvtAssesmentTeamJudge::where('event_team_id', $event_team_id)
                            ->where('stage', 'on desk')
                            ->select('assessment_event_id', 'judge_id')
                            ->get();
                        $event_id = pvtEventTeam::where('id', $event_team_id)
                            ->pluck('event_id')
                            ->toArray();

                        $set_judge = [];
                        foreach ($data_assessment_team_judge as $assessment_team_judge) {
                            pvtAssesmentTeamJudge::updateOrCreate([
                                'judge_id'  => $assessment_team_judge->judge_id,
                                'event_team_id' => $event_team_id,
                                'assessment_event_id'   => $assessment_team_judge->assessment_event_id,
                                'stage'     => 'presentation'
                            ]);


                            $set_judge[$assessment_team_judge->judge_id] = true;
                        }

                        $cat_assessment_point = PvtEventTeam::join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                            ->join('categories', 'categories.id', '=', 'teams.category_id')
                            ->pluck('category_parent') == "IDEA BOX" ? "IDEA" : "BI/II";

                        if ($cat_assessment_point == 'IDEA') {
                            $data_assessment_idea_presentation = PvtAssessmentEvent::where('event_id', $event_id[0])
                                ->where('category', 'IDEA')
                                ->where('status_point', 'active')
                                ->where('stage', 'presentation')
                                ->get();

                            foreach ($data_assessment_idea_presentation as $assessment_idea_presentation) {
                                foreach ($set_judge as $judge => $isi) {
                                    pvtAssesmentTeamJudge::updateOrCreate([
                                        'judge_id'  => $judge,
                                        'event_team_id' => $event_team_id,
                                        'assessment_event_id'   => $assessment_idea_presentation->id,
                                        'stage'     => 'presentation'
                                    ]);
                                }
                            }
                        } else {
                            $data_assessment_idea_presentation = PvtAssessmentEvent::where('event_id', $event_id[0])
                                ->where('category', 'BI/II')
                                ->where('status_point', 'active')
                                ->where('stage', 'presentation')
                                ->get();

                            foreach ($data_assessment_idea_presentation as $assessment_idea_presentation) {
                                foreach ($set_judge as $judge => $isi) {
                                    pvtAssesmentTeamJudge::updateOrCreate([
                                        'judge_id'  => $judge,
                                        'event_team_id' => $event_team_id,
                                        'assessment_event_id'   => $assessment_idea_presentation->id,
                                        'stage'     => 'presentation'
                                    ]);
                                }
                            }
                        }
                    }
                }
                DB::commit();
                return redirect()->back()->with('success', 'update successfully');
            } else {
                DB::rollback();
                return redirect()->back()->withErrors('Error: tidak ada tim yang dipilih');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors('Error: ' . $e->getMessage());
        }
    }

    public function pa_fix(Request $request)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            if (isset($request->event_id)) {
                $teams_id = PvtEventTeam::where('event_id', $request->event_id)
                    ->pluck('id')
                    ->toArray();
                // dd($teams_id);
                $nilai_pa_bi = MinimumscoreEvent::where('category', 'BI/II')
                    ->where('event_id', $request->event_id)
                    ->pluck('score_minimum_pa')
                    ->toArray();

                $nilai_pa_idea = MinimumscoreEvent::where('category', 'IDEA')
                    ->where('event_id', $request->event_id)
                    ->pluck('score_minimum_pa')
                    ->toArray();

                $assessment_event_poin_bi = PvtAssessmentEvent::where('event_id', $request->event_id)
                    ->where('category', 'BI/II')
                    ->where('status_point', 'active')
                    ->where('stage', 'presentation')
                    ->limit(1)
                    ->pluck('id')
                    ->toArray();

                $assessment_event_poin_idea = PvtAssessmentEvent::where('event_id', $request->event_id)
                    ->where('category', 'IDEA')
                    ->where('status_point', 'active')
                    ->where('stage', 'presentation')
                    ->limit(1)
                    ->pluck('id')
                    ->toArray();
                // dd($nilai_pa_bi);
                if (isset($nilai_pa_bi[0]) && isset($assessment_event_poin_bi[0])) {
                    $nilai_pa_bi = $nilai_pa_bi[0];
                    $assessment_event_poin_bi = $assessment_event_poin_bi[0];

                    $categoryID_list = Category::whereNot('category_parent', 'IDEA BOX')->pluck('id')->toArray();
                    // dd($category_list);

                    PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                        ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                        ->join('categories', 'teams.category_id', '=', 'categories.id')
                        ->whereNot('categories.category_parent', '=', 'IDEA BOX')
                        ->where('pvt_event_teams.status', '=', 'Presentation')
                        ->whereIn('pvt_event_teams.id', $teams_id)
                        ->where('pvt_assesment_team_judges.stage', 'presentation')
                        ->groupBy('pvt_event_teams.id')
                        // ->havingRaw('ROUND(ROUND(SUM(pvt_assesment_team_judges.score), 2) / COUNT(CASE WHEN pvt_assesment_team_judges.assessment_event_id = ? THEN pvt_assesment_team_judges.assessment_event_id END), 2) < ?', [$assessment_event_poin_bi, $nilai_oda_bi])
                        ->update([
                            'pvt_event_teams.status' => 'tidak lolos Presentation'
                        ]);

                    foreach ($categoryID_list as $categoryID) {
                        PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                            ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                            ->join('categories', 'teams.category_id', '=', 'categories.id')
                            ->whereNot('categories.category_parent', '=', 'IDEA BOX')
                            ->whereIn('pvt_event_teams.id', $teams_id)
                            ->where('teams.category_id', '=', $categoryID)
                            ->where('pvt_assesment_team_judges.stage', 'presentation')
                            ->where('pvt_event_teams.status', '=', 'tidak lolos Presentation')
                            ->groupBy('pvt_event_teams.id')
                            ->orderByRaw('ROUND(ROUND(SUM(pvt_assesment_team_judges.score), 2) / COUNT(CASE WHEN pvt_assesment_team_judges.assessment_event_id = ? THEN pvt_assesment_team_judges.assessment_event_id END), 2) DESC', [$assessment_event_poin_bi])
                            ->havingRaw('ROUND(ROUND(SUM(pvt_assesment_team_judges.score), 2) / COUNT(CASE WHEN pvt_assesment_team_judges.assessment_event_id = ? THEN pvt_assesment_team_judges.assessment_event_id END), 2) >= ?', [$assessment_event_poin_bi, $nilai_pa_bi])
                            // ->take($request->total_team)
                            ->update([
                                'pvt_event_teams.status' => 'Caucus'
                            ]);
                    }
                }

                if (isset($nilai_pa_idea[0]) && isset($assessment_event_poin_idea[0])) {
                    $nilai_pa_idea = $nilai_pa_idea[0];
                    $assessment_event_poin_idea = $assessment_event_poin_idea[0];

                    PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                        ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                        ->join('categories', 'teams.category_id', '=', 'categories.id')
                        ->where('categories.category_parent', '=', 'IDEA BOX')
                        ->where('pvt_event_teams.status', '=', 'Presentation')
                        ->where('pvt_assesment_team_judges.stage', 'presentation')
                        ->whereIn('pvt_event_teams.id', $teams_id)
                        ->groupBy('pvt_event_teams.id')
                        // ->havingRaw('ROUND(ROUND(SUM(pvt_assesment_team_judges.score), 2) / COUNT(CASE WHEN pvt_assesment_team_judges.assessment_event_id = ? THEN pvt_assesment_team_judges.assessment_event_id END), 2) <= ?', [$assessment_event_poin_idea, $nilai_pa_idea])
                        ->update([
                            'pvt_event_teams.status' => 'tidak lolos Presentation'
                        ]);

                    PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                        ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                        ->join('categories', 'teams.category_id', '=', 'categories.id')
                        ->where('categories.category_parent', '=', 'IDEA BOX')
                        ->whereIn('pvt_event_teams.id', $teams_id)
                        ->where('pvt_event_teams.status', '=', 'tidak lolos Presentation')
                        ->where('pvt_assesment_team_judges.stage', 'presentation')
                        ->groupBy('pvt_event_teams.id')
                        ->orderByRaw('ROUND(ROUND(SUM(pvt_assesment_team_judges.score), 2) / COUNT(CASE WHEN pvt_assesment_team_judges.assessment_event_id = ? THEN pvt_assesment_team_judges.assessment_event_id END), 2) DESC', [$assessment_event_poin_idea])
                        ->havingRaw('ROUND(ROUND(SUM(pvt_assesment_team_judges.score), 2) / COUNT(CASE WHEN pvt_assesment_team_judges.assessment_event_id = ? THEN pvt_assesment_team_judges.assessment_event_id END), 2) >= ?', [$assessment_event_poin_idea, $nilai_pa_idea])
                        // ->take($request->total_team)
                        ->update([
                            'pvt_event_teams.status' => 'Caucus'
                        ]);
                }

                $idEventTeams = PvtEventTeam::where('event_id', $request->event_id)
                    ->where('status', 'Caucus')
                    ->pluck('id')
                    ->toArray();

                DB::commit();
                return redirect()->back()->with('success', 'update successfully');
            } elseif (isset($request->pvt_event_team_id)) {
                $category = Category::where('id', $request->category)->pluck('category_parent') == "IDEA BOX" ? "IDEA" : "BI/II";
                $event_id = PvtEventTeam::where('id', $request->pvt_event_team_id[0])->pluck('event_id');
                $score_oda = (int)MinimumscoreEvent::where('event_id', $event_id[0])
                    ->where('category', $category)
                    ->pluck('score_minimum_pa')
                    ->toArray()[0];

                foreach ($request->pvt_event_team_id as $index => $event_team_id) {
                    $event_team = PvtEventTeam::findOrFail($event_team_id);

                    $total_score = intval($request->total_score_event[$index]);
                    if ($total_score >= $score_oda) {
                        $event_team->status = 'Caucus';
                    } else {
                        $event_team->status = 'tidak lolos Presentation';
                    }

                    $event_team->save();
                    $team_status = PvtEventTeam::where('id', $event_team_id)
                        ->pluck('status')
                        ->toArray();

                    if ($team_status[0] == 'Caucus') {
                        $data_assessment_team_judge = pvtAssesmentTeamJudge::where('event_team_id', $event_team_id)
                            ->where('stage', 'presentation')
                            ->select('assessment_event_id', 'judge_id')
                            ->get();
                        $event_id = pvtEventTeam::where('id', $event_team_id)
                            ->pluck('event_id')
                            ->toArray();

                        $set_judge = [];
                        foreach ($data_assessment_team_judge as $assessment_team_judge) {
                            pvtAssesmentTeamJudge::updateOrCreate([
                                'judge_id'  => $assessment_team_judge->judge_id,
                                'event_team_id' => $event_team_id,
                                'assessment_event_id'   => $assessment_team_judge->assessment_event_id,
                                'stage'     => 'caucus'
                            ]);


                            $set_judge[$assessment_team_judge->judge_id] = true;
                        }

                        $cat_assessment_point = PvtEventTeam::join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                            ->join('categories', 'categories.id', '=', 'teams.category_id')
                            ->pluck('category_parent') == "IDEA BOX" ? "IDEA" : "BI/II";

                        if ($cat_assessment_point == 'IDEA') {
                            $data_assessment_idea_presentation = PvtAssessmentEvent::where('event_id', $event_id[0])
                                ->where('category', 'IDEA')
                                ->where('status_point', 'active')
                                ->where('stage', 'caucus')
                                ->get();

                            foreach ($data_assessment_idea_presentation as $assessment_idea_presentation) {
                                foreach ($set_judge as $judge => $isi) {
                                    pvtAssesmentTeamJudge::updateOrCreate([
                                        'judge_id'  => $judge,
                                        'event_team_id' => $event_team_id,
                                        'assessment_event_id'   => $assessment_idea_presentation->id,
                                        'stage'     => 'caucus'
                                    ]);
                                }
                            }
                        } else {
                            $data_assessment_idea_presentation = PvtAssessmentEvent::where('event_id', $event_id[0])
                                ->where('category', 'BI/II')
                                ->where('status_point', 'active')
                                ->where('stage', 'caucus')
                                ->get();

                            foreach ($data_assessment_idea_presentation as $assessment_idea_presentation) {
                                foreach ($set_judge as $judge => $isi) {
                                    pvtAssesmentTeamJudge::updateOrCreate([
                                        'judge_id'  => $judge,
                                        'event_team_id' => $event_team_id,
                                        'assessment_event_id'   => $assessment_idea_presentation->id,
                                        'stage'     => 'caucus'
                                    ]);
                                }
                            }
                        }
                    }
                }
                DB::commit();
                return redirect()->back()->with('success', 'update successfully');
            } else {
                DB::rollback();
                return redirect()->back()->withErrors('Error: tidak ada tim yang dipilih');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors('Error: ' . $e->getMessage());
        }
    }

    public function eligible_fix(Request $request)
    {
        // dd($request->all());
        try {
            if (isset($request->pvt_event_team_id)) {
                PvtEventTeam::WhereIn('id', $request->pvt_event_team_id)
                    ->update([
                        'status' => 'Caucus'
                    ]);

                foreach ($request->pvt_event_team_id as $event_team_id) {
                    BodEventValue::updateOrCreate([
                        'event_team_id' => $event_team_id
                    ], [
                        'value'         => 0
                    ]);
                }

                return redirect()->back()->with('success', 'update successfully');
            } else {
                return redirect()->back()->withErrors('Error: tidak ada tim yang dipilih');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error: ' . $e->getMessage());
        }
    }

    public function to_caucus_fix(Request $request)
    {
        // dd($request->all());
        try {
            if (isset($request->pvt_event_team_id)) {
                PvtEventTeam::WhereIn('id', $request->pvt_event_team_id)
                    ->update([
                        'status' => 'Finish'
                    ]);
                return redirect()->back()->with('success', 'update successfully');
            } else {
                return redirect()->back()->withErrors('Error: tidak ada tim yang dipilih');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error: ' . $e->getMessage());
        }
    }
    public function caucus()
    {
        $currentYear = Carbon::now()->year;
        $data_event = Event::where('status', 'active')->get();
        $data_category = Category::all();
        return view('auth.user.assessment.caucus', [
            "data_event" => $data_event,
            'data_category' => $data_category,
        ]);
    }
    public function summaryExecutive(Request $request)
    {
        // dd($request->all());
        try {
            //code...
            DB::beginTransaction();
            SummaryExecutive::updateOrCreate(
                [
                    'pvt_event_teams_id' => $request->input('pvt_event_teams_id'),
                ],
                [
                    'pvt_event_teams_id' => $request->input('pvt_event_teams_id'),
                    'problem_background' => $request->input('problem_background'),
                    'innovation_idea' => $request->input('innovation_idea'),
                    'benefit' => $request->input('benefit')
                ]
            );

            // $pvt_event_id = $request->input('pvt_event_teams_id');

            // $updateStatus = PvtEventTeam::findOrFail($pvt_event_id);
            // $updateStatus->status = 'Presentation BOD';
            // $updateStatus->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('assessment.caucus.data')->withErrors('Error: ' . $e->getMessage());
        }
        return redirect()->route('assessment.caucus.data')->with('success', 'Data Berhasil disimpan');
    }
    public function summaryPPT(Request $request)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();

            //update PPT
            $idSummary = $request->input('id');
            $filePpt = $request->file('file_ppt');
            $file = $filePpt->storeAs(
                'summary_executive/file_ppt',
                Str::slug(pathinfo($filePpt->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $filePpt->getClientOriginalExtension(),
                'public'
            );

            $upPPT = SummaryExecutive::findOrFail($idSummary);
            $upPPT->file_ppt = $file;
            $upPPT->save();


            //update status pvt_event_team
            $pvt_event_id = $request->input('pvt_event_teams_id');
            $updateStatus = PvtEventTeam::findOrFail($pvt_event_id);
            $updateStatus->status = 'Juara';
            $updateStatus->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('assessment.presentasiBOD')->withErrors('Error: ' . $e->getMessage());
        }
        return redirect()->route('assessment.presentasiBOD')->with('success', 'PDF berhasil di update');
    }

    public function addBODvalue(Request $request)
    {
        // dd($request->all());
        // try {
        //     if (isset($request->bod_value)) {
        //         foreach ($request->bod_value as $event_team_id => $bodvalue) {
        //             BodEventValue::where("event_team_id", $event_team_id)
        //                 ->update([
        //                     'value' => $bodvalue
        //                 ]);

        //             Log::debug($event_team_id);
        //             // $pvt_event_id = $request->input('pvt_event_teams_id');

        //             // $updateStatus = PvtEventTeam::findOrFail($pvt_event_id);
        //             // $updateStatus->status = 'Presentation BOD';
        //             // $updateStatus->save();
        //         }
        //     }
        //     return redirect()->back()->with('success', 'update successfully');
        // } catch (\Exception $e) {
        //     return redirect()->back()->withErrors('Error: ' . $e->getMessage());
        // }

        try {
            DB::beginTransaction();
            if (isset($request->event_id)) {
                $teams_id = PvtEventTeam::where('event_id', $request->event_id)
                    ->pluck('id')
                    ->toArray();
                // dd($teams_id);
                $nilai_pa_bi = MinimumscoreEvent::where('category', 'BI/II')
                    ->where('event_id', $request->event_id)
                    ->pluck('score_minimum_pa')
                    ->toArray();

                $nilai_pa_idea = MinimumscoreEvent::where('category', 'IDEA')
                    ->where('event_id', $request->event_id)
                    ->pluck('score_minimum_pa')
                    ->toArray();

                $assessment_event_poin_bi = PvtAssessmentEvent::where('event_id', $request->event_id)
                    ->where('category', 'BI/II')
                    ->where('status_point', 'active')
                    ->where('stage', 'presentation')
                    ->limit(1)
                    ->pluck('id')
                    ->toArray();

                $assessment_event_poin_idea = PvtAssessmentEvent::where('event_id', $request->event_id)
                    ->where('category', 'IDEA')
                    ->where('status_point', 'active')
                    ->where('stage', 'presentation')
                    ->limit(1)
                    ->pluck('id')
                    ->toArray();
                // dd($nilai_pa_bi);
                if (isset($nilai_pa_bi[0]) && isset($assessment_event_poin_bi[0])) {
                    $nilai_pa_bi = $nilai_pa_bi[0];
                    $assessment_event_poin_bi = $assessment_event_poin_bi[0];

                    $categoryID_list = Category::whereNot('category_parent', 'IDEA BOX')->pluck('id')->toArray();
                    // dd($category_list);

                    PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                        ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                        ->join('categories', 'teams.category_id', '=', 'categories.id')
                        ->whereNot('categories.category_parent', '=', 'IDEA BOX')
                        ->where('pvt_event_teams.status', '=', 'Caucus')
                        ->whereIn('pvt_event_teams.id', $teams_id)
                        ->where('pvt_assesment_team_judges.stage', 'caucus')
                        ->groupBy('pvt_event_teams.id')
                        // ->havingRaw('ROUND(ROUND(SUM(pvt_assesment_team_judges.score), 2) / COUNT(CASE WHEN pvt_assesment_team_judges.assessment_event_id = ? THEN pvt_assesment_team_judges.assessment_event_id END), 2) < ?', [$assessment_event_poin_bi, $nilai_oda_bi])
                        ->update([
                            'pvt_event_teams.status' => 'tidak lolos Caucus'
                        ]);

                    foreach ($categoryID_list as $categoryID) {
                        PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                            ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                            ->join('categories', 'teams.category_id', '=', 'categories.id')
                            ->whereNot('categories.category_parent', '=', 'IDEA BOX')
                            ->whereIn('pvt_event_teams.id', $teams_id)
                            ->where('teams.category_id', '=', $categoryID)
                            ->where('pvt_assesment_team_judges.stage', 'caucus')
                            ->where('pvt_event_teams.status', '=', 'tidak lolos Caucus')
                            ->groupBy('pvt_event_teams.id')
                            ->orderByRaw('ROUND(ROUND(SUM(pvt_assesment_team_judges.score), 2) / COUNT(CASE WHEN pvt_assesment_team_judges.assessment_event_id = ? THEN pvt_assesment_team_judges.assessment_event_id END), 2) DESC', [$assessment_event_poin_bi])
                            ->havingRaw('ROUND(ROUND(SUM(pvt_assesment_team_judges.score), 2) / COUNT(CASE WHEN pvt_assesment_team_judges.assessment_event_id = ? THEN pvt_assesment_team_judges.assessment_event_id END), 2) >= ?', [$assessment_event_poin_bi, $nilai_pa_bi])
                            // ->take($request->total_team)
                            ->update([
                                'pvt_event_teams.status' => 'Presentation BOD'
                            ]);
                    }
                }

                if (isset($nilai_pa_idea[0]) && isset($assessment_event_poin_idea[0])) {
                    $nilai_pa_idea = $nilai_pa_idea[0];
                    $assessment_event_poin_idea = $assessment_event_poin_idea[0];

                    PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                        ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                        ->join('categories', 'teams.category_id', '=', 'categories.id')
                        ->where('categories.category_parent', '=', 'IDEA BOX')
                        ->where('pvt_event_teams.status', '=', 'Caucus')
                        ->where('pvt_assesment_team_judges.stage', 'caucus')
                        ->whereIn('pvt_event_teams.id', $teams_id)
                        ->groupBy('pvt_event_teams.id')
                        // ->havingRaw('ROUND(ROUND(SUM(pvt_assesment_team_judges.score), 2) / COUNT(CASE WHEN pvt_assesment_team_judges.assessment_event_id = ? THEN pvt_assesment_team_judges.assessment_event_id END), 2) <= ?', [$assessment_event_poin_idea, $nilai_pa_idea])
                        ->update([
                            'pvt_event_teams.status' => 'tidak lolos Caucus'
                        ]);

                    PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                        ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                        ->join('categories', 'teams.category_id', '=', 'categories.id')
                        ->where('categories.category_parent', '=', 'IDEA BOX')
                        ->whereIn('pvt_event_teams.id', $teams_id)
                        ->where('pvt_event_teams.status', '=', 'tidak lolos Caucus')
                        ->where('pvt_assesment_team_judges.stage', 'caucus')
                        ->groupBy('pvt_event_teams.id')
                        ->orderByRaw('ROUND(ROUND(SUM(pvt_assesment_team_judges.score), 2) / COUNT(CASE WHEN pvt_assesment_team_judges.assessment_event_id = ? THEN pvt_assesment_team_judges.assessment_event_id END), 2) DESC', [$assessment_event_poin_idea])
                        ->havingRaw('ROUND(ROUND(SUM(pvt_assesment_team_judges.score), 2) / COUNT(CASE WHEN pvt_assesment_team_judges.assessment_event_id = ? THEN pvt_assesment_team_judges.assessment_event_id END), 2) >= ?', [$assessment_event_poin_idea, $nilai_pa_idea])
                        // ->take($request->total_team)
                        ->update([
                            'pvt_event_teams.status' => 'Presentation BOD'
                        ]);
                }

                $idEventTeams = PvtEventTeam::where('event_id', $request->event_id)
                    ->where('status', 'Caucus')
                    ->pluck('id')
                    ->toArray();

                DB::commit();
                return redirect()->back()->with('success', 'update successfully');
            } elseif (isset($request->pvt_event_team_id)) {
                $category = Category::where('id', $request->category)->pluck('category_parent') == "IDEA BOX" ? "IDEA" : "BI/II";
                $event_id = PvtEventTeam::where('id', $request->pvt_event_team_id[0])->pluck('event_id');
                $score_oda = (int)MinimumscoreEvent::where('event_id', $event_id[0])
                    ->where('category', $category)
                    ->pluck('score_minimum_pa')
                    ->toArray()[0];

                foreach ($request->pvt_event_team_id as $index => $event_team_id) {
                    $event_team = PvtEventTeam::findOrFail($event_team_id);

                    $total_score = intval($request->total_score_event[$index]);
                    if ($total_score >= $score_oda) {
                        $event_team->status = 'Presentation BOD';
                    } else {
                        $event_team->status = 'tidak lolos Caucus';
                    }

                    $event_team->save();
                }
                DB::commit();
                return redirect()->back()->with('success', 'update successfully');
            } else {
                DB::rollback();
                return redirect()->back()->withErrors('Error: tidak ada tim yang dipilih');
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::debug($e);
            return redirect()->back()->withErrors('Error: ' . $e->getMessage());
        }
    }
    public function presentasiBOD(Request $request)
    {
        $currentYear = Carbon::now()->year;
        $data_event = Event::where('status', 'active')->get();
        $data_category = Category::all();
        return view('auth.user.assessment.presentasi_bod', [
            "data_event" => $data_event,
            'data_category' => $data_category,
        ]);
    }
    public function penetapanJuara(Request $request)
    {
        $currentYear = Carbon::now()->year;
        $data_event = Event::where('status', 'active')->get();
        $data_category = Category::all();
        $data = BeritaAcara::join('events', 'berita_acaras.event_id', 'events.id')
            ->select('berita_acaras.*', 'events.id as eventID', 'events.event_name', 'events.event_name', 'events.year', 'events.date_start', 'events.date_end')
            ->get();
        return view('auth.user.assessment.penetapan_juara', [
            "data_event" => $data_event,
            'data_category' => $data_category,
            'data' => $data
        ]);
    }

    public function keputusanBOD(Request $request)
    {
        // dd($request->all());
        $data = $request->only(['pvt_event_teams_id', 'val_peringkat']);
        DB::beginTransaction();
        try {
            foreach ($data['pvt_event_teams_id'] as $index => $eventTeamId) {
                KeputusanBod::updateOrCreate([
                    'pvt_event_teams_id' => $eventTeamId,
                    'val_peringkat' => $data['val_peringkat'][$index],
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // dd($e->getMessage());
            return redirect()->route('assessment.presentasiBOD')->withErrors('Error: ' . $e->getMessage());
        }
        return redirect()->route('assessment.presentasiBOD')->with('success', 'Data has been recorded'); // masih belom tau
    }
    public function pdfSummary($team_id)
    {
        try {
            // Temukan data summary berdasarkan ID
            $summary = SummaryExecutive::findOrFail($team_id);

            // Ambil path file PDF dari data summary
            $pdfPath = $summary->file_ppt;

            // Cek apakah file ada di penyimpanan
            if (Storage::disk('public')->exists($pdfPath)) {
                // Ambil konten file PDF
                $pdfContent = Storage::disk('public')->get($pdfPath);

                // Tampilkan file PDF
                return response($pdfContent)
                    ->header('Content-Type', 'application/pdf');
            } else {
                return redirect()->route('assessment.presentasiBOD')->withErrors('Error: File PDF tidak ditemukan');
            }
        } catch (\Exception $e) {
            return redirect()->route('assessment.presentasiBOD')->withErrors('Error: ' . $e->getMessage());
        }
    }
}
