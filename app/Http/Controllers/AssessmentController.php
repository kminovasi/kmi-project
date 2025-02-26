<?php

namespace App\Http\Controllers;

use Route;
use Exception;
use Mpdf\Mpdf;
use Carbon\Carbon;
use App\Models\Team;
use App\Models\Event;
use App\Models\Judge;
use App\Models\Paper;
use App\Models\Company;
use App\Models\NewSofi;
use App\Models\BodEvent;
use App\Models\Category;
use App\Models\SummaryPPT;
use App\Models\BeritaAcara;
use Illuminate\Support\Str;
use App\Models\keputusanBOD;
use App\Models\PvtEventTeam;
use Illuminate\Http\Request;
use App\Models\BodEventValue;
use setasign\Fpdi\Tcpdf\Fpdi;
use App\Services\JudgeService;
use App\Models\AssessmentPoint;
use App\Models\SummaryExecutive;
use App\Models\MinimumscoreEvent;
use App\Models\PvtAssessmentEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\pvtAssesmentTeamJudge;
use App\Models\TemplateAssessmentPoint;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\assessmentPointRequests;
use App\Http\Requests\assessmentTemplateRequests;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class AssessmentController extends Controller
{
    //
    // public function index(){
    //     return view('auth.juri.assessment');
    // }
    public function showTemplate()
    {
        $checkStatus = Auth::user()->role;
        $userCompanyCode = Auth::user()->company_code;

        if ($checkStatus == 'Superadmin') {
            $events = Event::where('status', '!=', 'finish')->get();
        } else {
            $events = Event::whereHas('companies', function ($query) use ($userCompanyCode) {
                $query->where('company_code', $userCompanyCode);
            })->where('status', '!=', 'finish')
                ->where('type', 'AP')
                ->get();
        }

        return view('auth.admin.assessment.template.index', compact('events'));
    }
    public function createTemplate()
    {
        // $rows = TemplateAssessmentPoint::get();
        return view('auth.admin.assessment.template.create');
    }
    public function storeTemplate(assessmentTemplateRequests $request)
    {
        // dd($request->all());
        try {
            // Memulai transaksi
            DB::beginTransaction();

            TemplateAssessmentPoint::UpdateOrCreate([
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
        $userCompanyId = Auth::user()->company_id;

        if ($checkStatus == 'Admin') {
            $data_event = Event::whereHas('companies', function ($query) use ($userCompanyId) {
                $query->where('company_id', $userCompanyId);
            })->where('status', '!=', 'finish')->get();
        } elseif ($checkStatus == 'Superadmin') {
            $data_event = Event::where('status', '!=', 'finish')->get();
        }

        return view('auth.admin.assessment.assessment_point', [
            'data_event' => $data_event,
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
            ->select('team_name', 'papers.id as paper_id', 'innovation_title', 'category_name', 'pvt_event_teams.event_id', 'pvt_event_teams.id as event_team_id', 'pvt_event_teams.status as status_event', 'proof_idea', 'full_paper', 'full_paper_updated_at')
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
        $judgeService = app(JudgeService::class);
        $is_judge = $judgeService->isJudge(Auth::user());

        // dd($sofiData);
        return view('auth.juri.assessment_oda', compact('datas', 'sofiData', 'datas_juri', 'is_judge'));
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

    // FIXME: INI FUNGSI YG DIPAKE SUBMIT SCORE
    public function submitJuri(Request $request, $event_team_id)
    {
        try {
            // Get Total Score
            $totalScore = 0;
            foreach ($request->score as $id => $score) {
                pvtAssesmentTeamJudge::where('id', $id)
                    ->update(['score' => $score,]);
                $totalScore += $score;
            }
            $previousFullUrl = url()->previous();
            $segments = explode(
                '/',
                $previousFullUrl
            );
            $value = $segments[4];
            $sofi = NewSofi::where('event_team_id', $event_team_id)->first();
            $sofi->update([
                'strength' => $request->sofi_strength,
                'opportunity_for_improvement' => $request->sofi_opportunity,
                'recommend_category' => $request->recommendation,
                'suggestion_for_benefit' => $request->suggestion_for_benefit
            ]);

            $pvtEventTeam = PvtEventTeam::findOrFail($event_team_id);
            if ($value === "assessment-ondesk-value") {
                $pvtEventTeam->update([
                    'total_score_on_desk' => $this->calculateAverageTotalScore($event_team_id, "on desk")
                ]);
                if ($sofi) {
                    $sofi->update([
                        'last_stage' => 'on desk'
                    ]);
                }
            } elseif ($value === "assessment-presentation-value") {
                $pvtEventTeam->update([
                    'total_score_presentation' => $this->calculateAverageTotalScore($event_team_id, "presentation")
                ]);
                if ($sofi) {
                    $sofi->update([
                        'last_stage' => 'presentation'
                    ]);
                }
            } elseif ($value === "assessment-caucus-value") {
                $pvtEventTeam->update([
                    'total_score_caucus' => $this->calculateAverageTotalScore($event_team_id, "presentation")
                ]);
                if ($sofi) {
                    $sofi->update([
                        'last_stage' => 'caucus'
                    ]);
                }
            }
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
        $userEmployeeId = Auth::user()->employee_id;
        $is_judge = Judge::where('employee_id', $userEmployeeId)->exists();
        $data_event = collect(); // Inisialisasi default untuk $data_event

        if (auth()->user()->role == "Superadmin") {
            $data_event = Event::where('status', '=', 'active')->get();
        } elseif (auth()->user()->role == "Admin") {
            $data_event = Event::where('status', '=', 'active')
                ->whereHas('companies', function ($query) {
                    $query->where('company_code', auth()->user()->company_code);
                })
                ->get();
        } elseif (
            auth()->user()->role == "Juri" ||
            $is_judge
        ) {
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

        // dd($data_event);
        // dd($data_category);
        // dd($is_judge);

        return view('auth.user.assessment.ondesk', [
            "data_event" => $data_event,
            'data_category' => $data_category,
            'is_judge' => $is_judge
        ]);
    }

    public function presentation()
    {
        $userEmployeeId = Auth::user()->employee_id;
        $is_judge = Judge::where('employee_id', $userEmployeeId)->exists();
        if (auth()->user()->role == "Superadmin") {
            $data_event = Event::where('status', '=', 'active')->get();
        } elseif (auth()->user()->role == "Admin") {
            $data_event = Event::where('status', '=', 'active')
                ->whereHas('companies', function ($query) {
                    $query->where('company_code', auth()->user()->company_code);
                })
                ->get();
        } elseif (auth()->user()->role == "Juri" || $is_judge) {
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
            'data_category' => $data_category,
            'is_judge' => $is_judge
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
            ->where('pvt_assessment_events.stage', 'on desk')
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
        dd(PvtEventTeam::findOrFail($id));
        $dataTeam = Team::join('papers', 'papers.team_id', '=', 'teams.id')
            ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            // ->join('pvt_assesment_team_judges', 'pvt_assesment_team_judges.event_team_id', '=', 'pvt_event_teams.id')
            ->join('new_sofi', 'new_sofi.event_team_id', '=', 'pvt_event_teams.id')
            ->join('events', 'events.id', '=', 'pvt_event_teams.event_id')
            ->select('pvt_event_teams.id as event_team_id', 'teams.id as team_id', 'team_name', 'innovation_title', 'inovasi_lokasi', 'event_name', 'financial', 'potential_benefit', 'potensi_replikasi', 'recommend_category', 'strength', 'opportunity_for_improvement', 'suggestion_for_benefit')
            ->where('pvt_event_teams.id', $id)
            ->first();
        dd($dataTeam);

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
        try {
            DB::beginTransaction();
            if (isset($request->event_id)) {
                $teams_id = PvtEventTeam::where('event_id', $request->event_id)
                    ->pluck('id')
                    ->toArray();
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
                        $data_assessment_team_judge =
                            DB::table('pvt_assesment_team_judges as judge')
                            ->join('pvt_assessment_events as event', 'judge.assessment_event_id', '=', 'event.id')
                            ->where('judge.event_team_id', $team_id)
                            ->where('judge.stage', 'on desk')
                            ->where('event.stage', 'on desk')
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
                    if ($event_team->total_score_on_desk >= $score_oda) {
                        $event_team->status = 'Presentation';
                    } else {
                        $event_team->status = 'tidak lolos On Desk';
                    }


                    $event_team->save();

                    $team_status = PvtEventTeam::where('id', $event_team_id)
                        ->pluck('status')
                        ->toArray();

                    if ($team_status[0] == 'Presentation') {
                        $data_assessment_team_judge =
                            DB::table('pvt_assesment_team_judges as judge')
                            ->join('pvt_assessment_events as event', 'judge.assessment_event_id', '=', 'event.id')
                            ->where('judge.event_team_id', $event_team_id)
                            ->where('judge.stage', 'on desk')
                            ->where('event.stage', 'on desk')
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
                        ->havingRaw('ROUND(ROUND(SUM(pvt_assesment_team_judges.score), 2) / COUNT(CASE WHEN pvt_assesment_team_judges.assessment_event_id = ? THEN pvt_assesment_team_judges.assessment_event_id END), 2) <= ?', [$assessment_event_poin_idea, $nilai_pa_idea])
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


                foreach ($teams_id as $team_id) {
                    $team_status =
                        PvtEventTeam::where('event_id', $request->event_id)
                        ->where('status', 'Caucus')
                        ->pluck('status')
                        ->toArray();


                    if ($team_status[0] == 'Caucus') {
                        $data_assessment_team_judge =
                            DB::table('pvt_assesment_team_judges as judge')
                            ->join('pvt_assessment_events as event', 'judge.assessment_event_id', '=', 'event.id')
                            ->where('judge.event_team_id', $team_id)
                            ->where('judge.stage', 'presentation')
                            ->where('event.stage', 'presentation')
                            ->select('assessment_event_id', 'judge_id')
                            ->get();
                        $set_judge = [];
                        foreach ($data_assessment_team_judge as $assessment_team_judge) {
                            pvtAssesmentTeamJudge::updateOrCreate([
                                'judge_id'  => $assessment_team_judge->judge_id,
                                'event_team_id' => $team_id,
                                'assessment_event_id'   => $assessment_team_judge->assessment_event_id,
                                'stage'     => 'caucus'
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
                                        'stage'     => 'caucus'
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
                                        'stage'     => 'caucus'
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
                $score_oda = (int)MinimumscoreEvent::where('event_id', $event_id[0])
                    ->where('category', $category)
                    ->pluck('score_minimum_pa')
                    ->toArray()[0];

                foreach ($request->pvt_event_team_id as $index => $event_team_id) {
                    $event_team = PvtEventTeam::findOrFail($event_team_id);
                    // $total_score = intval($request->total_score_event[$index]);
                    // dd($total_score);
                    if ($event_team->total_score_presentation >= $score_oda) {
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
        try {
            if (isset($request->pvt_event_team_id)) {
                $pvtEventTeamItems = PvtEventTeam::findOrFail('id', $request->pvt_event_team_id);
                $pvtEventTeamItems->update([
                    'status' => 'Finish',
                    'final_score' => $pvtEventTeamItems
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
        $userEmployeeId = Auth::user()->employee_id;
        $is_judge = Judge::where('employee_id', $userEmployeeId)->exists();
        $currentYear = Carbon::now()->year;
        $isSuperadmin = Auth::user()->role === 'Superadmin';
        $companyCode = Auth::user()->company_code;

        if ($isSuperadmin) {
            // Jika Superadmin, lihat semua event aktif
            $data_event = Event::where('status', 'active')->get();
        } elseif ($is_judge) {
            // Jika Juri, hanya lihat event yang relevan
            $data_event = Event::where('status', 'active')
                ->whereHas('judges', function ($query) use ($userEmployeeId) {
                    $query->where('employee_id', $userEmployeeId);
                })
                ->get();
        } else {
            $data_event = Event::where('status', '=', 'active')
                ->whereHas('companies', function ($query) {
                    $query->where('company_code', auth()->user()->company_code);
                })
                ->get();
        }

        $data_category = Category::all();
        return view('auth.user.assessment.caucus', [
            "data_event" => $data_event,
            'data_category' => $data_category,
            'is_judge' => $is_judge
        ]);
    }

    public function summaryExecutive(Request $request)
    {
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

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('assessment.caucus.data')->withErrors('Error: ' . $e->getMessage());
        }
        return redirect()->route('assessment.caucus.data')->with('success', 'Data Berhasil disimpan');
    }
    public function summaryPPT(Request $request)
    {
        try {
            DB::beginTransaction();

            // Ambil id summary dari request
            $idSummary = $request->input('id');
            $filePpt = $request->file('file_ppt');

            // Cari data summary berdasarkan id
            $upPPT = SummaryExecutive::findOrFail($idSummary);

            // Cek apakah file PPT lama ada dan hapus jika ada
            if ($upPPT->file_ppt && Storage::disk('public')->exists($upPPT->file_ppt)) {
                Storage::disk('public')->delete($upPPT->file_ppt);
            }

            // Upload file PPT baru
            $file = $filePpt->storeAs(
                'summary_executive/file_ppt',
                Str::slug(pathinfo($filePpt->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $filePpt->getClientOriginalExtension(),
                'public'
            );

            // Update path file PPT di database
            $upPPT->file_ppt = $file;
            $upPPT->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('assessment.presentasiBOD')->withErrors('Error: ' . $e->getMessage());
        }

        return redirect()->route('assessment.presentasiBOD')->with('success', 'PDF berhasil di update');
    }

    public function addBODvalue(Request $request)
    {
        $summary = SummaryExecutive::where('pvt_event_teams_id', $request->pvt_event_team_id[0])->get();
        if (count($summary) !== 0) {
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

                    $event_team_item = PvtEventTeam::findOrFail($request->pvt_event_team_id);

                    $finalScore = ($event_team_item->total_score_on_desk + $event_team_item->total_score_caucus) / 2;

                    $event_team_item->update([
                        'final_score' => $finalScore
                    ]);

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
                        $total_score = $event_team->total_score_caucus;
                        if ($total_score >= $score_oda) {
                            $event_team->status = 'Presentation BOD';
                        } else {
                            $event_team->status = 'tidak lolos Caucus';
                        }

                        $event_team->save();
                    }

                    $event_team_item = PvtEventTeam::findOrFail($request->pvt_event_team_id[0]);

                    $finalScore = ($event_team_item->total_score_on_desk + $event_team_item->total_score_caucus) / 2;

                    $event_team_item->update([
                        'final_score' => $finalScore
                    ]);
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
        } else {
            return redirect()->back()->withErrors('Error : ' . "Silahkan mengisi summary terlebih dahulu");
        }
    }

    public function fixSubmitAllCaucus(Request $request)
    {
        $event_id = $request->event_id;
        try {
            // Mulai transaksi
            DB::beginTransaction();

            // Ambil semua tim berdasarkan event
            $teams = PvtEventTeam::where('event_id', $event_id)->get();

            if ($teams->isEmpty()) {
                return redirect()->back()->withErrors('Error: Tidak ada tim untuk event ini.');
            }

            // Ambil nilai PA minimum berdasarkan kategori untuk event ini
            $nilai_pa_bi = MinimumscoreEvent::where('category', 'BI/II')
                ->where(
                    'event_id',
                    $event_id
                )
                ->pluck('score_minimum_pa')
                ->first();

            $nilai_pa_idea = MinimumscoreEvent::where('category', 'IDEA')
                ->where('event_id', $event_id)
                ->pluck('score_minimum_pa')
                ->first();

            // dd($nilai_pa_idea);
            if (
                $nilai_pa_bi === null && $nilai_pa_idea === null
            ) {
                return redirect()->back()->withErrors('Error: Minimum score belum diatur untuk kategori tertentu.');
            }

            // Proses setiap tim dalam event
            foreach ($teams as $team) {
                $category_parent = $team->team->category->category_parent; // Ambil kategori parent tim
                $total_score = $team->total_score_caucus; // Ambil total score caucus

                // Tentukan nilai minimum sesuai kategori tim
                $nilai_pa = ($category_parent == "IDEA BOX") ? $nilai_pa_idea : $nilai_pa_bi;

                // Update status tim berdasarkan score
                if ($total_score >= $nilai_pa) {
                    $team->status = 'Presentation BOD';
                } else {
                    $team->status = 'Tidak lolos Caucus';
                }

                // Hitung final score
                $team->final_score = ($team->total_score_on_desk + $team->total_score_caucus) / 2;

                // Simpan perubahan
                $team->save();
            }

            // Commit transaksi
            DB::commit();
            return redirect()->back()->with(
                'success',
                'Semua tim berhasil diperbarui.'
            );
        } catch (\Exception $e) {
            // Rollback jika terjadi error
            DB::rollback();
            Log::error($e->getMessage());
            return redirect()->back()->withErrors('Error: ' . $e->getMessage());
        }
    }

    public function presentasiBOD(Request $request)
    {
        $userEmployeeId = Auth::user()->employee_id;
        $is_judge = Judge::where('employee_id', $userEmployeeId)->exists();
        $currentYear = Carbon::now()->year;
        $data_event = Event::where('status', 'active')->get();
        $data_category = Category::all();
        return view('auth.user.assessment.presentasi_bod', [
            "data_event" => $data_event,
            'data_category' => $data_category,
            'is_judge' => $is_judge
        ]);
    }
    public function penetapanJuara(Request $request)
    {
        $userEmployeeId = Auth::user()->employee_id;
        $is_judge = Judge::where('employee_id', $userEmployeeId)->exists();
        $currentYear = Carbon::now()->year;
        $data_event = Event::where('status', 'active')->get();
        $data_category = Category::all();
        $data = BeritaAcara::join('events', 'berita_acaras.event_id', 'events.id')
            ->select('berita_acaras.*', 'events.id as eventID', 'events.event_name', 'events.event_name', 'events.year', 'events.date_start', 'events.date_end')
            ->get();
        return view('auth.user.assessment.penetapan_juara', [
            "data_event" => $data_event,
            'data_category' => $data_category,
            'data' => $data,
            'is_judge' => $is_judge
        ]);
    }

    public function keputusanBOD(Request $request)
    {
        $data = $request->only(['pvt_event_team_id', 'total_score_event']);
        DB::beginTransaction();
        try {
            foreach ($data['pvt_event_team_id'] as $index => $eventTeamId) {
                $updateStatus = PvtEventTeam::findOrFail($eventTeamId);
                KeputusanBod::updateOrCreate(
                    ['pvt_event_teams_id' => $eventTeamId],
                    ['val_peringkat' => $updateStatus->final_score]
                );
                $updateStatus->status = 'Juara';
                $updateStatus->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // dd($e->getMessage());
            return redirect()->route('assessment.presentasiBOD')->withErrors('Belum ada Tim Yang Dipilih');
        }
        return redirect()->route('assessment.presentasiBOD')->with('success', 'Tim Telah Berhasil Menyelesaikan Makalah'); // masih belom tau
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

    public function calculateAverageTotalScore($event_id, $stage)
    {
        // Mengambil semua penilaian berdasarkan event_team_id
        $pvtAssesmentTeamJudgeByEventTeamIdItems = pvtAssesmentTeamJudge::where('event_team_id', $event_id)->where('stage', $stage)->get()->toArray();
        // Kategorikan penilaian berdasarkan assessment_event_id
        $categorizedScores = [];
        foreach ($pvtAssesmentTeamJudgeByEventTeamIdItems as $item) {
            $assessmentEventId = $item['assessment_event_id'];
            // Jika kategori untuk assessment_event_id belum ada, buat array baru
            if (!isset($categorizedScores[$assessmentEventId])) {
                $categorizedScores[$assessmentEventId] = [];
            }
            // Tambahkan score ke kategori assessment_event_id
            $categorizedScores[$assessmentEventId][] = $item['score'];
        }

        // Hitung rata-rata untuk setiap assessment_event_id
        $averageScores = [];
        foreach ($categorizedScores as $assessmentEventId => $scores) {
            $averageScores[$assessmentEventId] = array_sum($scores) / count($scores);
        }

        // Hitung total dari semua nilai rata-rata
        $totalAverageScore = array_sum($averageScores);

        return $totalAverageScore;
    }

    public function addWatermarks($paperId) {
    try {
        // Ensure the filepath is correctly formatted by removing "f: " and trimming any leading/trailing slashes
        $filePath = storage_path('app/public/' . mb_substr(Paper::where('id', '=', $paperId)->pluck('full_paper')[0], 3));

        if (!file_exists($filePath)) {
                dump($filePath);
            return response()->json(['error' => 'File tidak ditemukan.'], 404);
        }

        $fpdi = new Fpdi();

        // Ambil Data User Saat Ini
        $currentDateTime = Carbon::now()->format('l, d F Y H:i:s');
        $userEmail = Auth::user()->email;
        $userIp = request()->ip();

        $watermarkText = "{$currentDateTime}\nDilihat oleh {$userEmail}\nIP: {$userIp}";

        $pageCount = $fpdi->setSourceFile($filePath);
        for ($pageNum = 1; $pageNum <= $pageCount; $pageNum++) {
            $tplIdx = $fpdi->importPage($pageNum);
            $fpdi->AddPage();
            $fpdi->useTemplate($tplIdx, 0, 0);

            // Tambahkan watermark
            $fpdi->SetAlpha(0.1); // Transparansi watermark
            $fpdi->SetFont('helvetica', 'B', 40);
            $fpdi->SetTextColor(255, 0, 0);

            // Memulai transformasi untuk rotasi
            $fpdi->StartTransform();
            $fpdi->Rotate(45, 150, 100); // Adjusted rotation and position
            $fpdi->MultiCell(180, 280, $watermarkText, 0, 'C'); // Adjusted size and position
            $fpdi->StopTransform(); // Akhiri transformasi

            $fpdi->SetAlpha(1); // Reset transparansi
        }

        return response($fpdi->Output($filePath, 'I'), 200)->header('Content-Type', 'application/pdf');
    } catch (FileNotFoundException $e) {
        return response()->json(['error' => 'File tidak ditemukan.'], 404);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
}