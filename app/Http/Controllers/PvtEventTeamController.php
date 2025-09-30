<?php

namespace App\Http\Controllers;

use DB;
use Log;
use Auth;
use Exception;
use DataTables;
use App\Models\Team;
use App\Models\Event;
use App\Models\Judge;
use App\Models\History;
use App\Models\Category;
use App\Models\PvtEventTeam;
use Illuminate\Http\Request;
use App\Models\SummaryExecutive;
use App\Models\PvtAssessmentEvent;

class PvtEventTeamController extends Controller
{
    public function updateScoreKeputusanBOD(Request $request)
    {
        // Ambil string JSON dan bersihkan spasi atau karakter whitespace
        $json_data = trim($request->input('selected_data_team'));

        // Decode string JSON menjadi array PHP
        $selected_data_team = json_decode($json_data, true);

        // Akses team_id dari array yang sudah didecode
        $team_id = $selected_data_team['team_id(removed)'] ?? null;
        $event_team_id = $selected_data_team['event_team_id(removed)'] ?? null;

        $pvtEventTeamItem = PvtEventTeam::findOrFail($event_team_id);
        $summaryExecutive = SummaryExecutive::where('pvt_event_teams_id', $event_team_id)->first();
        if ($summaryExecutive->file_ppt !== null) {
            $pvtEventTeamItem->update([
                'final_score' => $request->val_peringkat,
            ]);
            return redirect()->route('assessment.presentasiBOD')->with('success', 'keputusan score berhasil di ubah');
        } else {
            return redirect()->route('assessment.presentasiBOD')->withErrors('Error : Silahkan upload file summary terlebih dahulu');
        }
    }

    public function showDeterminingTheBestOfTheBestTeam(Request $request)
    {
        $userEmployeeId = Auth::user()->employee_id;
        $is_judge = Judge::where('employee_id', $userEmployeeId)->exists();
        $data_event = Event::whereHas('companies', function ($query) {
                $query->where('company_code', auth()->user()->company_code);
            })
            ->where('status', 'active')
            ->get();
        $data_category = Category::orderBy('category_name', 'ASC')->get();
        return view('auth.user.assessment.best_of_the_best', [
            "data_event" => $data_event,
            'data_category' => $data_category,
            'is_judge' => $is_judge
        ]);
    }
    
