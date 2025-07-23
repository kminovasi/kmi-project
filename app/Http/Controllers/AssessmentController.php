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
use App\Models\History;
use App\Models\NewSofi;
use App\Models\BodEvent;
use App\Models\Category;
use App\Models\SummaryPPT;
use App\Models\BeritaAcara;
use Illuminate\Support\Str;
use App\Models\KeputusanBOD;
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
use Illuminate\Support\Facades\View;
use App\Models\pvtAssesmentTeamJudge;
use App\Models\TemplateAssessmentPoint;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\assessmentPointRequests;
use App\Http\Requests\assessmentTemplateRequests;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class AssessmentController extends Controller
{
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
                ->where('type', ['internal', 'AP'])
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

        return redirect()->route('assessment.show.template')->with('success', 'Template Penilaian Berhasil disimpan'); // masih belom tau
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
        return redirect()->route('assessment.show.template')->with('success', 'Template Penilaian berhasil disimpan'); // masih belom tau
    }


    public function showAssessmentPoint()
    {
        $checkStatus = Auth::user()->role;
        $userCompanyCode = Auth::user()->company_code;

        if ($checkStatus == 'Admin') {
            $data_event = Event::whereHas('companies', function ($query) use ($userCompanyCode) {
                $query->where('company_code', $userCompanyCode);
            })
            ->where('status', '!=', 'finish')
            ->where('type', ['internal', 'AP'])
            ->get();
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
    
            $dataAssessmentPoint = PvtAssessmentEvent::where('event_id', $event_id)
                ->where('category', $request->category)
                ->where('stage', 'on desk')
                ->whereNotIn('id', $request->assessment_poin_id)
                ->get();
    
            $dataEventTeam = PvtEventTeam::where('event_id', $event_id)
                ->pluck('id')
                ->toArray();
    
            foreach ($request->assessment_poin_id as $poin_id) {
                PvtAssessmentEvent::where('id', $poin_id)->update(['status_point' => 'active']);
    
                foreach ($dataEventTeam as $eventTeam) {
                    $dataJudgeInPvtAssessment = pvtAssesmentTeamJudge::distinct()
                        ->where('event_team_id', $eventTeam)
                        ->select('judge_id')
                        ->get();
    
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
                'category' => $request->category,
            ], [
                'score_minimum_oda' => $request->minimumscore_oda,
                'score_minimum_pa' => $request->minimumscore_pa,
            ]);
    
            DB::commit();
    
            Session::put('buttonStatus', 'disabled');
    
            return redirect()->back()->with('success', 'Status Poin Penilaian berhasil diperbarui');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors('Error: ' . $e->getMessage());
        }
    }

    
    public function showBeritaAcaraBenefit($paperId)
    {
        $benefit = Paper::find($paperId);
        if ($benefit) {
            $filePath = storage_path('app/public/' . ltrim(Paper::where('id', '=', $paperId)->pluck('file_review')[0], '/'));
            if (file_exists($filePath)) {
                return response()->file($filePath);
            } else {
                throw new FileNotFoundException($filePath);
            }
        } else {
            return redirect()->back()->withErrors('File not found.');
        }
    }

    public function assessmentValue_oda($id)
    {
        // code untuk menampilkan detail team
        $datas = PvtEventTeam::join('teams', 'teams.id', 'pvt_event_teams.team_id')
            ->join('papers', 'papers.team_id', '=', 'teams.id')
            ->join('categories', 'categories.id', '=', 'teams.category_id')
            ->where('pvt_event_teams.id', $id)
            ->select(
                'teams.team_name as team_name',
                'papers.id as paper_id',
                'papers.financial',
                'papers.potential_benefit',
                'papers.updated_at',
                'innovation_title',
                'categories.category_name', 
                'pvt_event_teams.event_id', 
                'pvt_event_teams.id as event_team_id', 
                'pvt_event_teams.status as status_event', 
                'proof_idea',
                'full_paper', 
                'full_paper_updated_at'
            )
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
            ->select(
                'team_name', 
                'papers.id as paper_id', 
                'innovation_title', 
                'category_name', 
                'papers.potential_benefit',
                'papers.financial',
                'papers.updated_at',
                'innovation_title',
                'pvt_event_teams.event_id', 
                'pvt_event_teams.id as event_team_id', 
                'pvt_event_teams.status as status_event',
                'pvt_event_teams.total_score_on_desk as score_ondesk',
                'proof_idea', 
                'full_paper', 
                'full_paper_updated_at'
            )
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
            ->select(
                'team_name', 
                'innovation_title', 
                'category_name',
                'papers.financial',
                'papers.potential_benefit',
                'papers.updated_at',
                'total_score_presentation as score_presentation',
                'innovation_title',
                'pvt_event_teams.event_id', 
                'pvt_event_teams.id as event_team_id', 
                'pvt_event_teams.status as status_event'
            )
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
        $request->validate([
            'financial_benefit' => 'required|regex:/^[0-9.]+$/',
            'potential_benefit' => 'required|regex:/^[0-9.]+$/',
            'updated_at' => 'required|date',
        ]);
        
        try {
            // Get Total Score
            foreach ($request->score as $id => $score) {
                pvtAssesmentTeamJudge::where('id', $id)
                    ->update(['score' => $score,]);
            }
            
            $sofi = NewSofi::where('event_team_id', $event_team_id)->first();
            $sofi->update([
                'strength' => $request->sofi_strength,
                'opportunity_for_improvement' => $request->sofi_opportunity,
                'recommend_category' => $request->recommendation,
                'suggestion_for_benefit' => $request->suggestion_for_benefit
            ]);
            
            $team_id = DB::table('pvt_event_teams')
                ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                ->where('pvt_event_teams.id', $event_team_id)
                ->value('teams.id');
        
            if (!$team_id) {
                return back()->with('error', 'Tim tidak ditemukan.');
            }
        
            $paper = Paper::where('team_id', $team_id)->first();
        
            if (!$paper) {
                return back()->with('error', 'Data paper tidak ditemukan.');
            }
        
            // Validasi optimistic locking
            if (!Carbon::parse($request->updated_at)->equalTo($paper->updated_at)) {
                return back()->with('error', 'Data sudah diubah oleh orang lain. Silakan muat ulang halaman.');
            }
            
            $financial = $request->financial_benefit ? preg_replace('/[^0-9]/', '', $request->financial_benefit) : 0;
            $potential = $request->potential_benefit ? preg_replace('/[^0-9]/', '', $request->potential_benefit) : 0;
        
            // Simpan update dalam transaction
            DB::transaction(function () use ($request, $team_id, $financial, $potential) {
                DB::table('papers')
                    ->where('team_id', $team_id)
                    ->update([
                        'financial' => $financial,
                        'potential_benefit' => $potential,
                        'updated_at' => now()
                    ]);
            });

            $pvtEventTeam = PvtEventTeam::findOrFail($event_team_id);
            
            if ($request->stage == "assessment-ondesk-value") {
                $pvtEventTeam->update([
                    'total_score_on_desk' => $this->calculateAverageTotalScore($event_team_id, "on desk")
                ]);
                if ($sofi) {
                    $sofi->update([
                        'last_stage' => 'on desk'
                    ]);
                }
                History::create([
                    'team_id' => $pvtEventTeam->team_id,
                    'activity' => "Nilai stage On Desk telah ditetapkan",
                    'status' => 'passed'
                ]);
            } elseif ($request->stage == "assessment-presentation-value") {
                $pvtEventTeam->update([
                    'total_score_presentation' => $this->calculateAverageTotalScore($event_team_id, "presentation")
                ]);
                if ($sofi) {
                    $sofi->update([
                        'last_stage' => 'presentation'
                    ]);
                }
                History::create([
                    'team_id' => $pvtEventTeam->team_id,
                    'activity' => "Nilai stage Presentasi telah ditetapkan",
                    'status' => 'passed'
                ]);
            } elseif ($request->stage == "assessment-caucus-value") {
                $pvtEventTeam->update([
                    'total_score_caucus' => $this->calculateAverageTotalScore($event_team_id, "caucus")
                ]);
                if ($sofi) {
                    $sofi->update([
                        'last_stage' => 'caucus'
                    ]);
                }
                History::create([
                    'team_id' => $pvtEventTeam->team_id,
                    'activity' => "Nilai stage Caucus telah ditetapkan",
                    'status' => 'passed'
                ]);
            } else {
                return back()->with('error', 'Stage tidak valid.');
            }

            return redirect()->back()->with('success', 'Nilai Berhasil Ditambahkan');
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
            })
                ->where('pvt_event_teams.id', $request->event_team_id)
                ->where('pvt_assessment_events.category', $category)
                ->where('pvt_assessment_events.status_point', 'active')
                ->when($request->input('stage') == 'on desk', function ($query) use ($request) {
                    $query->where('pvt_assessment_events.stage', $request->input('stage'));
                })
                ->pluck(
                    'pvt_assessment_events.id as assessment_event_id'
                )
                ->toArray();

            $dataAssesmentTeamJudge_null = pvtAssesmentTeamJudge::where('event_team_id', $request->event_team_id)
                ->whereIn('assessment_event_id', $data_assessment_event)
                ->where('judge_id', null)
                ->where('stage', $request->input('stage'))
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
            return redirect()->back()->with('success', 'Juri Berhasil Ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error: ' . $e->getMessage());
        }
    }

    public function deleteJuri(Request $request)
    {
        try {
            $event_team_id = $request->input('event_team_id');
            $stage = $request->input('stage');
            pvtAssesmentTeamJudge::where('judge_id', $request->judge_id)
                ->where('event_team_id', $event_team_id)
                ->where('stage', $stage)
                ->delete();
            
            if($request->input('stage') == 'on desk') {
                PvtEventTeam::where('id', $event_team_id)
                    ->update([
                        'total_score_on_desk' => $this->calculateAverageTotalScore($event_team_id, "on desk")
                    ]);
            } elseif($request->input('stage') == 'presentation') {
                PvtEventTeam::where('id', $event_team_id)
                    ->update([
                        'total_score_presentation' => $this->calculateAverageTotalScore($event_team_id, "presentation")
                    ]);
            } elseif($request->input('stage') == 'caucus') {
                PvtEventTeam::where('id', $event_team_id)
                    ->update([
                        'total_score_caucus' => $this->calculateAverageTotalScore($event_team_id, "caucus")
                    ]);
            }            

            return redirect()->back()->with('success', 'Juri Berhasil Dihapus');
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
        $data_category = Category::orderBy('category_name', 'ASC')->get();

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
        $data_category = Category::orderBy('category_name', 'ASC')->get();

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
            ->select(
                'teams.id as team_id', 
                'pvt_event_teams.id as event_team_id', 
                'team_name', 
                'innovation_title', 
                'inovasi_lokasi', 
                'event_name', 
                'events.year',
                'financial', 
                'potential_benefit', 
                'potensi_replikasi', 
                'recommend_category', 
                'strength', 
                'opportunity_for_improvement', 
                'suggestion_for_benefit'
            )
            ->where('pvt_event_teams.id', $id)
            ->first();

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
            ->where('pvt_assesment_team_judges.stage', 'on desk')
            ->where('pvt_assessment_events.stage', 'on desk')
            ->groupBy('pvt_event_teams.id', 'pvt_assessment_events.id', 'pvt_assessment_events.pdca', 'pvt_assessment_events.id', 'point')->orderByRaw("CASE
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
            ->select('events.event_name', 'events.year', 'team_name', 'innovation_title', 'inovasi_lokasi', 'event_name', 'financial', 'potential_benefit', 'potensi_replikasi', 'recommend_category', 'strength', 'opportunity_for_improvement', 'suggestion_for_benefit')
            ->where('pvt_event_teams.id', $id)
            ->first();

        $dataNilai = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_assesment_team_judges.event_team_id', '=', 'pvt_event_teams.id')
            ->join('pvt_assessment_events', 'pvt_assessment_events.id', '=', 'pvt_assesment_team_judges.assessment_event_id')
            ->select('pvt_event_teams.id as id_event_team', 'pvt_assessment_events.pdca', 'pvt_assessment_events.id as id_point', 'point', DB::raw('ROUND(AVG(score),2) as average_score'))
            ->where('pvt_event_teams.id', $id)
            ->where('pvt_assessment_events.status_point', 'active')
            ->where('pvt_assesment_team_judges.stage', 'on desk')
            ->groupBy('pvt_event_teams.id', 'pvt_assessment_events.id', 'pvt_assessment_events.pdca', 'point')->orderByRaw("CASE
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
            ->select(
                'teams.id as team_id', 
                'pvt_event_teams.id as event_team_id', 
                'team_name', 
                'innovation_title', 
                'inovasi_lokasi', 
                'event_name', 
                'events.year',
                'financial', 
                'potential_benefit', 
                'potensi_replikasi', 
                'recommend_category', 
                'strength', 
                'opportunity_for_improvement', 
                'suggestion_for_benefit'
            )
            ->where('pvt_event_teams.id', $id)
            ->first();

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
            ->where('pvt_assesment_team_judges.stage', 'presentation')
            ->groupBy('pvt_event_teams.id', 'pvt_assessment_events.id', 'pvt_assessment_events.pdca', 'point')
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
        $dataTeam = Team::join('papers', 'papers.team_id', '=', 'teams.id')
            ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            // ->join('pvt_assesment_team_judges', 'pvt_assesment_team_judges.event_team_id', '=', 'pvt_event_teams.id')
            ->join('new_sofi', 'new_sofi.event_team_id', '=', 'pvt_event_teams.id')
            ->join('events', 'events.id', '=', 'pvt_event_teams.event_id')
            ->select(
                'teams.id as team_id', 
                'pvt_event_teams.id as event_team_id', 
                'team_name', 
                'innovation_title', 
                'inovasi_lokasi', 
                'event_name', 
                'events.year',
                'financial', 
                'potential_benefit', 
                'potensi_replikasi', 
                'recommend_category', 
                'strength', 
                'opportunity_for_improvement', 
                'suggestion_for_benefit'
            )
            ->where('pvt_event_teams.id', $id)
            ->first();

        $dataNilai = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_assesment_team_judges.event_team_id', '=', 'pvt_event_teams.id')
            ->join('pvt_assessment_events', 'pvt_assessment_events.id', '=', 'pvt_assesment_team_judges.assessment_event_id')
            ->select(
                'pvt_event_teams.id as id_event_team', 
                'pvt_assessment_events.pdca', 
                'pvt_assessment_events.id as id_point', 
                'point', DB::raw('ROUND(AVG(score),2) as average_score')
            )
            ->where('pvt_event_teams.id', $id)
            ->where('pvt_assessment_events.status_point', 'active')
            ->where('pvt_assesment_team_judges.stage', 'caucus')
            ->groupBy('pvt_event_teams.id', 'pvt_assessment_events.id', 'pvt_assessment_events.pdca', 'pvt_assessment_events.id', 'point')
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
            ->select(
                'teams.id as team_id', 
                'team_name',
                'events.event_name',
                'events.year',
                'innovation_title', 
                'inovasi_lokasi', 
                'event_name', 
                'financial', 
                'potential_benefit', 
                'potensi_replikasi', 
                'recommend_category', 
                'strength', 
                'opportunity_for_improvement', 
                'suggestion_for_benefit'
            )
            ->where('pvt_event_teams.id', $id)
            ->first();

        $dataNilai = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_assesment_team_judges.event_team_id', '=', 'pvt_event_teams.id')
            ->join('pvt_assessment_events', 'pvt_assessment_events.id', '=', 'pvt_assesment_team_judges.assessment_event_id')
            ->select('pvt_event_teams.id as id_event_team', 'pvt_assessment_events.pdca', 'pvt_assessment_events.id as id_point', 'point', DB::raw('ROUND(AVG(score),2) as average_score'))
            ->where('pvt_event_teams.id', $id)
            ->where('pvt_assessment_events.status_point', 'active')
            ->where('pvt_assesment_team_judges.stage', 'presentation')
            ->groupBy('pvt_event_teams.id', 'pvt_assessment_events.id', 'pvt_assessment_events.pdca', 'point')
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
                
                $passedStatus = 'Presentation';
                $failedStatus = 'tidak lolos Presentation';

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
                        ->where('pvt_event_teams.total_score_on_desk', '>=', $nilai_oda_bi)
                        ->groupBy('pvt_event_teams.id')
                        ->update([
                            'pvt_event_teams.status' => $passedStatus
                        ]);

                    PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                        ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                        ->join('categories', 'teams.category_id', '=', 'categories.id')
                        ->whereNot('categories.category_parent', '=', 'IDEA BOX')
                        ->where('pvt_event_teams.status', '=', 'On Desk')
                        ->whereIn('pvt_event_teams.id', $teams_id)
                        ->where('pvt_assesment_team_judges.stage', 'on desk')
                        ->where('pvt_event_teams.total_score_on_desk', '<=', $nilai_oda_bi)
                        ->groupBy('pvt_event_teams.id')
                        ->update([
                            'pvt_event_teams.status' => $failedStatus
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
                        ->where('pvt_event_teams.total_score_on_desk', '<=', $nilai_oda_idea)
                        ->groupBy('pvt_event_teams.id')
                        ->update([
                            'pvt_event_teams.status' => $passedStatus
                        ]);

                    PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                        ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                        ->join('categories', 'teams.category_id', '=', 'categories.id')
                        ->where('categories.category_parent', '=', 'IDEA BOX')
                        ->where('pvt_event_teams.status', '=', 'On Desk')
                        ->whereIn('pvt_event_teams.id', $teams_id)
                        ->where('pvt_assesment_team_judges.stage', 'on desk')
                        ->where('pvt_event_teams.total_score_on_desk', '<=', $nilai_oda_idea)
                        ->groupBy('pvt_event_teams.id')
                        ->update([
                            'pvt_event_teams.status' => $failedStatus
                        ]);
                }

                foreach ($teams_id as $team_id) {
                    $team_status = PvtEventTeam::where('id', $team_id)
                        ->pluck('status')
                        ->first();

                    if ($team_status === 'Presentation') {
                        // Ambil informasi tim untuk history
                        $team = PvtEventTeam::with('team')->where('id', $team_id)->first();

                        if ($team && $team->team) {
                            History::create([
                                'team_id' => $team->team->id,
                                'activity' => "Tim " . $team->team->team_name . " telah Lolos ke stage Presentasi",
                                'status' => 'passed'
                            ]);
                        } else {
                            Log::error("Team dengan ID $team_id tidak ditemukan.");
                        }

                        // Ambil daftar juri dan event yang terkait
                        $data_assessment_team_judge = DB::table('pvt_assesment_team_judges as judge')
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

                            $set_judge[$assessment_team_judge->judge_id] = true;
                        }

                        $cat_assessment_point = PvtEventTeam::join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                            ->join('categories', 'categories.id', '=', 'teams.category_id')
                            ->where('pvt_event_teams.id', $team_id)
                            ->pluck('category_parent')
                            ->first() === "IDEA BOX" ? "IDEA" : "BI/II";

                        $categoryFilter = $cat_assessment_point === 'IDEA' ? 'IDEA' : 'BI/II';

                        $data_assessment_presentation = PvtAssessmentEvent::where('event_id', $request->event_id)
                            ->where('category', $categoryFilter)
                            ->where('status_point', 'active')
                            ->where('stage', 'presentation')
                            ->get();

                        foreach ($data_assessment_presentation as $assessment_presentation) {
                            foreach ($set_judge as $judge => $isi) {
                                pvtAssesmentTeamJudge::updateOrCreate([
                                    'judge_id'  => $judge,
                                    'event_team_id' => $team_id,
                                    'assessment_event_id'   => $assessment_presentation->id,
                                    'stage'     => 'presentation'
                                ]);
                            }
                        }
                    } else if ($team_status === 'tidak lolos Presentation') {
                        // Ambil informasi tim untuk history
                        $team = PvtEventTeam::join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                            ->where('pvt_event_teams.id', $team_id)
                            ->select('teams.team_name')
                            ->first();

                        // Tambahkan history bahwa tim tidak lolos ke tahap Presentasi
                        History::create([
                            'team_id' => $team_id,
                            'activity' => "Tim " . $team->team_name . " tidak Lolos ke stage Presentasi",
                            'status' => 'failed'
                        ]);
                    }
                }

                DB::commit();
                return redirect()->back()->with('success', 'Nilai On Desk telah Berhasil Ditetapkan');
            } elseif (isset($request->pvt_event_team_id)) {
                $category = Category::where('id', $request->category)->pluck('category_parent') == "IDEA BOX" ? "IDEA" : "BI/II";
                $event_id = PvtEventTeam::where('id', $request->pvt_event_team_id[0])->pluck('event_id');
                $score_oda = (int)MinimumscoreEvent::where('event_id', $event_id)
                    ->where('category', $category)
                    ->pluck('score_minimum_oda')
                    ->toArray()[0];

                foreach ($request->pvt_event_team_id as $index => $event_team_id) {
                    $event_team = PvtEventTeam::findOrFail($event_team_id);
                    
                    // Ambil informasi tim untuk history
                    $team = PvtEventTeam::join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                        ->where('pvt_event_teams.team_id', $event_team->team_id)
                        ->select('teams.team_name', 'teams.id as team_id')
                        ->first();
                    
                    if ($event_team->total_score_on_desk >= $score_oda) {
                        $event_team->status = 'Presentation';

                        // Tambahkan history bahwa tim lolos ke tahap Presentasi
                        History::create([
                            'team_id' => $team->team_id,
                            'activity' => "Tim " . $team->team_name . " telah Lolos ke stage Presentasi",
                            'status' => 'passed'
                        ]);
                    } else {
                        $event_team->status = 'tidak lolos Presentation';

                        // Tambahkan history bahwa tim tidak lolos ke tahap Presentasi
                        History::create([
                            'team_id' => $team->team_id,
                            'activity' => "Tim " . $team->team_name . " tidak Lolos ke stage Presentasi",
                            'status' => 'failed'
                        ]);
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
                return redirect()->back()->with('success', 'Nilai On Desk Telah Berhasil Ditetapkan');
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
                $passedStatus = 'Caucus';
                $failedStatus = 'Tidak lolos Caucus';

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
                            'pvt_event_teams.status' => $failedStatus
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
                            // ->havingRaw('ROUND(ROUND(SUM(pvt_assesment_team_judges.score), 2) / COUNT(CASE WHEN pvt_assesment_team_judges.assessment_event_id = ? THEN pvt_assesment_team_judges.assessment_event_id END), 2) >= ?', [$assessment_event_poin_bi, $nilai_pa_bi])
                            // ->take($request->total_team)
                            ->update([
                                'pvt_event_teams.status' => $passedStatus
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
                            'pvt_event_teams.status' => $failedStatus
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
                            'pvt_event_teams.status' => $passedStatus
                        ]);
                }


                foreach ($teams_id as $team_id) {
                    $team_status = PvtEventTeam::where('event_id', $request->event_id)
                        ->where('status', 'Caucus')
                        ->pluck('status')
                        ->toArray();

                    if ($team_status[0] == 'Caucus') {
                        
                        // Ambil informasi tim untuk history
                        $team = PvtEventTeam::with('team')->where('id', $team_id)->first();

                        if($team && $team->team){
                            // Tambahkan history bahwa tim tidak lolos ke tahap Presentasi
                            History::create([
                                'team_id' => $team->team->id,
                                'activity' => "Tim " . $team->team->team_name . " Lolos ke stage Caucus",
                                'status' => 'passed'
                            ]);
                        }
                        
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
                    } else {
                        // Ambil informasi tim untuk history
                        $team = PvtEventTeam::join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                            ->where('pvt_event_teams.id', $team_id)
                            ->select('teams.team_name')
                            ->first();

                        // Tambahkan history bahwa tim tidak lolos ke tahap Presentasi
                        History::create([
                            'team_id' => $team_id,
                            'activity' => "Tim " . $team->team_name . " tidak Lolos ke stage Caucus",
                            'status' => 'failed'
                        ]);
                    }
                }

                DB::commit();
                return redirect()->back()->with('success', 'Nilai Presentasi Telah Berhasil Ditetapkan');
            } elseif (isset($request->pvt_event_team_id)) {
                $category = Category::where('id', $request->category)->pluck('category_parent') == "IDEA BOX" ? "IDEA" : "BI/II";
                $event_id = PvtEventTeam::where('id', $request->pvt_event_team_id[0])->pluck('event_id');
                $score_oda = (int)MinimumscoreEvent::where('event_id', $event_id[0])
                    ->where('category', $category)
                    ->pluck('score_minimum_pa')
                    ->toArray()[0];

                foreach ($request->pvt_event_team_id as $index => $event_team_id) {
                    $event_team = PvtEventTeam::findOrFail($event_team_id);
                    
                    // Ambil Informasi Tim Untuk History
                    $team = PvtEventTeam::with('team')->findOrFail($event_team->id);

                    if(!$team) {
                        Log::debug($team->team->team_name);
                    }                    

                    if ($event_team->total_score_presentation >= $score_oda) {
                        $event_team->status = 'Caucus';

                        History::create([
                            'team_id' => $team->team->id,
                            'activity' => "Tim " . $team->team->team_name . " Lolos ke stage Caucus",
                            'status' => 'passed'
                        ]);
                    } else {
                        $event_team->status = 'Tidak lolos Caucus';

                        History::create([
                            'team_id' => $team->team->id,
                            'activity' => "Tim " . $team->team->team_name . " tidak Lolos ke stage Caucus",
                            'status' => 'failed'
                        ]);
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
                return redirect()->back()->with('success', 'Nilai Presentasi telah Berhasil Ditetapkan');
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

        $data_category = Category::orderBy('category_name', 'ASC')->get();
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
        return redirect()->route('assessment.caucus.data')->with('success', 'Summary Berhasil disimpan');
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

        return redirect()->route('assessment.presentasiBOD')->with('success', 'Summary makalah sudah berhasil diungah');
    }

    public function addBODvalue(Request $request)
    {
        $summary = SummaryExecutive::where('pvt_event_teams_id', $request->pvt_event_team_id[0])->get();
        if (count($summary) !== 0) {
            try {
                DB::beginTransaction();
                if (isset($request->event_id)) {
                    // Ambil semua team_id berdasarkan event_id
                    $teams_id = PvtEventTeam::where('event_id', $request->event_id)
                        ->pluck('id')
                        ->toArray();

                    // Ambil nilai minimum PA untuk BI/II dan IDEA
                    $nilai_pa_bi = MinimumscoreEvent::where('category', 'BI/II')
                        ->where('event_id', $request->event_id)
                        ->value('score_minimum_pa');

                    $nilai_pa_idea = MinimumscoreEvent::where('category', 'IDEA')
                        ->where('event_id', $request->event_id)
                        ->value('score_minimum_pa');

                    // Ambil assessment event untuk BI/II dan IDEA
                    $assessment_event_poin_bi = PvtAssessmentEvent::where('event_id', $request->event_id)
                        ->where('category', 'BI/II')
                        ->where('status_point', 'active')
                        ->where('stage', 'presentation')
                        ->value('id');

                    $assessment_event_poin_idea = PvtAssessmentEvent::where('event_id', $request->event_id)
                        ->where('category', 'IDEA')
                        ->where('status_point', 'active')
                        ->where('stage', 'presentation')
                        ->value('id');

                    if ($nilai_pa_bi && $assessment_event_poin_bi) {
                        $categoryID_list = Category::whereNot('category_parent', 'IDEA BOX')->pluck('id')->toArray();

                        // Update status ke "tidak lolos Caucus" berdasarkan score
                        PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                            ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                            ->join('categories', 'teams.category_id', '=', 'categories.id')
                            ->whereNot('categories.category_parent', '=', 'IDEA BOX')
                            ->where('pvt_event_teams.status', '=', 'Caucus')
                            ->whereIn('pvt_event_teams.id', $teams_id)
                            ->where('pvt_assesment_team_judges.stage', 'caucus')
                            ->groupBy('pvt_event_teams.id')
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
                                ->update([
                                    'pvt_event_teams.status' => 'Presentation BOD'
                                ]);
                        }
                    }

                    if ($nilai_pa_idea && $assessment_event_poin_idea) {
                        PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                            ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                            ->join('categories', 'teams.category_id', '=', 'categories.id')
                            ->where('categories.category_parent', '=', 'IDEA BOX')
                            ->where('pvt_event_teams.status', '=', 'Caucus')
                            ->where('pvt_assesment_team_judges.stage', 'caucus')
                            ->whereIn('pvt_event_teams.id', $teams_id)
                            ->groupBy('pvt_event_teams.id')
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
                            ->update([
                                'pvt_event_teams.status' => 'Presentation BOD'
                            ]);
                    }

                    // Tambahkan history setelah status diperbarui
                    PvtEventTeam::with('team')
                        ->where('event_id', $request->event_id)
                        ->chunk(100, function ($teams) {
                            foreach ($teams as $team) {
                                if ($team->team) { // Pastikan tim memiliki relasi ke `team`
                                    $activity = ($team->status == 'Presentation BOD') 
                                        ? "Tim " . $team->team->team_name . " telah Lolos ke stage Presentasi BOD"
                                        : "Tim " . $team->team->team_name . " tidak Lolos ke stage Presentasi BOD";

                                    $status = ($team->status == 'Presentation BOD') ? 'passed' : 'failed';

                                    History::create([
                                        'team_id' => $team->team->id,
                                        'activity' => $activity,
                                        'status' => $status
                                    ]);
                                }
                            }
                        });

                    // Hitung final_score setelah semua perubahan status selesai
                    $event_team_item = PvtEventTeam::findOrFail($request->pvt_event_team_id);
                    $finalScore = ($event_team_item->total_score_on_desk + $event_team_item->total_score_caucus) / 2;
                    $event_team_item->update(['final_score' => $finalScore]);

                    DB::commit();
                    return redirect()->back()->with('success', 'Nilai Caucus Telah Berhasil Ditetapkan');
                } elseif (isset($request->pvt_event_team_id)) {
                    $category = Category::where('id', $request->category)->pluck('category_parent') == "IDEA BOX" ? "IDEA" : "BI/II";
                    $event_id = PvtEventTeam::where('id', $request->pvt_event_team_id[0])->pluck('event_id');
                    $score_oda = (int)MinimumscoreEvent::where('event_id', $event_id[0])
                        ->where('category', $category)
                        ->pluck('score_minimum_pa')
                        ->toArray()[0];

                    foreach ($request->pvt_event_team_id as $index => $event_team_id) {
                        // Ambil data event_team beserta relasi team
                        $event_team = PvtEventTeam::with('team')->findOrFail($event_team_id);
                        $total_score = $event_team->total_score_caucus;
                        
                        $event_team->status = 'Presentation BOD';
                        $activity = "Tim " . ($event_team->team->team_name ?? 'Tanpa Nama') . " telah Lolos ke stage Presentasi BOD";
                        $status = 'passed';
                        
                        $event_team->final_score = $total_score;

                        // Simpan perubahan status di tabel `pvt_event_teams`
                        $event_team->save();

                        // Cek apakah team tersedia sebelum menyimpan histori
                        if ($event_team->team) {
                            History::create([
                                'team_id' => $event_team->team->id, // Pastikan ini adalah `teams.id`
                                'activity' => $activity,
                                'status' => $status
                            ]);
                        }

                    }
                    
                    DB::commit();
                    return redirect()->back()->with('success', 'Nilai Caucus Telah Berhasil Ditetapkan');
                } else {
                    DB::rollback();
                    return redirect()->back()->withErrors('Error: tidak ada tim yang dipilih');
                }
            } catch (\Exception $e) {
                DB::rollback();
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

                // Cek apakah total score memenuhi syarat
                if ($total_score >= $nilai_pa) {
                    $team->status = 'Presentation BOD';
                    $activity = "Tim " . ($team->team->team_name ?? 'Tanpa Nama') . " telah Lolos ke stage Presentasi BOD";
                    $status = 'passed';
                } else {
                    $team->status = 'Tidak lolos Caucus';
                    $activity = "Tim " . ($team->team->team_name ?? 'Tanpa Nama') . " tidak Lolos ke stage Presentasi BOD";
                    $status = 'failed';
                }

                // Hitung final score
                $team->final_score = ($team->total_score_on_desk + $team->total_score_caucus) / 2;

                // Simpan perubahan status dan final score di tabel `pvt_event_teams`
                $team->save();

                // Cek apakah team tersedia sebelum menyimpan histori
                if ($team->team) {
                    History::create([
                        'team_id' => $team->team->id, // Pastikan ini adalah `teams.id`
                        'activity' => $activity,
                        'status' => $status
                    ]);
                } 
            }


            // Commit transaksi
            DB::commit();
            return redirect()->back()->with(
                'success',
                'Nilai Caucus Semua Tim Telah Berhasil Ditetapkan'
            );
        } catch (\Exception $e) {
            // Rollback jika terjadi error
            DB::rollback();
            return redirect()->back()->withErrors('Error: ' . $e->getMessage());
        }
    }

    public function presentasiBOD(Request $request)
    {
        $userEmployeeId = Auth::user()->employee_id;
        $is_judge = Judge::where('employee_id', $userEmployeeId)->exists();
        $data_event = Event::whereHas('companies', function ($query) {
                $query->where('company_code', auth()->user()->company_code);
            })
            ->where('status', 'active')
            ->get();

        $data_category = Category::orderBy('category_name', 'ASC')->get();
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
        $data_event = Event::whereHas('companies', function ($query) {
                $query->where('company_code', auth()->user()->company_code);
            })
            ->where('status', 'active')
            ->get();
        $data_category = Category::orderBy('category_name', 'ASC')->get();
        $data = BeritaAcara::join('events', 'berita_acaras.event_id', '=', 'events.id')
            ->join('company_event', 'events.id', '=', 'company_event.event_id')
            ->join('companies', 'company_event.company_id', '=', 'companies.id')
            ->when(auth()->user()->role !== 'Superadmin', function ($query) {
                $query->where('companies.company_code', auth()->user()->company_code);
            })
            ->select(
                'berita_acaras.*', 
                'events.id as eventID', 
                'events.event_name', 
                'events.year', 
                'events.date_start', 
                'events.date_end'
            )
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

                // Ambil informasi tim untuk history
                $team = PvtEventTeam::join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                    ->where('pvt_event_teams.id', $eventTeamId)
                    ->select('teams.team_name')
                    ->first();
                
                // Tambahkan history bahwa tim lolos ke tahap Presentasi
                History::create([
                    'team_id' => $updateStatus->team_id,
                    'activity' => "Selamat Tim " . $team->team_name . " telah Menyelesaikan Presentasi BOD",
                    'status' => 'passed'
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // dd($e->getMessage());
            return redirect()->route('assessment.presentasiBOD')->withErrors('Belum ada Tim Yang Dipilih');
        }
        return redirect()->route('assessment.presentasiBOD')->with('success', 'Nilai Akhir BOD Berhasil Ditetapkan');
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

    public function calculateAverageTotalScore($event_team_id, $stage)
    {
        // Mengambil semua penilaian berdasarkan event_team_id
        $pvtAssesmentTeamJudgeByEventTeamIdItems = pvtAssesmentTeamJudge::where('event_team_id', $event_team_id)->where('stage', $stage)->get()->toArray();
        // Kategorikan penilaian berdasarkan assessment_event_id
        $categorizedScores = [];
        foreach ($pvtAssesmentTeamJudgeByEventTeamIdItems as $item) {
            $assessmentEventId = $item['assessment_event_id'];
            if (!isset($categorizedScores[$assessmentEventId])) {
                $categorizedScores[$assessmentEventId] = [];
            }
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
            $filePath = storage_path('app/public/' . ltrim(Paper::where('id', '=', $paperId)->pluck('full_paper')[0], '/'));

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
            for($pageNum = 1; $pageNum <= $pageCount; $pageNum++) {
                $tplIdx = $fpdi->importPage($pageNum);
                $fpdi->AddPage();
                $fpdi->useTemplate($tplIdx, 0, 0);

                // Tambahkan watermark
                $fpdi->SetAlpha(0.1); // Transparansi watermark
                $fpdi->SetFont('helvetica', 'B', 40);
                $fpdi->SetTextColor(255, 0, 0);

                // Memulai transformasi untuk rotasi
                $fpdi->StartTransform();
                $fpdi->Rotate(45, 150, 50); // Atur sudut, x, y sesuai kebutuhan
                $fpdi->MultiCell(160, 180, $watermarkText, 0, 'C'); // Atur posisi watermark
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
    
    public function viewExecutiveSummary($eventTeamId)
    {
        try {
            $summary = SummaryExecutive::where('event_id', $eventTeamId)->first();
            if ($summary->file_ppt == null) {
                return redirect()->back()->withErrors('Error: Tidak ada ringkasan eksekutif untuk event ini.');
            }

            $filePath = storage_path('app/public/' . ltrim($summary->file_ppt, '/'));
            if (!file_exists($filePath)) {
                return redirect()->back()->withErrors('Error: File tidak ditemukan.');
            }

            $fpdi = new Fpdi();
            $pageCount = $fpdi->setSourceFile($filePath);
            for ($pageNum = 1; $pageNum <= $pageCount; $pageNum++) {
                $tplIdx = $fpdi->importPage($pageNum);
                $fpdi->AddPage();
                $fpdi->useTemplate($tplIdx, 0, 0);
            }
            return response($fpdi->Output($filePath, 'I'), 200)->header('Content-Type', 'application/pdf');
            
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Error: ' . $e->getMessage());
        }
    }
    
    public function viewSUpportingDocuments($eventTeamId) 
    {
        $supportingDocumentData = DB::table('pvt_event_teams')
            ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->join('papers', 'papers.team_id', '=', 'teams.id')
            ->join('document_supportings', 'document_supportings.paper_id', '=', 'papers.id')
            ->where('pvt_event_teams.id', $eventTeamId)
            ->select(
                'document_supportings.id as id',
                'file_name',
                'path'    
            )
            ->get();
        
        if ($supportingDocumentData->isEmpty()) {
            return response()->json(['message' => 'No documents found'], 404);
        }
        
        return response()->json($supportingDocumentData);
    }
}