<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\PvtAssessmentEvent;
use App\Models\PvtEventTeam;
use App\Models\SummaryExecutive;
use App\Models\Team;
use DataTables;
use DB;
use Exception;
use Illuminate\Http\Request;
use Log;

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
        $data_event = Event::where('status', 'active')->get();
        $data_category = Category::all();
        return view('auth.user.assessment.best_of_the_best', [
            "data_event" => $data_event,
            'data_category' => $data_category,
        ]);
    }
    public function getBestOfTheBest(Request $request)
    {
        try {
            // Cek apakah filterCategory ada atau null
            $data_category = Category::select('id', 'category_name', 'category_parent');
            $data_category = $data_category->get()
                ->toArray();

            $data_category_first = Category::where('id', $request->filterCategory)
                ->select('category_name', 'category_parent', 'id')->first();

            // Jika tidak ada category yang dipilih (null), ambil semua data
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
                // Jika category null, ambil semua kategori
                $arr_event_id = PvtAssessmentEvent::where('event_id', $request->filterEvent)
                    ->where('status_point', 'active')
                    ->where('stage', 'presentation')
                    ->select('id', 'pdca', 'point')
                    ->get()
                    ->toArray();
            }

            $categoryid = array_column($data_category, 'id');

            // Query untuk mengambil data tim dan skor
            $arr_select_case = [
                DB::raw('MIN(teams.id) as team_id'),
                DB::raw('MIN(team_name) as Tim'),
                DB::raw('MIN(innovation_title) as Judul'),
                DB::raw('MIN(category_name) as Kategori'),
                'pvt_event_teams.id AS event_team_id(removed)',
                'pvt_event_teams.is_best_of_the_best AS is_best_of_the_best',
                DB::raw('pvt_event_teams.final_score as final_score'),
            ];

            // Query utama untuk mengambil data tim
            $data_row = Team::join('papers', 'papers.team_id', '=', 'teams.id')
                ->join('categories', 'categories.id', '=', 'teams.category_id')
                ->join('themes', 'themes.id', '=', 'teams.theme_id')
                ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
                ->join('pvt_assesment_team_judges', 'pvt_assesment_team_judges.event_team_id', '=', 'pvt_event_teams.id')
                ->join('pvt_assessment_events', function ($join) {
                    $join->on('pvt_assessment_events.id', '=', 'pvt_assesment_team_judges.assessment_event_id');
                })
                ->where('pvt_event_teams.event_id', $request->filterEvent)
                ->where('pvt_event_teams.status', 'Juara')
                ->where('pvt_assessment_events.stage', 'presentation')
                ->whereNotIn('papers.status_event', ['reject_group', 'reject_national', 'reject_international'])
                ->groupBy('pvt_event_teams.id')
                ->select($arr_select_case);


            // Jika filterCategory tidak null, tambahkan filter untuk kategori
            if ($request->filterCategory) {
                $data_row->where('categories.id', $request->filterCategory);
            }

            // Urutkan berdasarkan MIN(category_name) dan final_score
            $dataTable = DataTables::of($data_row->orderBy(DB::raw('MIN(category_name)')) // Gunakan alias dari SELECT
                ->orderBy('final_score', 'desc') // Kemudian urutkan berdasarkan final_score
                ->get())->addIndexColumn();


            $rawColumns[] = 'Ranking';
            $dataTable->addColumn('Ranking', function ($data_row) use ($request, $categoryid) {
                $data_total = pvtEventTeam::join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                    ->join('categories', 'categories.id', '=', 'teams.category_id')
                    ->where('pvt_event_teams.event_id', $request->filterEvent)  // Filter berdasarkan event
                    ->whereIn('categories.id', $categoryid)                     // Filter berdasarkan kategori
                    ->whereNotNull('pvt_event_teams.final_score')  // Mengecualikan yang null
                    ->groupBy('pvt_event_teams.id', 'categories.id')            // Kelompokkan berdasarkan kategori
                    ->select(
                        DB::raw("DENSE_RANK() OVER (PARTITION BY categories.id ORDER BY pvt_event_teams.final_score DESC) AS \"Ranking\""), // Menghitung ranking per kategori
                        'pvt_event_teams.id as id',
                        'pvt_event_teams.final_score',
                        'categories.id as category_id'
                    )  // Menambahkan kategori ke dalam hasil
                    ->get()
                    ->keyBy('id')  // Ubah hasil query menjadi key-value pair dengan id sebagai key
                    ->toArray();

                // Cek apakah total_score_presentation null atau 0
                $eventTeamId = $data_row['event_team_id(removed)'];

                // Kembalikan ranking untuk event_team_id saat ini
                return $data_total[$eventTeamId]['Ranking'];
            });

            $rawColumns[] = 'Pilih Tim';
            $dataTable->addColumn('Pilih Tim', function ($data_row) use ($request, $categoryid) {
                // Ambil ranking tim berdasarkan event_team_id saat ini
                $eventTeamId = $data_row['event_team_id(removed)'];

                // Dapatkan data ranking tim
                $data_total = pvtEventTeam::join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                    ->join('categories', 'categories.id', '=', 'teams.category_id')
                    ->where('pvt_event_teams.event_id', $request->filterEvent)  // Filter berdasarkan event
                    ->whereIn('categories.id', $categoryid)                     // Filter berdasarkan kategori
                    ->whereNotNull('pvt_event_teams.final_score')  // Mengecualikan yang null
                    ->groupBy('pvt_event_teams.id', 'categories.id')            // Kelompokkan berdasarkan kategori
                    ->select(
                        DB::raw("DENSE_RANK() OVER (PARTITION BY categories.id ORDER BY pvt_event_teams.final_score DESC) AS \"Ranking\""), // Menghitung ranking per kategori
                        'pvt_event_teams.id as id',
                        'pvt_event_teams.final_score',
                        'categories.id as category_id'
                    )  // Menambahkan kategori ke dalam hasil
                    ->get()
                    ->keyBy('id')  // Ubah hasil query menjadi key-value pair dengan id sebagai key
                    ->toArray();

                // Cek ranking untuk event_team_id saat ini
                $ranking = isset($data_total[$eventTeamId]) ? $data_total[$eventTeamId]['Ranking'] : null;

                // Hanya tampilkan checkbox jika ranking adalah 1
                if ($ranking === 1) {
                    $checked = $data_row['is_best_of_the_best'] ? 'checked' : ''; // Check if true and set checked

                    if (auth()->user()->role === 'Admin' || auth()->user()->role === 'Superadmin') {
                        return '
            <div class="form-check">
                <input class="form-check-input" type="radio" id="radio-' . $eventTeamId . '" name="pvt_event_team_id[]" value="' . $eventTeamId . '" ' . $checked . '>
                <label class="form-check-label" for="radio-' . $eventTeamId . '">
                    Pilih
                </label>
            </div>
            ';
                    } else {
                        return '-';
                    }
                } else {
                    return '-'; // Jika ranking bukan 1, kembalikan tanda '-'
                }
            });

            // Mark 'Pilih Tim' column as raw
            $dataTable->rawColumns($rawColumns);

            // Menghapus kolom yang mengandung kata "removed"
            $remove_column = [];
            foreach ($dataTable->original as $data_column) {
                foreach ($data_column->getAttributes() as $column => $value) {
                    if (strstr($column, "removed") !== false || $column === 'team_id' || $column === 'is_best_of_the_best') {
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
        // Get the selected team IDs from the request
        $selectedTeams = $request->input('pvt_event_team_id');

        // Check if there are selected teams
        if (empty($selectedTeams) || count($selectedTeams) !== 1) {
            return response()->json(['message' => 'Please select exactly one team.'], 400);
        }

        try {
            // First, set all existing "Best of the Best" teams to false
            PvtEventTeam::where('is_best_of_the_best', true)
                ->update(['is_best_of_the_best' => false]);

            // Now, update the selected team to mark it as the best of the best
            PvtEventTeam::whereIn('id', $selectedTeams)
                ->update(['is_best_of_the_best' => true]);

            return redirect()->route('assessment.showDeterminingTheBestOfTheBestTeam')->with('success', 'Team berhasil dipilih menjadi Best of the Best');
        } catch (\Exception $e) {
            return redirect()->route('assessment.showDeterminingTheBestOfTheBestTeam')->withErrors('Error: ' . $e->getMessage());
        }
    }
}