    public function getBestOfTheBest(Request $request)
    {
        try {
            $data_category = Category::select('id', 'category_name', 'category_parent')->get()->toArray();
    
            $data_category_first = Category::where('id', $request->filterCategory)
                ->select('category_name', 'category_parent', 'id')->first();
    
            if ($data_category_first) {
                $category_type = ($data_category_first->category_parent == 'IDEA BOX') ? 'IDEA' : 'BI/II';
                $arr_event_id = PvtAssessmentEvent::where('event_id', $request->filterEvent)
                    ->where('category', $category_type)
                    ->where('status_point', 'active')
                    ->where('stage', 'presentation')
                    ->select('id', 'pdca', 'point')
                    ->get()
                    ->toArray();
            } else {
                $arr_event_id = PvtAssessmentEvent::where('event_id', $request->filterEvent)
                    ->where('status_point', 'active')
                    ->where('stage', 'presentation')
                    ->select('id', 'pdca', 'point')
                    ->get()
                    ->toArray();
            }
    
            $categoryid = array_column($data_category, 'id');
    
            // Ambil data ranking sekali saja
            $rankingData = PvtEventTeam::join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                ->join('categories', 'categories.id', '=', 'teams.category_id')
                ->where('pvt_event_teams.event_id', $request->filterEvent)
                ->whereIn('categories.id', $categoryid)
                ->whereNotNull('pvt_event_teams.final_score')
                ->select(
                    DB::raw("DENSE_RANK() OVER (PARTITION BY categories.id ORDER BY pvt_event_teams.final_score DESC) AS Ranking"),
                    'pvt_event_teams.id as id',
                    'pvt_event_teams.final_score',
                    'categories.id as category_id'
                )
                ->get()
                ->keyBy('id');
    
            $subquery = DB::table('pvt_event_teams')
                ->selectRaw('
                    DENSE_RANK() OVER (PARTITION BY categories.id ORDER BY pvt_event_teams.final_score DESC) AS Ranking,
                    pvt_event_teams.id as id,
                    pvt_event_teams.final_score as max_score,
                    categories.id as category_id
                ')
                ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                ->join('categories', 'categories.id', '=', 'teams.category_id')
                ->where('pvt_event_teams.event_id', $request->filterEvent)
                ->whereNotNull('pvt_event_teams.final_score')
                ->whereIn('categories.id', $categoryid);
    
            $arr_select_case = [
                'teams.id as team_id',
                'teams.team_name as Tim',
                'papers.innovation_title as Judul',
                'categories.category_name as Kategori',
                'pvt_event_teams.id as event_team_id_removed',
                'pvt_event_teams.is_best_of_the_best',
                'pvt_event_teams.is_honorable_winner',
                DB::raw('MAX(pvt_event_teams.keputusan_bod) as keputusan_bod'), 
                'pvt_event_teams.final_score as Score',
                'categories.id as category_id_removed'
            ];

            $data_row = Team::query()
                ->select($arr_select_case)
                ->join('papers', 'papers.team_id', '=', 'teams.id')
                ->join('categories', 'categories.id', '=', 'teams.category_id')
                ->join('themes', 'themes.id', '=', 'teams.theme_id')
                ->join('pvt_event_teams', function($join) use ($request) {
                    $join->on('pvt_event_teams.team_id', '=', 'teams.id')
                        ->where('pvt_event_teams.event_id', $request->filterEvent)
                        ->where('pvt_event_teams.status', 'Juara');
                })
                ->join('pvt_assesment_team_judges', 'pvt_assesment_team_judges.event_team_id', '=', 'pvt_event_teams.id')
                ->join('pvt_assessment_events', function($join) {
                    $join->on('pvt_assessment_events.id', '=', 'pvt_assesment_team_judges.assessment_event_id')
                        ->where('pvt_assessment_events.stage', 'presentation');
                })
                ->joinSub($subquery, 'max_scores', function($join) {
                    $join->on('teams.category_id', '=', 'max_scores.category_id')
                         ->on('pvt_event_teams.final_score', '=', 'max_scores.max_score');
                })
                ->whereNotIn('papers.status_event', ['reject_group', 'reject_national', 'reject_international'])
                ->groupBy([
                    'teams.id',
                    'teams.team_name',
                    'papers.innovation_title',
                    'categories.category_name',
                    'pvt_event_teams.id',
                    'pvt_event_teams.is_best_of_the_best',
                    'pvt_event_teams.is_honorable_winner',
                    'pvt_event_teams.final_score',
                    'categories.id'
                ])
                ->orderBy('categories.category_name')
                ->orderByDesc('pvt_event_teams.final_score');
    
            if ($request->filterCategory) {
                $data_row->where('categories.id', $request->filterCategory);
            }
    
            $dataTable = DataTables::of($data_row->get())
                ->addIndexColumn();
    
            // Tambahkan kolom Ranking
            $dataTable->addColumn('Ranking', function ($data_row) use ($rankingData) {
                $eventTeamId = $data_row->event_team_id_removed;
                return $rankingData[$eventTeamId]->Ranking ?? '-';
            });
    
            // Tambahkan kolom Pilih Tim
            $dataTable->addColumn('Best Of The Best', function ($data_row) use ($rankingData) {
                $eventTeamId = $data_row->event_team_id_removed;
                $ranking = $rankingData[$eventTeamId]->Ranking ?? null;
                    $checked = $data_row->is_best_of_the_best ? 'checked' : '';
    
                    if (auth()->user()->role === 'Admin' || auth()->user()->role === 'Superadmin') {
                        return '
                            <div class="form-check">
                                <input class="form-check-input" type="radio" id="radio-' . $eventTeamId . '" name="pvt_event_team_id[]" value="' . $eventTeamId . '" ' . $checked . '>
                                <label class="form-check-label" for="radio-' . $eventTeamId . '">
                                    Pilih
                                </label>
                            </div>
                        ';
                    }
            });
            
            $dataTable->addColumn('Juara Harapan', function ($data_row) {
                $eventTeamId = $data_row->event_team_id_removed;
            
                $checked = isset($data_row->is_honorable_winner) && $data_row->is_honorable_winner ? 'checked' : '';
            
                if (auth()->user()->role === 'Admin' || auth()->user()->role === 'Superadmin') {
                    return '
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="checkbox-harapan-' . $eventTeamId . '" name="juara_harapan_id[]" value="' . $eventTeamId . '"' . $checked . '>
                            <label class="form-check-label" for="checkbox-harapan-' . $eventTeamId . '">
                                Pilih
                            </label>
                        </div>
                    ';
                }
            
                return '-';
            });
            
                $dataTable->addColumn('Keputusan BOD', function ($row) {
                $id  = $row->event_team_id_removed;
                $val = $row->keputusan_bod ?? '';
            
                $checked     = $val !== '' ? ' checked' : '';
                $hiddenClass = $val === '' ? ' d-none' : '';
                $disabled    = $val === '' ? ' disabled' : '';
            
                return '
                <div class="d-flex align-items-center gap-2">
                    <div class="form-check m-0">
                        <input class="form-check-input keputusan-bod-check" type="checkbox"
                               id="checkbox-keputusan-bod-'.$id.'"
                               data-target="#input-keputusan-bod-'.$id.'"'.$checked.'>
                        <label class="form-check-label" for="checkbox-keputusan-bod-'.$id.'">Isi</label>
                    </div>
                    <input type="text"
                           class="form-control form-control-sm keputusan-bod-input'.$hiddenClass.'"
                           name="keputusan_bod['.$id.']"
                           id="input-keputusan-bod-'.$id.'"
                           value="'.e($val).'"
                           placeholder="Contoh: Juara Terbaik"'.$disabled.'>
                </div>';
            });

    
            $dataTable->rawColumns(['Ranking', 'Best Of The Best', 'Juara Harapan', 'Keputusan BOD']);
    
            $dataTable->removeColumn('team_id');
            $dataTable->removeColumn('is_best_of_the_best');
            $dataTable->removeColumn('is_honorable_winner');
             $dataTable->removeColumn('keputusan_bod');
            
            $remove_column = [];
                foreach ($dataTable->original as $data_column) {
                    foreach ($data_column->getAttributes() as $column => $value) {
                        if (strstr($column, "removed") !== false) {
                            $remove_column[] = $column;
                        }
                    }
                }
                $dataTable->removeColumn($remove_column);
    
            return $dataTable->toJson();
    
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }

    
    public function determiningTheBestOfTheBestTeam(Request $request)
    {
        $request->validate([
        'pvt_event_team_id' => 'required|array|size:1',
        'juara_harapan_id'  => 'nullable|array',
        'keputusan_bod'     => 'nullable|array',
        'keputusan_bod.*'   => 'nullable|string|max:100',
        ]);

        $selectedBestOfTheBest = $request->input('pvt_event_team_id');     
        $selectedHonorableWiner = $request->input('juara_harapan_id', []); 
        $keputusanBodMap = $request->input('keputusan_bod', []);           

        if (empty($selectedBestOfTheBest) || count($selectedBestOfTheBest) !== 1) {
            return redirect()->route('assessment.showDeterminingTheBestOfTheBestTeam')->with('error', 'Silahkan Pilih Satu Tim Untuk Dinominasikan');
        }

        try {
            PvtEventTeam::where('is_best_of_the_best', true)
                ->update(['is_best_of_the_best' => false]);
            PvtEventTeam::whereIn('id', $selectedBestOfTheBest)
                ->update(['is_best_of_the_best' => true]);
            if(!empty($selectedHonorableWiner)){
                PvtEventTeam::where('is_honorable_winner', true)
                    ->update(['is_honorable_winner' => false]);
                 PvtEventTeam::whereIn('id', $selectedHonorableWiner)
                    ->update(['is_honorable_winner' => true]);
            }

            foreach ($keputusanBodMap as $id => $text) {
            $text = trim((string)$text);
            PvtEventTeam::where('id', $id)->update([
                'keputusan_bod' => ($text === '' ? null : mb_substr($text, 0, 100)),
            ]);
        }
            return redirect()->route('assessment.showDeterminingTheBestOfTheBestTeam')->with('success', 'Penetapan Akhir Telah Berhasil');
        } catch (\Exception $e) {
            return redirect()->route('assessment.showDeterminingTheBestOfTheBestTeam')->withErrors('Error: ' . $e->getMessage());
        }
    }
}