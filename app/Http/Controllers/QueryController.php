<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Team;
use App\Models\User;
use App\Models\Event;
use App\Models\Judge;
use App\Models\Paper;
use App\Models\Company;
use App\Models\History;
use App\Models\BodEvent;
use App\Models\Category;
use App\Models\ph2Member;
use App\Models\PvtMember;
use App\Models\BeritaAcara;
use App\Models\PvtEventTeam;
use Illuminate\Http\Request;
use App\Services\JudgeService;
use App\Models\AssessmentPoint;
use App\Models\MetodologiPaper;
use App\Models\SummaryExecutive;
use App\Models\PvtAssessmentTeam;
use App\Models\PvtAssessmentEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\pvtAssesmentTeamJudge;
use Illuminate\Support\Facades\Cache;
use App\Models\CustomBenefitFinancial;
use App\Models\TemplateAssessmentPoint;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class QueryController extends Controller
{
    protected function checkIfJudge()
    {
        return Judge::where('employee_id', Auth::user()->employee_id)
            ->where('status', 'active')
            ->exists();
    }

    public function autocomplete(Request $request)
    {
        $query = $request->input('query');

        // Lakukan pencarian berdasarkan query dan kembalikan hasil sebagai JSON
        $results = User::join('companies', 'companies.company_code', '=', 'users.company_code')
            ->where('name', 'LIKE', "%$query%")
            ->orWhere('employee_id', 'LIKE', "%$query%")
            ->limit(10)
            ->get();

        if ($results->isEmpty()) {
            $results = User::take(10)->get(); // Ambil 10 data pertama
        }

        return response()->json($results);
    }

    public function get_role(Request $request)
    {
        try {
            $role = $request->input('role');
            $user = Auth::user();
            $query = User::select("name", "username", "position_title", "job_level", "companies.company_name as co_name")
                ->join('companies', 'companies.company_code', '=', 'users.company_code');

            if ($role === "Superadmin" || $role === "Admin") {
                $query = $query->where('users.role', 'LIKE', "$role");
            } else {
                $query = $query->where('users.role', 'LIKE', "%$role%");
            }

            // Restrict to same company if the user is an admin
            if ($user->role === 'Admin') { //note: superadmin masih bug karena cuma tampil nama dia saja
                $query->where('users.company_code', $user->company_code);
            }

            $data_row = $query->get();
            $dataTable = DataTables::of($data_row);

            return $dataTable->addIndexColumn()->toJson();
        } catch (Exception $e) {
            // dd($e->getMessage());
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        // Lakukan pencarian berdasarkan query dan kembalikan hasil sebagai JSON
        $results = User::where('name', 'LIKE', "%$query%")
            ->orWhere('email', 'LIKE', "%$query%")
            ->limit(1)
            ->get();

        return redirect()->route('profile.index')->with([
            'data_query' => $results,
            'query' => $query
        ]);
    }
    public function get_fasilitator(Request $request)
    {
        $query = $request->input('query');
        $results = User::with('company')
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%$query%")
                    ->orWhere('email', 'LIKE', "%$query%");
            })
            ->whereIn('job_level', ["Band 2", "Band 1", "Band 3"])
            ->select('employee_id', 'name', 'company_name', 'job_level')
            ->limit(10)
            ->get();

        return response()->json($results);
    }

    public function getJudges(Request $request)
    {
        $searchTerm = $request->input('query');
        $eventId = $request->input('event_id');

        $judges = DB::table('judges')
            ->join('users', 'users.employee_id', '=', 'judges.employee_id')
            ->where('judges.status', 'active')
            ->where('judges.event_id', $eventId)
            ->where(function ($query) use ($searchTerm) {
                $query->where('users.name', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('users.employee_id', 'LIKE', '%' . $searchTerm . '%');
            })
            ->select(
                'judges.id as judges_id',
                'users.employee_id as employee_id',
                'users.name as name'
            )
            ->limit(100)
            ->get();

        return response()->json($judges);
    }

    public function get_GM(Request $request)
    {
        // $unit = $request->input('unit');
        $query = $request->input('query');
        $results = User::where('name', 'LIKE', "%$query%")
            ->where('email', 'LIKE', "%$query%")
            ->whereIn('job_level', ["Band 1"])
            ->join('companies', 'companies.company_code', '=', 'users.company_code')
            ->select('employee_id', 'name', 'companies.company_name', 'job_level')
            ->limit(10)
            ->get();


        return response()->json($results);
    }
    public function get_BOD(Request $request)
    {
        // $unit = $request->input('unit');
        $query = $request->input('query');
        $results = User::where('name', 'LIKE', "%$query%")
            ->where('email', 'LIKE', "%$query%")
            ->whereIn('role', ["BOD"])
            ->join('companies', 'companies.company_code', '=', 'users.company_code')
            ->select('employee_id', 'name', 'companies.company_name', 'job_level')
            ->limit(10)
            ->get();


        return response()->json($results);
    }

    public function custom_get(Request $request)
    {

        try {
            $table = $request->input('table');
            $limit = $request->input('limit');

            if ($request->input('join') !== null) {
                $join = $request->input('join');
            }

            if ($request->input('where') !== null) {
                $where = $request->input('where');
            }

            if ($request->input('select') !== null) {
                $select = $request->input('select');
            }


            if (empty($table) || empty($limit)) {
                return response()->json([
                    // 'where' => $where,
                    'table' => $table,
                    'limit' => $limit,
                    'message' => "pastikan semua parameter terpenuhi"
                ], 400);
            }

            $query = DB::table($table);

            if (isset($join)) {
                foreach ($join as $table => $column) {
                    $query->join($table, function ($join) use ($column) {

                        foreach ($column as $column1 => $column2) {
                            $join->on($column1, '=', $column2);
                        }
                    });
                }
            }

            if (isset($where)) {
                foreach ($where as $column => $value) {
                    if (gettype($value) == "array")
                        $query->whereIn($column, $value);
                    else
                        $query->where($column, 'LIKE', '%' . $value . '%');
                }
            }

            if (isset($select)) {
                $query->select($select);
            }

            $result = $query->limit($limit)
                ->get();

            if ($result->isEmpty()) {
                return response()->json([
                    'message' => "data kosong"
                ], 400);
            }

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function custom_getAssesment(Request $request)
    {
        try {
            $table = $request->input('table');
            $limit = $request->input('limit');

            if ($request->input('join') !== null) {
                $join = $request->input('join');
            }

            if ($request->input('where') !== null) {
                $where = $request->input('where');
            }

            if ($request->input('select') !== null) {
                $select = $request->input('select');
            }

            if (!$table || !$limit) {
                return response()->json([
                    'table' => $table,
                    'limit' => $limit,
                    'message' => "pastikan semua parameter terpenuhi"
                ], 400);
            }

            $query = DB::table($table);

            if (isset($join)) {
                foreach ($join as $table => $column) {
                    $query->join($table, function ($join) use ($column) {

                        foreach ($column as $column1 => $column2) {
                            $join->on($column1, '=', DB::raw("ANY(string_to_array(" . $column2 . ", ','))"));
                        }
                    });
                }
            }

            if (isset($where)) {
                foreach ($where as $column => $value) {
                    if (gettype($value) == "array")
                        $query->whereIn($column, $value);
                    else
                        // $query->where($column, 'LIKE', '%'.$value.'%');
                        $query->where($column, '=', $value);
                }
            }

            if (isset($select)) {
                $query->select($select);
            }

            $result = $query->limit($limit)
                ->get();

            if ($result->isEmpty()) {
                return response()->json([
                    'message' => "data kosong"
                ], 400);
            }

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function getCompanyByEventId(Request $request)
    {
        try {
            $eventId = $request->input('eventId');

            // Ensure event_id is provided
            if (!$eventId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event ID is required'
                ], 400);
            }

            $event = Event::with(['companies' => function($query) {
                $query->select('companies.id', 'companies.company_name', 'companies.company_code');
            }])->find($eventId);

            // Check if event exists
            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event not found'
                ], 404);
            }

            $response = $event->companies->map(function($company) use ($event) {
                return [
                    'company_name' => $company->company_name,
                    'company_code' => $company->company_code,
                    'event_name' => $event->event_name,
                    'year' => $event->year
                ];
            });

            return response()->json([
                'success' => true,
                'data_event' => $response
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function get_data_makalah(Request $request)
    {
        try {
            $query_data = Paper::join('teams', 'papers.team_id', '=', 'teams.id')
                ->join('categories', 'teams.category_id', '=', 'categories.id')
                ->join('themes', 'teams.theme_id', '=',  'themes.id')
                ->join('companies', 'companies.company_code', '=', 'teams.company_code')
                ->join('metodologi_papers', 'papers.metodologi_paper_id', '=', 'metodologi_papers.id')
                ->orderBy('papers.created_at', 'desc');

            $select = [
                'papers.id as paper_id',
                'teams.id as team_id',
                'teams.gm_id as gm_id',
                'innovation_title',
                'team_name',
                'company_name',
                'theme_name',
                'category_name',
                'financial',
                'file_review',
                'potential_benefit',
                'papers.status',
                'papers.metodologi_paper_id',
                'papers.created_at',
                'papers.status_rollback',
                'metodologi_papers.name as metodologi_makalah', // Select metodologi makalah
                DB::raw("CASE
                            WHEN SUBSTRING(step_1, 1, 1) = 'w' THEN 3
                            WHEN SUBSTRING(step_1, 1, 1) = 'f' THEN 2
                            WHEN SUBSTRING(step_1, 1, 1) = '-' THEN 4
                            WHEN step_1 is NOT NULL THEN 1
                        ELSE 0 END AS step_1"),
                DB::raw("CASE
                            WHEN SUBSTRING(step_2, 1, 1) = 'w' THEN 3
                            WHEN SUBSTRING(step_2, 1, 1) = 'f' THEN 2
                            WHEN SUBSTRING(step_2, 1, 1) = '-' THEN 4
                            WHEN step_2 is NOT NULL THEN 1
                        ELSE 0 END AS step_2"),
                DB::raw("CASE
                            WHEN SUBSTRING(step_3, 1, 1) = 'w' THEN 3
                            WHEN SUBSTRING(step_3, 1, 1) = 'f' THEN 2
                            WHEN SUBSTRING(step_3, 1, 1) = '-' THEN 4
                            WHEN step_3 is NOT NULL THEN 1
                        ELSE 0 END AS step_3"),
                DB::raw("CASE
                            WHEN SUBSTRING(step_4, 1, 1) = 'w' THEN 3
                            WHEN SUBSTRING(step_4, 1, 1) = 'f' THEN 2
                            WHEN SUBSTRING(step_4, 1, 1) = '-' THEN 4
                            WHEN step_4 is NOT NULL THEN 1
                        ELSE 0 END AS step_4"),
                DB::raw("CASE
                            WHEN SUBSTRING(step_5, 1, 1) = 'w' THEN 3
                            WHEN SUBSTRING(step_5, 1, 1) = 'f' THEN 2
                            WHEN SUBSTRING(step_5, 1, 1) = '-' THEN 4
                            WHEN step_5 is NOT NULL THEN 1
                        ELSE 0 END AS step_5"),
                DB::raw("CASE
                            WHEN SUBSTRING(step_6, 1, 1) = 'w' THEN 3
                            WHEN SUBSTRING(step_6, 1, 1) = 'f' THEN 2
                            WHEN SUBSTRING(step_6, 1, 1) = '-' THEN 4
                            WHEN step_6 is NOT NULL THEN 1
                        ELSE 0 END AS step_6"),
                DB::raw("CASE
                            WHEN SUBSTRING(step_7, 1, 1) = 'w' THEN 3
                            WHEN SUBSTRING(step_7, 1, 1) = 'f' THEN 2
                            WHEN SUBSTRING(step_7, 1, 1) = '-' THEN 4
                            WHEN step_7 is NOT NULL THEN 1
                        ELSE 0 END AS step_7"),
                DB::raw("CASE
                            WHEN SUBSTRING(step_8, 1, 1) = 'w' THEN 3
                            WHEN SUBSTRING(step_8, 1, 1) = 'f' THEN 2
                            WHEN SUBSTRING(step_8, 1, 1) = '-' THEN 4
                            WHEN step_8 is NOT NULL THEN 1
                        ELSE 0 END AS step_8"),
                DB::raw("CASE
                            WHEN SUBSTRING(full_paper, 1, 1) = 'w' THEN 3
                            WHEN SUBSTRING(full_paper, 1, 1) = 'f' THEN 2
                            WHEN full_paper is NOT NULL THEN 1
                        ELSE 0 END AS full_paper"),

                'papers.step_1 as step_1_initial',
                'papers.step_2 as step_2_initial',
                'papers.step_3 as step_3_initial',
                'papers.step_4 as step_4_initial',
                'papers.step_5 as step_5_initial',
                'papers.step_6 as step__initial',
                'papers.step_2 as step_2_initial',
                'papers.step_8 as step_8_initial',
                'papers.status as paper_status',

            ];
            if(Auth::user()->role == 'Superadmin') {
                $query_data->distinct();
            } elseif ($request->filterRole == 'admin') {
                // Gabungkan tabel pvt_members untuk memeriksa keikutsertaan admin sebagai member
                $query_data->leftJoin('pvt_members', function ($join) use ($request) {
                    $join->on('teams.id', '=', 'pvt_members.team_id')
                        ->where('pvt_members.employee_id', Auth::user()->employee_id);
                });

                // Filter berdasarkan company_code kecuali admin adalah peserta/member dengan status tertentu
                $query_data->where(function ($query) use ($request) {
                    $query->where(function ($query) {
                            $query->whereNotNull('pvt_members.id') // Admin adalah member
                                ->whereIn('pvt_members.status', ['leader', 'member', 'facilitator', 'gm']); // Status tertentu
                        });
                });

                // Tambahkan informasi status member
                $select[] = DB::raw("COALESCE(pvt_members.status, 'not_member') as member_status");

                // Hindari duplikasi dengan DISTINCT atau GROUP BY
                $query_data->distinct();
            } else {
                // Untuk role lainnya, langsung filter berdasarkan membership
                $query_data->join('pvt_members', 'teams.id', '=', 'pvt_members.team_id')
                    ->where('pvt_members.employee_id', Auth::user()->employee_id);
                $select[] = 'pvt_members.status as member_status';
            }

            // Filter untuk company_code (mendukung single dan multiple select)
            if ($request->has('filterCompany') && !empty($request->filterCompany)) {
                $filterCompany = is_array($request->filterCompany)
                    ? $request->filterCompany
                    : [$request->filterCompany]; // ubah jadi array kalau single string

                $query_data->whereIn('teams.company_code', $filterCompany);
            }

            //filter untuk status inovasi
            if ($request->has('status_inovasi') && $request->status_inovasi != '') {
                $query_data->where('papers.status_inovasi', $request->status_inovasi);
            }

            //filter for category
            if ($request->has('filter_category') && $request->filter_category != '') {
                $query_data->where('teams.category_id', $request->filter_category);
            }

            $data_row = $query_data->select($select)->get();

            $dataTable = DataTables::of($data_row);
            $ownerCache = [];
            $currentUserId = Auth::user()->employee_id;
            $isSuperadmin = Auth::user()->role === 'Superadmin';
            $isAdmin = Auth::user()->role === 'Admin';

            $rawColumns = ['detail_team'];
            $dataTable->addColumn('detail_team', function ($data_row) {
                return '
                <div class="d-flex justify-content-center flex-column gap-1">
                    <button class="btn btn-dark btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#detailTeamMember" onclick="get_data_on_modal(' . $data_row->team_id . ')" >Detail</button>
                    <button class="btn btn-primary btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#coachingClinic" onclick="prepare_modal_coaching('. $data_row->team_id .')">Coaching</button>
                </div>
                ';
            });

            //     // Add metodologi makalah column
            // $rawColumns[] = 'metodologi_makalah';
            // $dataTable->addColumn('metodologi_makalah', function ($data_row) {
            //     return $data_row->metodologi_makalah;
            // });

            //button untuk full paper
            $rawColumns[] = 'full_paper';
            $dataTable->addColumn('full_paper', function ($data_row) use ($currentUserId, &$ownerCache, $isSuperadmin, $isAdmin) {
                if (!isset($ownerCache[$data_row->paper_id])) {
                    $ownerCache[$data_row->paper_id] = Paper::where('id', $data_row->paper_id)
                        ->whereHas('team.pvtMembers', function ($query) use ($currentUserId) {
                            $query->where('employee_id', $currentUserId)
                                ->whereIn('status', ['leader', 'member']);
                        })
                        ->exists();
                }

                $isOwner = $ownerCache[$data_row->paper_id];
                $html = '';
                if ($data_row->full_paper != null) {
                    $html .= "
                            <a class=\"btn btn-info btn-sm mb-2\" href=\"" . route('paper.show.stages', ['id' => $data_row->paper_id, 'stage' => 'full']) . " \" target=\"_blank\">Detail</a>
                            ";
                    $maxStep = MetodologiPaper::findOrFail($data_row->metodologi_paper_id)->step;
                    $allStepsCompleted = true; // Asumsi semua langkah terisi

                    for ($i = 1; $i <= $maxStep; $i++) {
                        $stepField = "step_$i";

                        // Jika ada langkah yang masih null atau bernilai '-', maka tidak selesai
                        if ($data_row->$stepField === null || $data_row->$stepField === '-') {
                            $allStepsCompleted = false;
                            break;
                        }
                    }

                    if ($allStepsCompleted && ($data_row->status === 'not finish' || $data_row->status === 'revision paper by facilitator' || $data_row->status === 'revision paper by general manager' || $data_row->status === 'revision paper and benefit by general manager' || $data_row->status === 'revision paper and benefit by innovation admin' || $data_row->status === 'revision paper by innovation admin')) {
                        if ($isOwner || $isSuperadmin) {
                            $html .= "
                                <button class=\"btn btn-success btn-sm\" data-bs-toggle=\"modal\" data-bs-target=\"#fixationModal\" data-paper-id=\"{$data_row->paper_id}\" >Fiksasi Makalah</button>
                            ";
                        } else {
                            $html .= "
                                <button class=\"btn btn-warning btn-sm\" disabled >Makalah belum fix</button>
                            ";
                        }
                    }

                    if($data_row->status === 'accepted by innovation admin'){
                        $html .= '<button class="btn btn-warning btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#uploadStep" onclick="change_url_step(' . $data_row->paper_id . ', \'uploadStepForm\' ' . ', \'full_paper\' )">Update Paper</button>';
                    }

                    if ($data_row->status_rollback == 'rollback paper') {
                        $html .= '<button class="btn btn-purple btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#uploadStep" onclick="change_url_step(' . $data_row->paper_id . ', \'uploadStepForm\' ' . ', \'full_paper\' )" >Upload Full Paper</button>';
                    }
                } else {
                    if ($isOwner || $isSuperadmin)
                        $html .= '<button class="btn btn-purple btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#uploadStep" onclick="change_url_step(' . $data_row->paper_id . ', \'uploadStepForm\' ' . ', \'full_paper\' )" >Upload Full Paper</button>';
                }
                return $html;
            });


            //link benefit
            $rawColumns[] = 'benefit';
            $dataTable->addColumn('benefit', function ($data_row) {
                if ($data_row->financial != null || $data_row->file_review != null || $data_row->potential_benefit != null) {
                    if ($data_row->member_status === "leader" || $data_row->member_status === "member") {
                        return "<a class=\"btn btn-dark btn-sm\" href=\" " . route('benefit.create.user', ['id' => $data_row->paper_id]) . "\">Update</a>";
                    } else {
                        return "<a class=\"btn btn-primary btn-sm\" href=\" " . route('benefit.create.user', ['id' => $data_row->paper_id]) . "\">Lihat</a>";
                    }
                } else {
                    if ($data_row->member_status === "leader" || $data_row->member_status === "member") {
                        if ($data_row->status === "not finish" || $data_row->status === 'revision paper by facilitator' || $data_row->status === "upload full paper" || $data_row->status === 'revision paper by general manager' || $data_row->status === 'revision paper and benefit by general manager' || $data_row->status === 'revision paper and benefit by innovation admin' || $data_row->status === 'revision benefit by innovation admin') {
                            return "<a class=\"btn btn-outline-dark btn-sm btn-\" href=\"#\"  >Add</a>";
                        } else {
                            return "<a class=\"btn btn-dark btn-sm\" href=\" " . route('benefit.create.user', ['id' => $data_row->paper_id]) . "\">Add</a>";
                        }
                    } else {
                        return "<a class=\"btn btn-primary btn-sm\" href=\" " . route('benefit.create.user', ['id' => $data_row->paper_id]) . "\">Lihat</a>";
                    }
                }
            });

            $rawColumns[] = 'approval';
            $dataTable->addColumn('approval', function ($data_row) {
                $html = '';
                if ($data_row['paper_status'] == 'upload full paper' && $data_row->member_status == "facilitator") {
                    $html .= '<button class="btn btn-primary btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#accFasilitator" onclick="approve_paper_fasil_modal(' . $data_row->paper_id . ' )">Approval Fasil</button>';
                } elseif ($data_row['paper_status'] == 'upload benefit' && $data_row->member_status == "facilitator") {
                    $html .= '<button class="btn btn-primary btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#accFasilitatorBnefit" onclick="approve_benefit_fasil_modal(' . $data_row->paper_id . ' )">Approval Fasil</button>';
                } elseif ($data_row['paper_status'] == 'accepted benefit by facilitator' && $data_row->member_status == "gm") {
                    $html .= '<button class="btn btn-primary btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#accGM" onclick="approve_benefit_gm_modal(' . $data_row->paper_id . ' )">Approval General Manager</button>';
                } elseif ($data_row['paper_status'] == 'accepted benefit by general manager' && (Auth::user()->role == 'Admin' || Auth::user()->role == 'Superadmin')) {
                    $html .=  '<button class="btn btn-cyan btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#accAdmin" onclick="approve_admin_modal(' . $data_row->paper_id . ', ' . $data_row->team_id . ')">Approval Admin</button>';
                } else {
                    $html .= '-';
                }

                return $html;
            });

            $rawColumns[] = 'Dokumen';
            $dataTable->addColumn('Dokumen', function ($data_row) {
                $teamId = $data_row->team_id;
                $employeeId = Auth::user()->employee_id;
                $statusProp = PvtMember::where('team_id', $teamId)->where('employee_id', $employeeId)->select('status')->first();
                if ($statusProp === null) {
                    return '<div class="d-flex">
                    <button class="btn btn-outline-primary btn-sm next-2" type="button" data-bs-toggle="modal" data-bs-target="#showDocument" onclick="show_document_modal(' . $data_row->paper_id . ')" ><i class="fa fa-eye"></i>&nbsp</button>
                </div>';
                } else {

                    if ($statusProp->status === "leader" || $statusProp->status === "member") {
                        return '<div class="d-flex">
                                    <button class="btn btn-primary btn-sm next-2" type="button" data-bs-toggle="modal" data-bs-target="#uploadDocument" onclick="upload_document_modal(' . $data_row->paper_id . ')" ><i class="fa fa-upload"></i></button><br>
                                    <button class="btn btn-outline-primary btn-sm next-2" type="button" data-bs-toggle="modal" data-bs-target="#showDocument" onclick="show_document_modal(' . $data_row->paper_id . ')" ><i class="fa fa-eye"></i>&nbsp</button>
                                    </div>';
                    } else {

                        return '<div class="d-flex">
                                    <button class="btn btn-outline-primary btn-sm next-2" type="button" data-bs-toggle="modal" data-bs-target="#showDocument" onclick="show_document_modal(' . $data_row->paper_id . ')" ><i class="fa fa-eye"></i>&nbsp</button>
                                </div>';
                    }
                }
            });
            // Log::debug($data_row->)
            //modal action
            $rawColumns[] = 'status';
            $dataTable->addColumn('status', function ($data_row) {
                $html = '';

                if ($data_row->status == "not finish") {
                    $html .=  '<div class="badge bg-yellow">Belum Lengkap</div>';
                } elseif ($data_row->status == 'upload full paper') {
                    $html .=  '<div class="badge bg-yellow">Menunggu Makalah Disetujui Oleh Fasilitator</div>';
                } elseif ($data_row->status == 'upload benefit') {
                    $html .=  '<div class="badge bg-yellow">Menunggu Benefit Disetujui Oleh Fasilitator</div>';
                } elseif ($data_row->status == 'accepted paper by facilitator') {
                    $html .= '<button class="btn btn-success btn-xs" type="button" title="Acc Fasilitator" data-bs-target="#commentModal" onclick="get_comment(' . $data_row->paper_id . ', \'facilitator\')"><i >Makalah Disetujui Oleh Fasilitator, Silahkan Upload Benefit</i></button>';
                } elseif ($data_row->status == 'revision paper by facilitator') {
                    $html .= '<button class="btn bg-pink text-white btn-xs" type="button" title="Revisi Fasilitator" data-bs-target="#commentModal" onclick="get_comment(' . $data_row->paper_id . ', \'facilitator\')"><i >Makalah terdapat revisi dari Fasilitator</i></button>';
                } elseif ($data_row->status == 'rejected paper by facilitator') {
                    $html .=  '<button class="btn btn-danger btn-xs" type="button" title="Reject Fasilitator" data-bs-target="#commentModal" onclick="get_comment(' . $data_row->paper_id . ', \'innovation admin\')"><i >Makalah tidak disetujui oleh Fasilitator</i></button>';
                } elseif ($data_row->status == 'accepted benefit by facilitator') {
                    $html .= '<button class="btn btn-success btn-xs" type="button" title="Acc Fasilitator"><i >Benefit Disetujui Oleh Fasilitator</i></button>';
                } elseif ($data_row->status == 'rejected benefit by facilitator') {
                    $html .= '<button class="btn btn-danger btn-xs" type="button" title="Reject Fasilitator"><i >Benefit tidak Disetujui Oleh Fasilitator</i></button>';
                } elseif ($data_row->status == 'revision benefit by facilitator') {
                    $html .= '<button class="btn bg-pink text-white btn-xs" type="button" title="Reject Fasilitator"><i >Benefit mendapatkan revisi dari Fasilitator</i></button>';
                } elseif ($data_row->status == 'accepted benefit by general manager') {
                    $html .= '<button class="btn btn-success btn-xs" type="button" title="Acc GM"><i >Benefit Disetujui Oleh General Manager</i></button>';
                } elseif ($data_row->status == 'rejected benefit by general manager') {
                    $html .= '<button class="btn btn-danger btn-xs" type="button" title="Reject GM"><i >Benefit tidak Disetujui Oleh General Manager</i></button>';
                } elseif ($data_row->status == 'revision benefit by general manager') {
                    $html .= '<button class="btn btn-pink btn-xs" type="button" title="Reject GM"><i >Benefit mendapatkan revisi dari General Manager</i></button>';
                } elseif ($data_row->status == 'revision paper by general manager') {
                    $html .= '<button class="btn btn-pink btn-xs" type="button" title="Reject GM"><i >Makalah mendapatkan revisi dari General Manager</i></button>';
                } elseif ($data_row->status == 'revision paper and benefit by general manager') {
                    $html .= '<button class="btn btn-pink btn-xs" type="button" title="Reject GM"><i >Makalah dan Benefit mendapatkan revisi dari General Manager</i></button>';
                } elseif ($data_row->status == 'accepted by innovation admin') {
                    $html .= '<button class="btn btn-success btn-xs" type="button" title="Acc Admin"><i >Disetujui Oleh  Admin, lanjut ikuti event</i></button>';
                } elseif ($data_row->status == 'revision benefit by innovation admin') {
                    $html .= '<button class="btn btn-danger btn-xs" type="button" title="Reject Admin"><i >Makalah tidak Disetujui Oleh Admin</i></button>';
                } elseif ($data_row->status == 'revision paper and benefit by innovation admin') {
                    $html .= '<button class="btn btn-danger btn-xs" type="button" title="Reject Admin"><i >Makalah dan benefit mendapatkan revisi dari Admin</i></button>';
                } elseif ($data_row->status == 'revision paper by innovation admin') {
                    $html .= '<button class="btn btn-danger btn-xs" type="button" title="Reject Admin"><i >Makalah mendapatkan revisi dari Admin</i></button>';
                } elseif ($data_row->status == 'rejected by innovation admin') {
                    $html .= '<button class="btn btn-danger btn-xs" type="button" title="Reject Admin"><i >Tidak Disetujui oleh Admin</i></button>';
                } elseif ($data_row->status == "replicate") {
                    $html .= '<button class="btn btn-danger btn-xs" type="button" title="Replicate" <i >Replicate</i></button>';
                } elseif ($data_row->status == "not complete") {
                    $html .= '<button class="btn btn-default btn-xs" type="button" title="Not Complete" <i >Not Complete</i></button>';
                } elseif ($data_row->status == "rollback paper") {
                    $html .= '<button class="btn btn-default btn-xs" type="button" title="Rollback Paper" <i >Rollback Paper</i></button>';
                } elseif ($data_row->status == "rollback benefit") {
                    $html .= '<button class="btn btn-default btn-xs" type="button" title="Rollback Benefit" <i >Rollback Benefit</i></button>';
                }


                $html .=  '<button class="btn btn-secondary text-white btn-sm m-2" type="button" data-bs-toggle="modal" title="Lihat Komentar" data-bs-target="#commentModal" onclick="get_comment(' . $data_row->paper_id . ', \'innovation admin\')"><i >Lihat Komentar</i></button>';
                return $html . '<button class="btn btn-info btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#infoHistory" onclick="get_data_modal_history(' . $data_row->team_id . ')">History</button>';
            });
            $rawColumns[] = 'action';
            $dataTable->addColumn('action', function ($data_row) {
                if ($data_row->status === "accepted benefit by general manager") {
                    if (auth()->user()->role === "Admin" || auth()->user()->role === "Superadmin") {
                        return '<div class="d-flex flex-column gap-1">
                                    <button class="btn btn-warning btn-sm next" type="button" data-bs-toggle="modal" data-bs-target="#updateData" onclick="get_data_modal_update(' . $data_row->team_id . ')">Update</button>
                                    <button class="btn btn-danger btn-sm next" type="button" data-bs-toggle="modal" data-bs-target="#rollback" onclick="change_url(' . $data_row->paper_id . ', \'formRollback\' )">Rollback</button>
                                </div>';
                    }
                } else {
                    return '<button class="btn btn-warning btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#updateData" onclick="get_data_modal_update(' . $data_row->team_id . ')">Update</button>';
                }
            });

            $jumlah_step = 8;
            for ($i = 1; $i <= $jumlah_step; $i++) {
                $dataTable->addColumn('step_' . $i, function ($data_row) use ($i, &$ownerCache, $currentUserId) {
                    $html = '';
                    if (!isset($ownerCache[$data_row->paper_id])) {
                        $ownerCache[$data_row->paper_id] = Paper::where('id', $data_row->paper_id)
                            ->whereHas('team.pvtMembers', function ($query) use ($currentUserId) {
                                $query->where('employee_id', $currentUserId)
                                    ->whereIn('status', ['leader', 'member']);
                            })
                            ->exists();
                    }

                    $isOwner = $ownerCache[$data_row->paper_id];

                    if ($data_row->{"step_" . $i} == 0) {
                        $html .= "<a class=\"btn btn-primary btn-xs\" href=\" " . route('paper.create.stages', ['id' => $data_row->paper_id, 'stage' => 'stage_' . $i]) . "\">Add</a>";
                        $html .= '<button class="btn btn-purple btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#uploadStep" onclick="change_url_step(' . $data_row->paper_id . ', \'uploadStepForm\' ' . ', \'step_' . $i . '\' )" >Upload</button>';
                    } else {
                        if ($data_row->status == "not finish" || $data_row->status_rollback == 'rollback paper') {
                            if ($data_row->{"step_" . $i} == 2)
                                $html .= '<button class="btn btn-purple btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#uploadStep" onclick="change_url_step(' . $data_row->paper_id . ', \'uploadStepForm\' ' . ', \'step_' . $i . '\' )" >Upload</button>';
                            elseif (($data_row->{"step_" . $i} == 3 || $data_row->{"step_" . $i} == 1) && $isOwner) {
                                $html .= "<a class=\"btn btn-warning btn-xs\" href=\"" . route('paper.create.stages', ['id' => $data_row->paper_id, 'stage' => 'stage_' . $i]) . " \">Edit</a>";
                            }
                        }
                        $metodologiPaper = MetodologiPaper::findOrFail($data_row->metodologi_paper_id);
                        $metodologiPaperStep = $metodologiPaper->step;
                        if ($metodologiPaperStep === 7 && $i === 8) {
                            $html .= "-";
                        } else {
                            $html .= "<a class=\"btn btn-info btn-xs\" href=\"" . route('paper.show.stages', ['id' => $data_row->paper_id, 'stage' => 'step_' . $i]) . " \" target=\"_blank\">Detail</a>";
                        }
                    }

                    return $html;
                });

                $rawColumns[] = 'step_' . $i;
            }


            $dataTable->rawColumns($rawColumns);
            return $dataTable->addIndexColumn()->toJson();
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function get_data_member(Request $request)
    {
        try {
            $dataPaper = Paper::join('teams', 'papers.team_id', '=', 'teams.id')
                ->where('papers.team_id', $request->team_id)
                ->select(
            'papers.innovation_title',
                    'papers.inovasi_lokasi',
                    'papers.abstract',
                    'papers.problem',
                    'papers.main_cause',
                    'papers.solution',
                    'papers.innovation_photo',
                    'papers.proof_idea',
                    'teams.team_name',
                    'papers.team_id',
                    'papers.id as paper_id',
                )
                ->get();

            $data_anggotas = PvtMember::with('team')
                            ->where('team_id', $request->team_id)
                            ->get();

            $data_karyawan = [];
            foreach ($data_anggotas as $data_anggota) {
                $data_user = User::where('employee_id', $data_anggota->employee_id)->first();

                // Check if the status already exists in the array
                if (!isset($data_karyawan[$data_anggota['status']])) {
                    if ($data_anggota['status'] == 'member') {
                        // Initialize as an array for members
                        $data_karyawan[$data_anggota['status']] = [];
                    } else {
                        // Initialize as a single User instance for other statuses
                        $data_karyawan[$data_anggota['status']] = $data_user;
                    }
                }

                // For members, push the User into the array
                if ($data_anggota['status'] == 'member') {
                    array_push($data_karyawan[$data_anggota['status']], $data_user);
                }
            }


            $data_ph2_members = ph2Member::where('team_id', $request->team_id)->get();

            if ($data_ph2_members->count()) {
                $data_karyawan['outsource'] = [];
                foreach ($data_ph2_members as $data_ph2) {
                    array_push($data_karyawan['outsource'], $data_ph2);
                }
            }

            return response()->json([
                'data' => $data_karyawan,
                'paper' => $dataPaper,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }


    public function add_benefit(Request $request)
    {
        try {

            $benefit_custom = CustomBenefitFinancial::create([
                'name_benefit' => $request->name_benefit,
            ]);

            return response()->json([
                'success' => "success",
                'data' => $benefit_custom
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function update_benefit(Request $request)
    {
        try {
            $benefit_custom = CustomBenefitFinancial::where('id', $request->id)->update([
                'name_benefit' => $request->name_benefit,
            ]);

            return response()->json([
                'success' => "success",
                'data' => $benefit_custom
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }
    public function delete_benefit(Request $request)
    {
        try {
            // Cari data berdasarkan ID
            $benefit_custom = CustomBenefitFinancial::where('id', $request->id)->first();

            if (!$benefit_custom) {
                return response()->json([
                    'error' => 'Data not found',
                ], 404);
            }

            // Perbarui kolom is_deleted menjadi true
            $benefit_custom->is_deleted = true;
            $benefit_custom->save();

            return response()->json([
                'success' => 'Data marked as deleted',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function get_berita_acara(Request $request)
    {
        try {
            // Ambil data pengguna yang sedang login
            $currentUser = auth()->user();
            $companyNameUser = $currentUser->company_name;

            // Cek apakah pengguna adalah Superadmin
            if ($currentUser->role === "Superadmin") {
                // Superadmin dapat melihat semua data tanpa filter perusahaan
                $data_row = BeritaAcara::select(
                    "berita_acaras.id",
                    "berita_acaras.event_id",
                    "events.event_name",
                    "berita_acaras.no_surat",
                    "berita_acaras.jenis_event",
                    "berita_acaras.penetapan_juara",
                    "berita_acaras.signed_file"
                )->join('events', 'berita_acaras.event_id', '=', 'events.id');
            } else {
                // Untuk Admin atau User, filter berdasarkan perusahaan
                $getCompanyCode = Company::where('company_name', $companyNameUser)->select('id')->first();
                $currentCompanyId = $getCompanyCode->id;

                $data_row = BeritaAcara::select(
                    "berita_acaras.id",
                    "berita_acaras.event_id",
                    "events.event_name",
                    "berita_acaras.no_surat",
                    "berita_acaras.jenis_event",
                    "berita_acaras.penetapan_juara",
                    "berita_acaras.signed_file"
                )->join('events', 'berita_acaras.event_id', '=', 'events.id')
                    ->join('company_event', 'events.id', '=', 'company_event.event_id')
                    ->where('company_event.company_id', $currentCompanyId);
            }

            $dataTable = DataTables::of($data_row->get());

            $rawColumns = ['upload', 'delete', 'view']; // Tambahkan 'view' ke rawColumns

            $dataTable->addColumn('upload', function ($data_row) use ($currentUser) {
                // Cek apakah file sudah diunggah
                if ($data_row['signed_file']) {
                    // Jika file sudah ada
                    if ($currentUser->role === "Admin" || $currentUser->role === "Superadmin") {
                        return '<button class="btn btn-warning btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#upload" onclick="modal_update_beritaacara(' . $data_row['id'] . ')">
                                    <i class="fa fa-edit"></i></button>';
                    }
                } else {
                    // Jika file belum ada
                    if ($currentUser->role === "Admin" || $currentUser->role === "Superadmin") {
                        return '<button class="btn btn-indigo btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#upload" onclick="modal_update_beritaacara(' . $data_row['id'] . ')">
                                    <i class="fa fa-upload"></i></button>';
                    }
                }
                // User biasa tidak memiliki tombol
                return '';
            });

            // Tambahkan kolom hapus jika signed_file sudah ada
            $dataTable->addColumn('delete', function ($data_row) use ($currentUser) {
                if ($data_row['signed_file'] && ($currentUser->role === "Admin" || $currentUser->role === "Superadmin")) { //periksa apakah pengguna admin & superadmin
                    // Tampilkan tombol hapus jika file sudah diunggah
                    return '<form action="' . route('dokumentasi.berita-acara.delete', ['id' => $data_row['id']]) . '" method="POST" onsubmit="return confirm(\'Yakin ingin menghapus file ini?\');">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                        </form>';
                }
                return ''; // Jika tidak ada file, tidak ada tombol hapus
            });

            // Tambahkan kolom view untuk melihat file
            $dataTable->addColumn('view', function ($data_row) {
                if ($data_row['signed_file']) {
                    // Tampilkan tombol lihat jika file sudah diunggah
                    return '<a href="' . route('berita-acara.viewUploadedPDF', ['path' => rawurlencode($data_row['signed_file'])]) . '" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></a>';
                }
                return '<a href="' . route('berita-acara.showPDF', ['id' => $data_row['id']]) . '" class="btn btn-info btn-sm" target="_blank">Tampilkan</a>'; // Jika tidak ada file, tidak ada tombol lihat
            });

            $dataTable->rawColumns($rawColumns);

            // Menambahkan event_name ke dalam DataTable
            $dataTable->addColumn('event_name', function ($data_row) {
                return $data_row['event_name'];
            });

            return $dataTable->addIndexColumn()->toJson();
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }





    public function get_data_template_assessment(Request $request)
    {
        try {
            $data_row = TemplateAssessmentPoint::select("id", "point", "detail_point", "pdca", "score_max", "stage")
                ->where("category", $request->filterCategory)
                ->orderByRaw("CASE
                            WHEN pdca = 'Plan' THEN 1
                            WHEN pdca = 'Do' THEN 2
                            WHEN pdca = 'Check' THEN 3
                            WHEN pdca = 'Action' THEN 4
                            ELSE 5
                        END, id ASC");

            // dd($data_row);
            $dataTable = DataTables::of($data_row);

            $rawColumns = ['assign'];
            $dataTable->addColumn('assign', function ($data_row) {
                return '<input class="form-check-input" id="flexCheckChecked" type="checkbox" name="assessment_poin_id[]" data-score-max="' . $data_row->score_max . '" value="' . $data_row->id . '" onclick="cek()">';
            });

            $rawColumns[] = 'action';
            $dataTable->addColumn('action', function ($data_row) {
                if (auth()->user()->role == 'Superadmin') {
                    return '<button class="btn btn-warning btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#updateTemplate" onclick="get_data_template(' . $data_row->id . ')">Update</button>
                            <button class="btn btn-danger btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#deleteTemplate" onclick="delete_template(' . $data_row->id . ')">Delete</button>';
                }
            });

            $rawColumns[] = 'detail_point';
            $dataTable->addColumn('detail_point', function ($data_row) {
                return '<textarea class="form-control"  id="textarea" name="detail_assesment_' . $data_row->id . '" rows="10" cols="40" type="text" readonly> ' . $data_row->detail_point . ' </textarea>';
            });
            $dataTable->rawColumns($rawColumns);
            return $dataTable->addIndexColumn()->toJson();
        } catch (Exception $e) {
            // dd($e->getMessage());
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function get_data_point_assessment(Request $request)
    {
        try {
            $data_row = PvtAssessmentEvent::join('events', 'events.id', '=', 'pvt_assessment_events.event_id')
                ->select(
                    "pvt_assessment_events.id as pvt_assessment_event_id",
                    "event_name",
                    "point",
                    "detail_point",
                    "pdca",
                    "score_max",
                    "year",
                    "status_point",
                    "stage"
                )
                ->where('events.id', $request->filterEvent)
                // ->where("year", $request->filterYear)
                ->where("category", $request->filterCategory)
                ->orderByRaw("CASE
                            WHEN pdca = 'Plan' THEN 1
                            WHEN pdca = 'Do' THEN 2
                            WHEN pdca = 'Check' THEN 3
                            WHEN pdca = 'Action' THEN 4
                            ELSE 5
                        END, pvt_assessment_events.id ASC");

            $dataTable = DataTables::of($data_row->get());
            $rawColumns[] = 'action';
            $dataTable->addColumn('action', function ($data_row) {
                if ($data_row->status_point == 'nonactive') {
                    return '<button class="btn btn-warning btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#updatePoint" onclick="get_data_point(' . $data_row->pvt_assessment_event_id . ')">Update</button>';
                }
            });

            $rawColumns[] = 'assign';
            $dataTable->addColumn('assign', function ($data_row) {
                if ($data_row->status_point == 'active') {
                    return '<input class="form-check-input" id="flexCheckChecked" type="checkbox" name="assessment_poin_id[]" value="' . $data_row->pvt_assessment_event_id . '" checked onclick="cek()" data-score-max="' . $data_row->score_max . '">';
                } elseif ($data_row->status_point == 'nonactive') {
                    return '<input class="form-check-input" id="flexCheckChecked" type="checkbox" name="assessment_poin_id[]" value="' . $data_row->pvt_assessment_event_id . '" onclick="cek()" data-score-max="' . $data_row->score_max . '">';
                }
            });
            $rawColumns[] = 'detail_point';
            $dataTable->addColumn('detail_point', function ($data_row) {
                return '<textarea class="form-control"  id="textarea"  rows="10" cols="40" type="text" readonly> ' . $data_row->detail_point . ' </textarea>';
            });
            $dataTable->rawColumns($rawColumns);
            return $dataTable->addIndexColumn()->toJson();
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function get_point($id)
    {
        dd($id);
        $assessmentPoint = AssessmentPoint::find($id);

        if (!$assessmentPoint) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        return response()->json($assessmentPoint);
    }
    public function get_data_assessment_juri(Request $request)
    {
        try {
            $id = $request->filterCategory;

            $data_row = PvtAssessmentTeam::join('pvt_event_teams', 'pvt_event_teams.id', '=', 'pvt_assessment_teams.event_team_id')
                ->join('pvt_assessment_events', 'pvt_assessment_events.id', '=', 'pvt_assessment_teams.assessment_event_id')
                ->where('pvt_assessment_teams.event_team_id', $id)
                ->select('point', 'detail_point', 'score_max', 'score', 'pvt_assessment_teams.id as key_id')
                ->get();

            $dataTable = DataTables::of($data_row);

            $rawColumns[] = 'detail_point';
            $dataTable->addColumn('detail_point', function ($data_row) {
                return '<textarea class="form-control"  id="textarea" rows="10" cols="50" type="text" readonly> ' . $data_row->detail_point . ' </textarea>';
            });

            $rawColumns[] = 'input_score';
            $dataTable->addColumn('input_score', function ($data_row) {
                if ($data_row->score != null) {
                    return '<b>' . $data_row->score . '</b>';
                } else {
                    return '<input class="form-control"  id="input" name="score[' . $data_row->key_id . ']" type="number">';
                }
            });

            $dataTable->rawColumns($rawColumns);

            return $dataTable->toJson();
        } catch (Exception $e) {
            //throw $th;
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function get_data_history($team_id)
    {
        try {
            $data = History::where('team_id', $team_id)->get(); // Code untuk mengambil data dari tabel
            return response()->json($data);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function get_competition(Request $request)
    {
        try {
            $idEmployee = auth()->user()->employee_id;

            $data_row = Team::join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                ->join('papers', 'papers.team_id', '=', 'teams.id')
                ->join('events', 'events.id', '=', 'pvt_event_teams.event_id')
                ->select([
                    'teams.id as team_id',
                    'pvt_event_teams.id as pvt_event_team_id',
                    'team_name',
                    'innovation_title',
                    'event_name',
                    'financial',
                    'potential_benefit',
                    'pvt_event_teams.status as status'
                ]);

            if ($request->filterRole == "innovator") {
                $data_row->join('pvt_members', 'teams.id', '=', 'pvt_members.team_id')
                    ->where('pvt_members.employee_id', $idEmployee);
            } elseif ($request->filterRole == "admin") {
                if ($request->filterEvent != '')
                    $data_row->where('pvt_event_teams.event_id', $request->filterEvent);
            }

            $dataTable = DataTables::of($data_row->get());

            if ($request->filterRole == "innovator") {
                $rawColumns[] = 'action';
                $dataTable->addColumn('action', function ($data_row) {
                    if ($data_row->status == "finish") {
                        return "<p>penilaian</p>";
                    } else {
                        return "<p> - </p>";
                    };
                });
            } elseif ($request->filterRole == "admin") {
                $rawColumns[] = 'action';
                $dataTable->addColumn('action', function ($data_row) {

                    $html = '<button class="btn btn-secondary btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#getExecute" onclick="setTeamId(' . $data_row->team_id . ')"><i class="fa fa-forward" aria-hidden="true"></i>&nbspExecute</button>';
                    // $html .= "<a class=\"btn btn-primary btn-xs\" href=\" " . route('assessment.juri.value', ['id' => $data_row->pvt_event_team_id]) . "\">Penilaian</a>";
                    // $html .= '<button class="btn btn-danger btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#deletePoint" onclick="change_url('. $data_row->pvt_event_team_id .', \'form_rollback\' )">Rollback</button>';
                    return $html;
                });
            }

            return $dataTable->toJson();
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }
    public function get_evidence(Request $request)
    {
        try {
            $data = Team::join('evidences', 'evidences.team_id', '=', 'teams.id')
                ->join('papers', 'papers.team_id', '=', 'teams.id')
                ->join('categories', 'categories.id', '=', 'teams.category_id')
                ->join('pvt_members', 'pvt_members.team_id', '=', 'teams.id')
                ->join('users', 'pvt_members.employee_id', '=', 'users.employee_id')
                ->select(
                    'evidences.id as id_evidence',
                    'team_name',
                    'innovation_title',
                    'category_name',
                    'event_name',
                    'prestasi',
                    'file_certificate',
                    'pvt_members.employee_id',
                    'pvt_members.status as status_member',
                    'users.name as name_employee',
                    'company_name',
                    'email',
                    'year'
                )
                ->get(); // Code untuk mengambil data dari tabel

            $dataTable = DataTables::of($data);

            return $dataTable->toJson();
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function submitData(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1', // Minimal satu elemen dalam array
            'items.*' => 'integer', // Validasi setiap elemen dalam array
        ]);

        if ($validator->fails()) {
            // Tangani jika validasi gagal
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Proses data jika validasi berhasil
        // ...

        return redirect()->route('success-page');
    }





    public function get_oda_assessment(Request $request)
    {
        try {
            // Membuat kunci cache berdasarkan filter yang digunakan
            $cacheKey = 'oda_assessment_' . md5(json_encode($request->all()));

            // Cek apakah data sudah ada di cache
            $dataTable = Cache::remember($cacheKey, 60, function () use ($request) {
                $data_category = Category::select('id', 'category_name', 'category_parent');
                if (!is_null($request->filterCategory) || $request->filterCategory != "") {
                    $data_category->where('id', $request->filterCategory);
                } else {
                    $data_category->where('category_parent', '<>', 'IDEA BOX');
                }
                $data_category = $data_category->get()->toArray();
                $categoryid = array_column($data_category, 'id');
                $searchidea = array_column($data_category, 'category_parent');
                $category_parent = in_array("IDEA BOX", $searchidea) ? 'IDEA' : 'BI/II';

                $arr_event_id = PvtAssessmentEvent::where('event_id', $request->filterEvent)
                    ->where('category', $category_parent)
                    ->where('status_point', 'active')
                    ->where('stage', 'on desk')
                    ->select('id', 'pdca', 'point')
                    ->get()
                    ->toArray();

                $arr_select_case = [
                    DB::raw('MIN(team_name) as Tim'),
                    DB::raw('MIN(innovation_title) as Judul'),
                    DB::raw('MIN(category_name) as Kategori'),
                    DB::raw('MIN(theme_name) as Tema'),
                    DB::raw('MIN(inovasi_lokasi) as Lokasi'),
                    'pvt_event_teams.id AS event_team_id_removed',
                    'pvt_event_teams.status as status_removed',
                ];

                if (count($arr_event_id)) {
                    $arr_select_case[] = DB::raw("MIN(pvt_assesment_team_judges.score) as \"score_kosong_removed\"");
                }

                $data_row = Team::join('papers', 'papers.team_id', '=', 'teams.id')
                    ->join('categories', 'categories.id', '=', 'teams.category_id')
                    ->join('themes', 'themes.id', '=', 'teams.theme_id')
                    ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
                    ->join('pvt_assesment_team_judges', 'pvt_assesment_team_judges.event_team_id', '=', 'pvt_event_teams.id')
                    ->join('pvt_assessment_events', function ($join) {
                        $join->on('pvt_assessment_events.id', '=', 'pvt_assesment_team_judges.assessment_event_id');
                    })
                    ->whereIn('categories.id', $categoryid)
                    ->where('pvt_event_teams.event_id', $request->filterEvent)
                    ->where('pvt_assessment_events.status_point', 'active')
                    ->where('pvt_assesment_team_judges.stage', 'on desk')
                    ->whereNotIn('papers.status_event', ['reject_group', 'reject_national', 'reject_international']);

                if (auth()->user()->role == "Juri") {
                    $data_row->join("judges", 'judges.id', '=', 'pvt_assesment_team_judges.judge_id')
                        ->where('judges.employee_id', auth()->user()->employee_id);
                }
                $data_row->groupBy('pvt_event_teams.id', 'pvt_event_teams.status' )->select($arr_select_case);

                $dataTable = DataTables::of($data_row->get());

                // Function to insert <br> after every specified number of characters
                function insertLineBreaks($string, $length = 15)
                {
                    return implode('<br>', str_split($string, $length));
                }
                $rawColumns[] = 'fix';
                $dataTable->addColumn('fix', function ($data_row) {
                    if ((auth()->user()->role === 'Superadmin' && $data_row['status_removed'] === 'On Desk')) {
                        return '<input class="form-check" type="checkbox" id="checkbox-' . $data_row['event_team_id_removed'] . '" name="pvt_event_team_id[]" value="' . $data_row['event_team_id_removed'] . '">';
                    } else if ((auth()->user()->role === 'Admin' && $data_row['status_removed'] === 'On Desk')) {
                        return '<input class="form-check" type="checkbox" id="checkbox-' . $data_row['event_team_id_removed'] . '" name="pvt_event_team_id[]" value="' . $data_row['event_team_id_removed'] . '">';
                    } else {
                        return '-';
                    }
                });

                $rawColumns[] = 'Total';
                $dataTable->addColumn('Total', function ($data_row) use ($arr_event_id) {
                    $data_total = pvtEventTeam::join('pvt_assesment_team_judges', 'pvt_assesment_team_judges.event_team_id', '=', 'pvt_event_teams.id')
                        ->join('pvt_assessment_events', 'pvt_assessment_events.id', '=', 'pvt_assesment_team_judges.assessment_event_id')
                        ->where('pvt_assessment_events.status_point', 'active')
                        ->where('pvt_assesment_team_judges.stage', 'on desk')
                        ->where('pvt_event_teams.id', $data_row['event_team_id_removed'])
                        ->groupBy('pvt_event_teams.id')
                        ->select(DB::raw("ROUND(ROUND(SUM(pvt_assesment_team_judges.score), 2) / COUNT(CASE WHEN pvt_assesment_team_judges.assessment_event_id = '" . $arr_event_id[0]['id'] . "' THEN pvt_assesment_team_judges.assessment_event_id END), 2) AS \"total\""))
                        ->get()
                        ->toArray();
                    return $data_total[0]['total'];
                });

                $rawColumns[] = 'action';
                $dataTable->addColumn('action', function ($data_row) use ($request) {
                    $inputPenilaianUrl = route('assessment.juri.value.oda', ['id' => $data_row['event_team_id_removed']]);
                    $lihatSofiUrl = route('assessment.show.sofi.oda', ['id' => $data_row['event_team_id_removed']]);

                    if (auth()->user()->role == 'Admin' || auth()->user()->role == 'Superadmin') {
                        $nextStepButton = $data_row['score_kosong(removed)'] == 0 ?
                            "<a class=\"btn btn-primary btn-xs mb-2\" href=\"$inputPenilaianUrl\">Pengaturan Juri</a>" :
                            "<a class=\"btn btn-primary btn-xs mb-2\" href=\"$inputPenilaianUrl\">Pengaturan Juri</a>";

                        return "$nextStepButton <a class=\"btn btn-info btn-xs " . ($data_row['status_removed'] != 'On Desk' ? 'disabled' : '') . "\" href=\"$lihatSofiUrl\">Lihat SOFI</a>";
                    } elseif (auth()->user()->role == 'Juri' || $this->checkIfJudge()) {
                        $inputPenilaianButton = "<a class=\"btn btn-primary btn-xs mb-2\" href=\"$inputPenilaianUrl\">Input Penilaian</a>";
                        return "$inputPenilaianButton <a class=\"btn btn-info btn-xs " . ($data_row['status_removed'] == 'On Desk' ? 'disabled' : '') . "\" href=\"$lihatSofiUrl\">Lihat SOFI</a>";
                    } else {
                        return "<a class=\"btn btn-info btn-xs " . ($data_row['status_removed'] == 'On Desk' ? 'disabled' : '') . "\" href=\"$lihatSofiUrl\">Lihat SOFI</a>";
                    }
                });


                if (count($arr_event_id)) {
                    for ($i = 0; $i < count($arr_event_id); $i++) {
                        $arr_select_case[] = DB::raw("ROUND(AVG(CASE WHEN pvt_assesment_team_judges.assessment_event_id = '" . $arr_event_id[$i]['id'] . "' THEN pvt_assesment_team_judges.score END), 2) AS \"Penilaian (" . $arr_event_id[$i]['pdca'] . ") : " . $arr_event_id[$i]['point'] . "\"");
                        $rawColumns[] = "Penilaian (" . $arr_event_id[$i]['pdca'] . ") : " . $arr_event_id[$i]['point'];
                        $dataTable->addColumn("Penilaian (" . $arr_event_id[$i]['pdca'] . ") : " . $arr_event_id[$i]['point'], function ($data_row) use ($i, $arr_event_id) {
                            $data_avg = pvtEventTeam::join('pvt_assesment_team_judges', 'pvt_assesment_team_judges.event_team_id', '=', 'pvt_event_teams.id')
                                ->join('pvt_assessment_events', 'pvt_assessment_events.id', '=', 'pvt_assesment_team_judges.assessment_event_id')
                                ->where('pvt_assessment_events.status_point', 'active')
                                ->where('pvt_assesment_team_judges.stage', 'on desk')
                                ->where('pvt_event_teams.id', $data_row['event_team_id_removed'])
                                ->groupBy('pvt_event_teams.id')
                                ->select(DB::raw("ROUND(AVG(CASE WHEN pvt_assesment_team_judges.assessment_event_id = '" . $arr_event_id[$i]['id'] . "' THEN pvt_assesment_team_judges.score END), 2) AS \"Nilai\""))
                                ->get()
                                ->toArray();

                            return $data_avg[0]['Nilai'];
                        });
                    }
                }

                $dataTable->rawColumns($rawColumns);

                $remove_column = [];
                foreach ($dataTable->original as $data_column) {
                    foreach ($data_column->getAttributes() as $column => $value) {
                        if (strstr($column, "removed") !== false) {
                            $remove_column[] = $column;
                        }
                    }
                }
                $dataTable->removeColumn($remove_column);

                return $dataTable->addIndexColumn()->make(true);
            });

            return $dataTable;
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function get_pa_assessment(Request $request)
    {
        try {
            $data_category = Category::select('id', 'category_name', 'category_parent');
            if (!is_null($request->filterCategory) || $request->filterCategory != "") {
                $data_category->where('id', $request->filterCategory);
            } else {
                $data_category->where('category_parent', '<>', 'IDEA BOX');
                // $data_category->where('id', 2);
            }
            $data_category = $data_category->get()
                ->toArray();

            $categoryid = array_column($data_category, 'id');
            $searchidea = array_column($data_category, 'category_parent');
            if (in_array("IDEA BOX", $searchidea)) {
                $category_parent = 'IDEA';
            } else {
                $category_parent = 'BI/II';
            }
            $arr_event_id = PvtAssessmentEvent::where('event_id', $request->filterEvent)
                ->where('category', $category_parent)
                ->where('status_point', 'active')
                ->where('stage', 'presentation')
                ->select('id', 'pdca', 'point')
                ->get()
                ->toArray();
            $arr_select_case = [
                DB::raw('MIN(team_name) as Tim'),
                DB::raw('MIN(innovation_title) as Judul'),
                DB::raw('MIN(category_name) as Kategori'),
                DB::raw('MIN(theme_name) as Tema'),
                DB::raw('MIN(inovasi_lokasi) as Lokasi'),
                'pvt_event_teams.id AS event_team_id(removed)',
                'pvt_event_teams.status as status(removed)',
                'pvt_event_teams.total_score_on_desk as Hasil Penilaian On Desk'
            ];


            if (count($arr_event_id)) {
                $arr_select_case[] = DB::raw("MIN(pvt_assesment_team_judges.score) as \"score_kosong(removed)\"");
            }

            $data_row = Team::join('papers', 'papers.team_id', '=', 'teams.id')
                ->join('categories', 'categories.id', '=', 'teams.category_id')
                ->join('themes', 'themes.id', '=', 'teams.theme_id')
                ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
                ->join('pvt_assesment_team_judges', 'pvt_assesment_team_judges.event_team_id', '=', 'pvt_event_teams.id')
                ->join('pvt_assessment_events', function ($join) {
                    $join->on('pvt_assessment_events.id', '=', 'pvt_assesment_team_judges.assessment_event_id');
                })
                ->whereIn('categories.id', $categoryid)
                ->where('pvt_event_teams.event_id', $request->filterEvent)
                ->where('pvt_event_teams.status', '!=', 'tidak lolos On Desk')
                ->where('pvt_assessment_events.status_point', 'active')
                ->where('pvt_assesment_team_judges.stage', 'presentation')
                ->whereNotIn('papers.status_event', ['reject_group', 'reject_national', 'reject_international']);

            if (auth()->user()->role == "Juri" || $this->checkIfJudge()) {
                $data_row->join("judges", 'judges.id', '=', 'pvt_assesment_team_judges.judge_id')
                    ->where('judges.employee_id', auth()->user()->employee_id);
            }
            $data_row->groupBy('pvt_event_teams.id')
                ->select($arr_select_case);

            $dataTable = DataTables::of($data_row->get());

            $rawColumns[] = 'Total';
            $dataTable->addColumn('Total', function ($data_row) use ($arr_event_id) {
                // Mengambil nilai total_score_presentation berdasarkan event_team_id
                $data_total = pvtEventTeam::where('id', $data_row['event_team_id(removed)'])
                    ->select('total_score_presentation') // Ambil hanya kolom total_score_presentation
                    ->first(); // Ambil data pertama (seharusnya hanya ada satu hasil)

                // Cek apakah data_total tidak null
                if ($data_total) {
                    // Kembalikan input dengan nilai dari total_score_presentation
                    return $data_total->total_score_presentation;
                }

                // Jika tidak ada, kembalikan input kosong
                return '<input class="form-control" style="width: 150px;" type="text" name="total_score_event[]" value="" readonly>';
            });

            $rawColumns[] = 'Ranking';
            $dataTable->addColumn('Urutan', function ($data_row) use ($request, $categoryid) {

                // Mengambil data tim berdasarkan total_score_presentation per kategori
                $data_total = pvtEventTeam::join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                    ->join('categories', 'categories.id', '=', 'teams.category_id')
                    ->where('pvt_event_teams.event_id', $request->filterEvent)  // Filter berdasarkan event
                    ->whereIn('categories.id', $categoryid)                     // Filter berdasarkan kategori
                    ->whereNotNull('pvt_event_teams.total_score_presentation')  // Mengecualikan yang null
                    ->groupBy('pvt_event_teams.id', 'categories.id')            // Kelompokkan berdasarkan kategori
                    ->select(
                        DB::raw("DENSE_RANK() OVER (PARTITION BY categories.id ORDER BY pvt_event_teams.total_score_presentation DESC) AS \"Ranking\""), // Menghitung ranking per kategori
                        'pvt_event_teams.id as id',
                        'pvt_event_teams.total_score_presentation',
                        'categories.id as category_id'
                    )  // Menambahkan kategori ke dalam hasil
                    ->get()
                    ->keyBy('id')  // Ubah hasil query menjadi key-value pair dengan id sebagai key
                    ->toArray();

                // Cek apakah total_score_presentation null atau 0
                $eventTeamId = $data_row['event_team_id(removed)'];
                if (!isset($data_total[$eventTeamId]) || $data_total[$eventTeamId]['total_score_presentation'] == 0) {
                    return 'belum dinilai'; // Kembalikan jika belum ada penilaian atau nilai 0
                }

                // Kembalikan ranking untuk event_team_id saat ini
                return $data_total[$eventTeamId]['Ranking'];
            });

            $rawColumns[] = 'fix';
            $dataTable->addColumn('fix', function ($data_row) {
                if (auth()->user()->role === 'Admin' | auth()->user()->role === 'Superadmin' && $data_row['status(removed)'] === 'Presentation')
                    return '<input class="form-check" type="checkbox" id="checkbox-' . $data_row['event_team_id(removed)'] . '" name="pvt_event_team_id[]" value="' . $data_row['event_team_id(removed)'] . '">';
                else
                    return '-';
            });

            $rawColumns[] = 'action';
            $dataTable->addColumn('action', function ($data_row) {
                $inputPenilaianUrl = route('assessment.juri.value.pa', ['id' => $data_row['event_team_id(removed)']]);
                $lihatSofiUrl = route('assessment.show.sofi.pa', ['id' => $data_row['event_team_id(removed)']]);
                
                // Mengecek peran pengguna dan status peserta
                if (auth()->user()->role == 'Admin' || auth()->user()->role == 'Superadmin') {
                    $nextStepButton = $data_row['score_kosong(removed)'] == 0 ?
                        "<a class=\"btn btn-primary btn-xs mb-2\" href=\"$inputPenilaianUrl\">Pengaturan Juri</a>" :
                        "<a class=\"btn btn-primary btn-xs mb-2\" href=\"$inputPenilaianUrl\">Pengaturan Juri</a>";

                    return "$nextStepButton <a class=\"btn btn-info btn-xs " . ($data_row['status(removed)'] == 'Presentation' ? 'disabled' : '') . "\"\" href=\"$lihatSofiUrl\">Lihat SOFI</a>";
                } elseif (auth()->user()->role == 'Juri' || $this->checkIfJudge()) {
                    $inputPenilaianButton = "<a class=\"btn btn-primary btn-xs mb-2\" href=\"$inputPenilaianUrl\">Input Penilaian</a>";
                    return "$inputPenilaianButton <a class=\"btn btn-info btn-xs  " . ($data_row['status(removed)'] == 'Presentation' ? 'disabled' : '') . "\" href=\"$lihatSofiUrl\">Lihat SOFI</a>";
                } else {
                    // Jika bukan admin, superadmin, atau juri, hanya menampilkan tombol Lihat SOFI
                    return "<a class=\"btn btn-info btn-xs  " . ($data_row['status(removed)'] == 'Presentation' ? 'disabled' : '') . "\" href=\"$lihatSofiUrl\">Lihat SOFI</a>";
                }
            });


            $dataTable->rawColumns($rawColumns);

            $remove_column = [];
            foreach ($dataTable->original as $data_column) {
                foreach ($data_column->getAttributes() as $column => $value) {
                    if (strstr($column, "removed") !== false) {
                        $remove_column[] = $column;
                    }
                }
            }
            $dataTable->removeColumn($remove_column);

            return $dataTable->addIndexColumn()->toJson();
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function get_input_oda_assessment_team(Request $request)
    {
        try {
            $arr_event_team_id = pvtAssesmentTeamJudge::join('judges', 'pvt_assesment_team_judges.judge_id', '=', 'judges.id')
                ->join('users', 'users.employee_id', '=', 'judges.employee_id')
                ->where('event_team_id', $request->filterEventTeamId)
                ->where('pvt_assesment_team_judges.stage', 'on desk')
                ->select(['judge_id', 'users.name as name', 'users.employee_id as employee_id'])
                // ->distinct()
                ->groupBy('judge_id', 'users.name', 'users.employee_id')
                // ->pluck('judge_id')
                ->get()
                ->toArray();

            $category = PvtEventTeam::join('teams', 'pvt_event_teams.team_id', '=', 'teams.id')
                ->join('categories', 'categories.id', '=', 'teams.category_id')
                ->where('pvt_event_teams.id', $request->filterEventTeamId)
                ->pluck('category_parent')
                ->first();

            if ($category == 'IDEA BOX')
                $category = 'IDEA';
            else
                $category = 'BI/II';

            $size_event_team = count($arr_event_team_id);
            $arr_select_case = [
                'point as Poin',
                'detail_point as detail_point(removed)',
                'score_max as score_max(removed)',
                'pvt_assessment_events.id as assessment_events_id(removed)',
                DB::raw("MIN(pvt_event_teams.status) as \"status(removed)\""),
                'pvt_assessment_events.pdca as pdca(removed)'
            ];

            if ($size_event_team != 0) {
                for ($i = 0; $i < $size_event_team; $i++) {
                    $poin_ke = $i + 1;
                    $arr_select_case[] = DB::raw("MAX(CASE WHEN pvt_assesment_team_judges.judge_id = '" . $arr_event_team_id[$i]['judge_id'] . "' THEN pvt_assesment_team_judges.score END) AS \"Penilaian (Juri " . $i + 1 . " : " . str_replace('.', '', $arr_event_team_id[$i]['name']) . " (removed) )\"");
                    $arr_select_case[] = DB::raw("MAX(CASE WHEN pvt_assesment_team_judges.judge_id = '" . $arr_event_team_id[$i]['judge_id'] . "' THEN pvt_assesment_team_judges.id END) AS \"ID (Juri " . $i + 1 . " : " . str_replace('.', '', $arr_event_team_id[$i]['name'])  . " (removed) )\"");
                }
                $data_row = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                    // ->join('pvt_assessment_events', 'pvt_assessment_events.event_id', '=', 'pvt_event_teams.event_id')
                    ->join('pvt_assessment_events', function ($join) {
                        $join->on('pvt_assessment_events.id', '=', 'pvt_assesment_team_judges.assessment_event_id');
                        //  ->on('pvt_assessment_events.year', '=', 'pvt_event_teams.year');
                    })
                    ->where('category', $category)
                    ->where('pvt_assessment_events.status_point', 'active')
                    ->where('pvt_event_teams.id', $request->filterEventTeamId)
                    ->where('pvt_assesment_team_judges.stage', 'on desk')
                    ->where('pvt_assessment_events.stage', 'on desk')
                    ->groupBy('pvt_assessment_events.id')
                    ->select($arr_select_case)
                    ->orderByRaw("CASE
                                            WHEN 'pdca(removed)' = 'Plan' THEN 1
                                            WHEN 'pdca(removed)' = 'Do' THEN 2
                                            WHEN 'pdca(removed)' = 'Check' THEN 3
                                            WHEN 'pdca(removed)' = 'Action' THEN 4
                                            ELSE 5
                                        END, pvt_assessment_events.id ASC");
            } else {
                $data_row = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                    ->join('pvt_assessment_events', function ($join) {
                        $join->on('pvt_assessment_events.id', '=', 'pvt_assesment_team_judges.assessment_event_id');
                        //  ->on('pvt_assessment_events.year', '=', 'pvt_event_teams.year');
                    })
                    ->where('category', $category)
                    ->where('pvt_assessment_events.status_point', 'active')
                    ->where('pvt_event_teams.id', $request->filterEventTeamId)
                    ->where('pvt_assesment_team_judges.stage', 'on desk')
                    ->where('pvt_assessment_events.stage', 'on desk')
                    ->groupBy('pvt_assessment_events.id')
                    ->select($arr_select_case)
                    ->orderByRaw("CASE
                                            WHEN 'pdca(removed)' = 'Plan' THEN 1
                                            WHEN 'pdca(removed)' = 'Do' THEN 2
                                            WHEN 'pdca(removed)' = 'Check' THEN 3
                                            WHEN 'pdca(removed)' = 'Action' THEN 4
                                            ELSE 5
                                        END, pvt_assessment_events.id ASC");
            }

            $dataTable = DataTables::of($data_row->get());

            $rawColumns[] = 'Detail Poin';
            $dataTable->addColumn('Detail Poin', function ($data_row) {
                return '<textarea class="form-control mb-3"  id="textarea" rows="10" cols="50" type="text" readonly> ' . $data_row['detail_point(removed)'] . ' </textarea>';
            });

            $rawColumns[] = 'Maks Skor';
            $dataTable->addColumn('Maks Skor', function ($data_row) {
                return '<a id="' . $data_row['assessment_events_id(removed)'] . '" readonly style="color: green; text-align: center; display: block; margin-top: 20px;"> ' . $data_row['score_max(removed)'] . ' </a>
            <br>
            <br>';
            });

            if ($size_event_team != 0) {
                for ($i = 0; $i < $size_event_team; $i++) {
                    $poin_ke = $i + 1;
                    $rawColumns[] = "Penilaian (Juri " . $i + 1 . " : " . str_replace('.', '', $arr_event_team_id[$i]['name']) . ")";
                    $dataTable->addColumn("Penilaian (Juri " . $i + 1 . " : " . str_replace('.', '', $arr_event_team_id[$i]['name']) . ")", function ($data_row) use ($i, $arr_event_team_id) {
                        if ($arr_event_team_id[$i]['employee_id'] == auth()->user()->employee_id && $data_row['status(removed)'] == "On Desk") {
                            return '<input class="form-control"  id="input-' . $i + 1 . '-' . $data_row['assessment_events_id(removed)'] . '" name="score[' . $data_row["ID (Juri " . $i + 1 . " : " . str_replace('.', '', $arr_event_team_id[$i]['name']) . " (removed) )"] . ']" value="' . $data_row["Penilaian (Juri " . $i + 1 . " : " . str_replace('.', '', $arr_event_team_id[$i]['name']) . " (removed) )"] . '" type="number" onInput="validate_score(this)" >
                                <div class="invalid-feedback">
                                    Score melebihi maksimum.
                                </div>
                                <br id="br-' . $i + 1 . '-' . $data_row['assessment_events_id(removed)'] . '" style="display:block">';
                        } else {
                            return '<input class="form-control"  id="input-' . $i + 1 . '-' . $data_row['assessment_events_id(removed)'] . '" name="score[' . $data_row["ID (Juri " . $i + 1 . " : " . str_replace('.', '', $arr_event_team_id[$i]['name']) . " (removed) )"] . ']" value="' . $data_row["Penilaian (Juri " . $i + 1 . " : " . str_replace('.', '', $arr_event_team_id[$i]['name']) . " (removed) )"] . '" type="number" onInput="validate_score(this)" disabled>
                                <div class="invalid-feedback">
                                    Score melebihi maksimum.
                                </div>
                                <br id="br-' . $i + 1 . '-' . $data_row['assessment_events_id(removed)'] . '" style="display:block">';
                        }
                    });
                }
            }
            
            $dataTable->rawColumns($rawColumns);

            $remove_column = [];
            foreach ($dataTable->original as $data_column) {
                foreach ($data_column->getAttributes() as $column => $value) {
                    if (strstr($column, "removed") !== false) {
                        $remove_column[] = $column;
                    }
                }
            }

            $dataTable->removeColumn($remove_column);

            return $dataTable->addIndexColumn()->toJson();
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }


    public function get_input_pa_assessment_team(Request $request)
    {
        try {
            // $idEmployee = auth()->user()->employee_id;
            $arr_event_team_id = pvtAssesmentTeamJudge::join('judges', 'pvt_assesment_team_judges.judge_id', '=', 'judges.id')
                ->join('users', 'users.employee_id', '=', 'judges.employee_id')
                ->where('event_team_id', $request->filterEventTeamId)
                ->where('pvt_assesment_team_judges.stage', 'presentation')
                ->select(['judge_id', 'users.name as name', 'users.employee_id as employee_id'])
                // ->distinct()
                ->groupBy('judge_id', 'users.name', 'users.employee_id')
                // ->pluck('judge_id')
                ->get()
                ->toArray();

            $category = PvtEventTeam::join('teams', 'pvt_event_teams.team_id', '=', 'teams.id')
                ->join('categories', 'categories.id', '=', 'teams.category_id')
                ->where('pvt_event_teams.id', $request->filterEventTeamId)
                ->pluck('category_parent')
                ->first();

            if ($category == 'IDEA BOX')
                $category = 'IDEA';
            else
                $category = 'BI/II';

            $size_event_team = count($arr_event_team_id);
            $arr_select_case = [
                'point',
                'detail_point as detail_point(removed)',
                'score_max as score_max(removed)',
                'pvt_assessment_events.id as assessment_events_id(removed)',
                'pvt_assessment_events.pdca as pdca(removed)',
                DB::raw("MIN(pvt_event_teams.status) as \"status(removed)\""),
            ];

            if ($size_event_team != 0) {
                for ($i = 0; $i < $size_event_team; $i++) {
                    $poin_ke = $i + 1;
                    $arr_select_case[] = DB::raw("MAX(CASE WHEN pvt_assesment_team_judges.judge_id = '" . $arr_event_team_id[$i]['judge_id'] . "' THEN pvt_assesment_team_judges.score END) AS \"Penilaian (Juri " . $i + 1 . " : " . str_replace('.', '', $arr_event_team_id[$i]['name']) . " (removed) )\"");
                    $arr_select_case[] = DB::raw("MAX(CASE WHEN pvt_assesment_team_judges.judge_id = '" . $arr_event_team_id[$i]['judge_id'] . "' THEN pvt_assesment_team_judges.id END) AS \"ID (Juri " . $i + 1 . " : " . str_replace('.', '', $arr_event_team_id[$i]['name'])  . " (removed) )\"");
                }
                // $arr_select_case[] = DB::raw("AVG(CASE WHEN pvt_assesment_team_judges.event_team_id = pvt_event_teams.id AND pvt_assesment_team_judges.assessment_event_id = pvt_assessment_events.id THEN pvt_assesment_team_judges.score END) AS \"Rata - rata Nilai\"");
                $data_row = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                    // ->join('pvt_assessment_events', 'pvt_assessment_events.event_id', '=', 'pvt_event_teams.event_id')
                    ->join('pvt_assessment_events', function ($join) {
                        $join->on('pvt_assessment_events.id', '=', 'pvt_assesment_team_judges.assessment_event_id');
                        //  ->on('pvt_assessment_events.year', '=', 'pvt_event_teams.year');
                    })
                    ->where('category', $category)
                    ->where('pvt_assessment_events.status_point', 'active')
                    ->where('pvt_event_teams.id', $request->filterEventTeamId)
                    ->where('pvt_assesment_team_judges.stage', 'presentation')
                    ->groupBy('pvt_assessment_events.id')
                    ->select($arr_select_case)
                    ->orderByRaw("CASE
                                            WHEN 'pdca(removed)' = 'Plan' THEN 1
                                            WHEN 'pdca(removed)' = 'Do' THEN 2
                                            WHEN 'pdca(removed)' = 'Check' THEN 3
                                            WHEN 'pdca(removed)' = 'Action' THEN 4
                                            ELSE 5
                                        END, pvt_assessment_events.id ASC");
            } else {
                $data_row = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                    ->join('pvt_assessment_events', function ($join) {
                        $join->on('pvt_assessment_events.id', '=', 'pvt_assesment_team_judges.assessment_event_id');
                        //  ->on('pvt_assessment_events.year', '=', 'pvt_event_teams.year');
                    })
                    ->where('category', $category)
                    ->where('pvt_assessment_events.status_point', 'active')
                    ->where('pvt_event_teams.id', $request->filterEventTeamId)
                    ->where('pvt_assesment_team_judges.stage', 'presentation')
                    ->groupBy('pvt_assessment_events.id')
                    ->select($arr_select_case)
                    ->orderByRaw("CASE
                                            WHEN 'pdca(removed)' = 'Plan' THEN 1
                                            WHEN 'pdca(removed)' = 'Do' THEN 2
                                            WHEN 'pdca(removed)' = 'Check' THEN 3
                                            WHEN 'pdca(removed)' = 'Action' THEN 4
                                            ELSE 5
                                        END, pvt_assessment_events.id ASC");
            }
            $dataTable = DataTables::of($data_row->get());

            $rawColumns[] = 'Detail poin';
            $dataTable->addColumn('Detail poin', function ($data_row) {
                return '<textarea class="form-control"  id="textarea" rows="10" cols="50" type="text" readonly> ' . $data_row['detail_point(removed)'] . ' </textarea>';
            });

            $data = $data_row->get();

            $found = false;
            foreach ($data as $item) {
                if (isset($item['point']) && $item['point'] === 'Mutu Presentasi') {
                    $found = true;
                    break;
                }
            }

            // Output hasil pengecekan
            if ($found) {
                $rawColumns[] = 'Rata - Rata Nilai';
                $dataTable->addColumn('Rata - Rata Nilai', function ($data_row) use ($request) {
                    $data_assessment_team_judge = pvtAssesmentTeamJudge::where('assessment_event_id', $data_row['assessment_events_id(removed)'])
                        ->where('event_team_id', $request->filterEventTeamId)
                        ->where('stage', 'presentation')
                        ->groupBy('assessment_event_id')
                        ->select(DB::raw("ROUND(AVG(score), 2) AS \"average\""))
                        ->get();

                    if ($data_assessment_team_judge->count())
                        return '<span style="color: #007bff;">' . $data_assessment_team_judge[0]['average'] . '</span><br><br>';
                    else
                        return '-';
                });
            } else {
                $dataTable->addColumn('Rata - Rata Nilai', function ($data_row) use ($request) {
                    $data_assessment_team_judge = pvtAssesmentTeamJudge::where('assessment_event_id', $data_row['assessment_events_id(removed)'])
                        ->where('event_team_id', $request->filterEventTeamId)
                        ->where('stage', 'on desk')
                        ->groupBy('assessment_event_id')
                        ->select(DB::raw("ROUND(AVG(score), 2) AS \"average\""))
                        ->get();

                    if ($data_assessment_team_judge->count())
                        return '<span style="color: #007bff;">' . $data_assessment_team_judge[0]['average'] . '</span><br><br>';
                    else
                        return '-';
                });
            }



            $rawColumns[] = 'Maks skor';
            $dataTable->addColumn('Maks skor', function ($data_row) {
                return '<a id="' . $data_row['assessment_events_id(removed)'] . '" readonly style="color: green; text-align: center; display: block; margin-top: 20px;"> ' . $data_row['score_max(removed)'] . ' </a>
            <br>
            <br>';
            });


            if ($size_event_team != 0) {
                for ($i = 0; $i < $size_event_team; $i++) {
                    $poin_ke = $i + 1;
                    $rawColumns[] = "Penilaian (Juri " . $i + 1 . " : " . str_replace('.', '', $arr_event_team_id[$i]['name']) . ")";
                    $dataTable->addColumn("Penilaian (Juri " . $i + 1 . " : " . str_replace('.', '', $arr_event_team_id[$i]['name']) . ")", function ($data_row) use ($i, $arr_event_team_id) {
                        if ($arr_event_team_id[$i]['employee_id'] != auth()->user()->employee_id || $data_row['status(removed)'] != "Presentation") {
                            return '<input class="form-control"  id="input-' . $i + 1 . '-' . $data_row['assessment_events_id(removed)'] . '" name="score[' . $data_row["ID (Juri " . $i + 1 . " : " . str_replace('.', '', $arr_event_team_id[$i]['name']) . " (removed) )"] . ']" value="' . $data_row["Penilaian (Juri " . $i + 1 . " : " . str_replace('.', '', $arr_event_team_id[$i]['name']) . " (removed) )"] . '" type="number" onInput="validate_score(this)" disabled>
                                <div class="invalid-feedback">
                                    Score melebihi maksimum.
                                </div>
                                <br id="br-' . $i + 1 . '-' . $data_row['assessment_events_id(removed)'] . '" style="display:block">';
                        } else {
                            return '<input class="form-control"  id="input-' . $i + 1 . '-' . $data_row['assessment_events_id(removed)'] . '" name="score[' . $data_row["ID (Juri " . $i + 1 . " : " . str_replace('.', '', $arr_event_team_id[$i]['name']) . " (removed) )"] . ']" value="' . $data_row["Penilaian (Juri " . $i + 1 . " : " . str_replace('.', '', $arr_event_team_id[$i]['name']) . " (removed) )"] . '" type="number" onInput="validate_score(this)">
                                <div class="invalid-feedback">
                                    Score melebihi maksimum.
                                </div>
                                <br id="br-' . $i + 1 . '-' . $data_row['assessment_events_id(removed)'] . '" style="display:block">';
                        }
                    });
                }
            }
            // dd($rawColumns);
            $dataTable->rawColumns($rawColumns);

            $remove_column = [];
            foreach ($dataTable->original as $data_column) {
                foreach ($data_column->getAttributes() as $column => $value) {
                    if (strstr($column, "removed") !== false) {
                        $remove_column[] = $column;
                    }
                }
            }

            $dataTable->removeColumn($remove_column);

            return $dataTable->addIndexColumn()->toJson();
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }


    public function get_input_caucus_assessment_team(Request $request)
    {
        try {
            // $idEmployee = auth()->user()->employee_id;
            $arr_event_team_id = pvtAssesmentTeamJudge::join('judges', 'pvt_assesment_team_judges.judge_id', '=', 'judges.id')
                ->join('users', 'users.employee_id', '=', 'judges.employee_id')
                ->where('event_team_id', $request->filterEventTeamId)
                ->where('pvt_assesment_team_judges.stage', 'caucus')
                ->select(['judge_id', 'users.name as name', 'users.employee_id as employee_id'])
                // ->distinct()
                ->groupBy('judge_id', 'users.name', 'users.employee_id')
                // ->pluck('judge_id')
                ->get()
                ->toArray();

            $category = PvtEventTeam::join('teams', 'pvt_event_teams.team_id', '=', 'teams.id')
                ->join('categories', 'categories.id', '=', 'teams.category_id')
                ->where('pvt_event_teams.id', $request->filterEventTeamId)
                ->pluck('category_parent')
                ->first();

            if ($category == 'IDEA BOX')
                $category = 'IDEA';
            else
                $category = 'BI/II';

            $size_event_team = count($arr_event_team_id);
            $arr_select_case = [
                'point',
                'detail_point as detail_point(removed)',
                'score_max as score_max(removed)',
                'pvt_assessment_events.id as assessment_events_id(removed)',
                'pvt_assessment_events.pdca as pdca(removed)',
                DB::raw("MIN(pvt_event_teams.status) as \"status(removed)\""),
            ];

            if ($size_event_team != 0) {
                for ($i = 0; $i < $size_event_team; $i++) {
                    $poin_ke = $i + 1;
                    $arr_select_case[] = DB::raw("MAX(CASE WHEN pvt_assesment_team_judges.judge_id = '" . $arr_event_team_id[$i]['judge_id'] . "' THEN pvt_assesment_team_judges.score END) AS \"Penilaian (Juri " . $i + 1 . " : " . str_replace('.', '', $arr_event_team_id[$i]['name']) . " (removed) )\"");
                    $arr_select_case[] = DB::raw("MAX(CASE WHEN pvt_assesment_team_judges.judge_id = '" . $arr_event_team_id[$i]['judge_id'] . "' THEN pvt_assesment_team_judges.id END) AS \"ID (Juri " . $i + 1 . " : " . str_replace('.', '', $arr_event_team_id[$i]['name'])  . " (removed) )\"");
                }
                // $arr_select_case[] = DB::raw("AVG(CASE WHEN pvt_assesment_team_judges.event_team_id = pvt_event_teams.id AND pvt_assesment_team_judges.assessment_event_id = pvt_assessment_events.id THEN pvt_assesment_team_judges.score END) AS \"Rata - rata Nilai\"");
                $data_row = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                    // ->join('pvt_assessment_events', 'pvt_assessment_events.event_id', '=', 'pvt_event_teams.event_id')
                    ->join('pvt_assessment_events', function ($join) {
                        $join->on('pvt_assessment_events.id', '=', 'pvt_assesment_team_judges.assessment_event_id');
                        //  ->on('pvt_assessment_events.year', '=', 'pvt_event_teams.year');
                    })
                    ->where('category', $category)
                    ->where('pvt_assessment_events.status_point', 'active')
                    ->where('pvt_event_teams.id', $request->filterEventTeamId)
                    ->where('pvt_assesment_team_judges.stage', 'presentation')
                    ->groupBy('pvt_assessment_events.id')
                    ->select($arr_select_case)
                    ->orderByRaw("CASE
                                            WHEN 'pdca(removed)' = 'Plan' THEN 1
                                            WHEN 'pdca(removed)' = 'Do' THEN 2
                                            WHEN 'pdca(removed)' = 'Check' THEN 3
                                            WHEN 'pdca(removed)' = 'Action' THEN 4
                                            ELSE 5
                                        END, pvt_assessment_events.id ASC");
            } else {
                $data_row = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                    ->join('pvt_assessment_events', function ($join) {
                        $join->on('pvt_assessment_events.id', '=', 'pvt_assesment_team_judges.assessment_event_id');
                        //  ->on('pvt_assessment_events.year', '=', 'pvt_event_teams.year');
                    })
                    ->where('category', $category)
                    ->where('pvt_assessment_events.status_point', 'active')
                    ->where('pvt_event_teams.id', $request->filterEventTeamId)
                    ->where('pvt_assesment_team_judges.stage', 'caucus')
                    ->groupBy('pvt_assessment_events.id')
                    ->select($arr_select_case)
                    ->orderByRaw("CASE
                                            WHEN 'pdca(removed)' = 'Plan' THEN 1
                                            WHEN 'pdca(removed)' = 'Do' THEN 2
                                            WHEN 'pdca(removed)' = 'Check' THEN 3
                                            WHEN 'pdca(removed)' = 'Action' THEN 4
                                            ELSE 5
                                        END, pvt_assessment_events.id ASC");
            }

            $dataTable = DataTables::of($data_row->get());

            $rawColumns[] = 'Detail point';
            $dataTable->addColumn('Detail point', function ($data_row) {
                return '<textarea class="form-control"  id="textarea" rows="10" cols="50" type="text" readonly> ' . $data_row['detail_point(removed)'] . ' </textarea>';
            });

            $data = $data_row->get();

            $found = false;
            foreach ($data as $item) {
                if (isset($item['point']) && $item['point'] === 'Mutu Presentasi') {
                    $found = true;
                    break;
                }
            }

            // Output hasil pengecekan
            if ($found) {
                $rawColumns[] = 'Rata - Rata Nilai';
                $dataTable->addColumn('Rata - Rata Nilai', function ($data_row) use ($request) {
                    $data_assessment_team_judge = pvtAssesmentTeamJudge::where('assessment_event_id', $data_row['assessment_events_id(removed)'])
                        ->where('event_team_id', $request->filterEventTeamId)
                        ->where('stage', 'presentation')
                        ->groupBy('assessment_event_id')
                        ->select(DB::raw("ROUND(AVG(score), 2) AS \"average\""))
                        ->get();

                    if ($data_assessment_team_judge->count())
                        return '<span style="color: #007bff;">' . $data_assessment_team_judge[0]['average'] . '</span><br><br>';
                    else
                        return '-';
                });
            } else {
                $dataTable->addColumn('Rata - Rata Nilai', function ($data_row) use ($request) {
                    $data_assessment_team_judge = pvtAssesmentTeamJudge::where('assessment_event_id', $data_row['assessment_events_id(removed)'])
                        ->where('event_team_id', $request->filterEventTeamId)
                        ->where('stage', 'on desk')
                        ->groupBy('assessment_event_id')
                        ->select(DB::raw("ROUND(AVG(score), 2) AS \"average\""))
                        ->get();

                    if ($data_assessment_team_judge->count())
                        return '<span style="color: #007bff;">' . $data_assessment_team_judge[0]['average'] . '</span><br><br>';
                    else
                        return '-';
                });
            }


            $rawColumns[] = 'Score max';
            $dataTable->addColumn('Score max', function ($data_row) {
                return '<a id="' . $data_row['assessment_events_id(removed)'] . '" readonly style="color: green; text-align: center; display: block; margin-top: 20px;"> ' . $data_row['score_max(removed)'] . ' </a>
            <br>
            <br>';
            });


            if ($size_event_team != 0) {
                for ($i = 0; $i < $size_event_team; $i++) {
                    $poin_ke = $i + 1;
                    $rawColumns[] = "Penilaian (Juri " . $i + 1 . " : " . str_replace('.', '', $arr_event_team_id[$i]['name']) . ")";
                    $dataTable->addColumn("Penilaian (Juri " . $i + 1 . " : " . str_replace('.', '', $arr_event_team_id[$i]['name']) . ")", function ($data_row) use ($i, $arr_event_team_id) {
                        if ($arr_event_team_id[$i]['employee_id'] != auth()->user()->employee_id || $data_row['status(removed)'] != "Caucus") {
                            return '<input class="form-control"  id="input-' . $i + 1 . '-' . $data_row['assessment_events_id(removed)'] . '" name="score[' . $data_row["ID (Juri " . $i + 1 . " : " . str_replace('.', '', $arr_event_team_id[$i]['name']) . " (removed) )"] . ']" value="' . $data_row["Penilaian (Juri " . $i + 1 . " : " . str_replace('.', '', $arr_event_team_id[$i]['name']) . " (removed) )"] . '" type="number" onInput="validate_score(this)" disabled>
                                <div class="invalid-feedback">
                                    Score melebihi maksimum.
                                </div>
                                <br id="br-' . $i + 1 . '-' . $data_row['assessment_events_id(removed)'] . '" style="display:block">';
                        } else {
                            return '<input class="form-control"  id="input-' . $i + 1 . '-' . $data_row['assessment_events_id(removed)'] . '" name="score[' . $data_row["ID (Juri " . $i + 1 . " : " . str_replace('.', '', $arr_event_team_id[$i]['name']) . " (removed) )"] . ']" value="' . $data_row["Penilaian (Juri " . $i + 1 . " : " . str_replace('.', '', $arr_event_team_id[$i]['name']) . " (removed) )"] . '" type="number" onInput="validate_score(this)">
                                <div class="invalid-feedback">
                                    Score melebihi maksimum.
                                </div>
                                <br id="br-' . $i + 1 . '-' . $data_row['assessment_events_id(removed)'] . '" style="display:block">';
                        }
                    });
                }
            }
            // dd($rawColumns);
            $dataTable->rawColumns($rawColumns);

            $remove_column = [];
            foreach ($dataTable->original as $data_column) {
                foreach ($data_column->getAttributes() as $column => $value) {
                    if (strstr($column, "removed") !== false) {
                        $remove_column[] = $column;
                    }
                }
            }

            $dataTable->removeColumn($remove_column);

            return $dataTable->addIndexColumn()->toJson();
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function get_judge(Request $request)
    {
        try {
            $data_row = Judge::join('users', 'users.employee_id', '=', 'judges.employee_id')
                ->join('events', 'events.id', '=', 'judges.event_id')
                ->select(
                    'judges.*',
                    'name',
                    'event_name',
                )->get();
            $dataTable = DataTables::of($data_row);

            $rawColumns[] = 'status_juri';
            $dataTable->addColumn('status_juri', function ($data_row) {
                if ($data_row->status == 'active') {
                    return '<span class="badge bg-success me-2">Active</span>';
                } elseif ($data_row->status == 'nonactive') {
                    return '<span class="badge bg-danger me-2">Nonactive</span>';
                }
            });

            $rawColumns[] = 'action';
            $dataTable->addColumn('action', function ($data_row) {
                if ($data_row->status == 'active') {
                    return '

                     <button class="btn btn-danger btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#revokeJudge" onclick="get_data_judge(' . $data_row->id . ')">Revoke</button> <button class="btn btn-warning btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#updateJudge" onclick="update_judge(' . $data_row->id . ')"><i class="fa fa-pencil" aria-hidden="true"></button>';
                } elseif ($data_row->status == 'nonactive') {
                    return '<button class="btn btn-warning btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#updateJudge" onclick="update_judge(' . $data_row->id . ')"><i class="fa fa-pencil-square" aria-hidden="true"></button>';
                }
            });

            $dataTable->rawColumns($rawColumns);
            return $dataTable->toJson();
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }
    public function get_bod_event(Request $request)
    {
        try {
            $data_row = BodEvent::join('users', 'users.employee_id', '=', 'bod_events.employee_id')
                ->join('events', 'events.id', '=', 'bod_events.event_id')
                ->select(
                    'bod_events.*',
                    'name',
                    'event_name',
                )->get();
            $dataTable = DataTables::of($data_row);

            $rawColumns[] = 'status_bod';
            $dataTable->addColumn('status_bod', function ($data_row) {
                if ($data_row->status == 'active') {
                    return '<span class="badge bg-success me-2">Active</span>';
                } elseif ($data_row->status == 'nonactive') {
                    return 'Nonactive';
                }
            });

            $rawColumns[] = 'revoke';
            $dataTable->addColumn('revoke', function ($data_row) {
                if ($data_row->status == 'active') {
                    return '<button class="btn btn-danger btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#revokeJudge" onclick="get_data_judge(' . $data_row->id . ')">Revoke</button>';
                } elseif ($data_row->status == 'nonactive') {
                    return '-';
                }
            });
            $dataTable->rawColumns($rawColumns);
            return $dataTable->toJson();
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }
    public function get_event(Request $request)
    {
        $isSuperadmin = Auth::user()->role === 'Superadmin';
        $company_code = Auth::user()->company_code;

        try {
            $data_row = Event::orderBy('id', 'desc')
                ->orderByRaw("CASE WHEN status = 'active' THEN 0 WHEN status = 'not active' THEN 1 WHEN status = 'finish' THEN 2 ELSE 3 END");
            Log::debug($data_row->get());

            // Jika user bukan Superadmin, filter berdasarkan company_code
            if (!$isSuperadmin) {
                $data_row->whereHas('companies', function ($query) use ($company_code) {
                    $query->where('company_code', $company_code);
                });
            }

            $data_row = $data_row->get();

            $dataTable = DataTables::of($data_row);
            $rawColumns = [];

            $rawColumns[] = 'Company';
            $dataTable->addColumn('company', function ($data_row) {
                $companies = $data_row->companies->pluck('company_name')->toArray();
                return $companies;
            });

            $rawColumns[] = 'status';
            $dataTable->addColumn('status', function ($data_row) {
                if ($data_row->status == 'active') {
                    return '<span class="badge bg-success me-2">Active</span>';
                } elseif ($data_row->status == 'not active') {
                    return '<span class="badge bg-danger me-2">Not active</span>';
                } elseif ($data_row->status == 'finish') {
                    return '<span class="badge bg-info me-2">Finish</span>';
                }
            });

            $rawColumns[] = 'action';
            $dataTable->addColumn('action', function ($data_row) {
                $companyCodeUser = Auth::user()->company_code;
                $isAdmin = Auth::user()->role === "Admin";
                $event = Event::find($data_row->id);
                $startDiv = '<div class="d-flex flex-column gap-1 justify-content-center align-items-center">';
                $endDiv = '</div>';

                if (auth()->user()->role == 'Superadmin') {
                    return $startDiv . '<button class="btn btn-sm btn-dark btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#changeEvent" onclick="set_data_on_modal(' . $data_row->id . ')" >Edit Status</button>
                            <button class="btn btn-sm btn-warning btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#updateEvent" onclick="update_modal(' . $data_row->id . ')"><i class="fa fa-pencil me-1"></i> Edit</button>' . $endDiv;
                } else if ($isAdmin && $event->companies()->where('company_code', $companyCodeUser)->exists() && in_array($event->type, ['internal', 'AP'])) {
                    return $startDiv . '<button class="btn btn-sm btn-dark btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#changeEvent" onclick="set_data_on_modal(' . $data_row->id . ')" >Edit Status</button>
                            <button class="btn btn-sm btn-warning btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#updateEvent" onclick="update_modal(' . $data_row->id . ')"><i class="fa fa-pencil me-1"></i> Edit</button>' . $endDiv;
                }
            });

            $dataTable->rawColumns($rawColumns);

            return $dataTable->toJson();
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }


    public function get_fix_assessment(Request $request)
    {
        try {
            // dd($request->all());
            $data_category = Category::where('id', $request->filterCategory)
                ->select('category_name', 'category_parent')
                ->first()
                ->toArray();
            // dd($data_category);
            $arr_event_id = PvtAssessmentEvent::where('event_id', $request->filterEvent)
                ->where('category', ($data_category['category_parent'] == 'IDEA BOX') ? 'IDEA' : 'BI/II')
                ->where('status_point', 'active')
                ->select('id', 'pdca', 'point')
                ->get()
                ->toArray();
            // dd($arr_event_id[0]['id']);
            $arr_select_case = [
                DB::raw('MIN(teams.id) as team_id'),
                DB::raw('MIN(team_name) as team_name'),
                'pvt_event_teams.id AS event_team_id(removed)',
                'pvt_event_teams.status AS status_event_team',
            ];
            if (count($arr_event_id)) {
                $arr_select_case[] = DB::raw("ROUND(ROUND(SUM(pvt_assesment_team_judges.score), 2) / COUNT(CASE WHEN pvt_assesment_team_judges.assessment_event_id = '" . $arr_event_id[0]['id'] . "' THEN pvt_assesment_team_judges.assessment_event_id END), 2) AS \"Total(removed)\"");
            }
            $data_row = Team::join('papers', 'papers.team_id', '=', 'teams.id')
                ->join('categories', 'categories.id', '=', 'teams.category_id')
                ->join('themes', 'themes.id', '=', 'teams.theme_id')
                ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
                ->join('pvt_assesment_team_judges', 'pvt_assesment_team_judges.event_team_id', '=', 'pvt_event_teams.id')
                ->join('pvt_assessment_events', function ($join) {
                    $join->on('pvt_assessment_events.id', '=', 'pvt_assesment_team_judges.assessment_event_id');
                    //  ->on('pvt_assessment_events.year', '=', 'pvt_event_teams.year');
                })
                ->where('categories.id', $request->filterCategory)
                ->where('pvt_event_teams.event_id', $request->filterEvent)
                ->whereIn('pvt_event_teams.status', $request->filterStatus)
                ->where('pvt_assessment_events.status_point', 'active')
                // ->where('pvt_assessment_events.year', $request->filterYear)
                ->groupBy('pvt_event_teams.id')
                ->select($arr_select_case);


            $dataTable = DataTables::of($data_row->get());

            $rawColumns[] = 'Total';
            $dataTable->addColumn('Total', function ($data_row) {
                return '<input class="form-control" style="width: 150px; type="text" id="text-' . $data_row['event_team_id(removed)'] . '" name="total_score_event[]" value="' . $data_row['Total(removed)'] . '" readonly>';
            });

            // $rawColumns[] = 'action';
            // $dataTable->addColumn('action', function ($data_row) {
            //         return '<input class="form-check" type="checkbox" id="checkbox-'. $data_row['event_team_id(removed)'] .'" name="pvt_event_team_id[]" value="'. $data_row['event_team_id(removed)'] .'">';
            // });
            $rawColumns[] = 'opsi';
            $dataTable->addColumn('opsi', function ($data_row) {
                $optionsColumn = '';

                // Mengecek kondisi status_event_team
                if ($data_row->status_event_team === "Caucus") {
                    // Menambahkan kolom opsi sesuai dengan kebutuhan
                    $optionsColumn = '<button class="btn btn-cyan btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#executiveSummary" onclick="setSummary(' . $data_row['team_id'] . ')">Submit</button>';
                } else {
                    $optionsColumn = '-';
                }

                return $optionsColumn;
            });

            $dataTable->rawColumns($rawColumns);

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
    public function getUser(Request $request)
    {
        $data = User::pluck('name', 'id'); // Sesuaikan dengan model dan kolom yang sesuai

        return response()->json(['data' => $data]);
    }


    public function get_caucus(Request $request)
    {
        try {
            $data_category = Category::select('id', 'category_name', 'category_parent');
            if (!is_null($request->filterCategory) || $request->filterCategory != "") {
                $data_category->where('id', $request->filterCategory);
            } else {
                $data_category->where('category_parent', '<>', 'IDEA BOX');
            }
            $data_category = $data_category->get()
                ->toArray();

            $categoryid = array_column($data_category, 'id');
            $searchidea = array_column($data_category, 'category_parent');
            if (in_array("IDEA BOX", $searchidea)) {
                $category_parent = 'IDEA';
            } else {
                $category_parent = 'BI/II';
            }
            $arr_event_id = PvtAssessmentEvent::where('event_id', $request->filterEvent)
                ->where('category', $category_parent)
                ->where('status_point', 'active')
                ->where('stage', 'presentation')
                ->select('id', 'pdca', 'point')
                ->get()
                ->toArray();
            $arr_select_case = [
                DB::raw('MIN(team_name) as Tim'),
                DB::raw('MIN(innovation_title) as Judul'),
                DB::raw('MIN(category_name) as Kategori'),
                DB::raw('MIN(theme_name) as Tema'),
                DB::raw('MIN(inovasi_lokasi) as Lokasi'),
                'pvt_event_teams.id AS event_team_id(removed)',
                'pvt_event_teams.total_score_presentation as Hasil Penilaian Presentasi',
                'pvt_event_teams.status as status(removed)',
                'pvt_event_teams.team_id as team_id',
            ];


            if (count($arr_event_id)) {
                $arr_select_case[] = DB::raw("MIN(pvt_assesment_team_judges.score) as \"score_kosong(removed)\"");
            }

            $data_row = Team::join('papers', 'papers.team_id', '=', 'teams.id')
                ->join('categories', 'categories.id', '=', 'teams.category_id')
                ->join('themes', 'themes.id', '=', 'teams.theme_id')
                ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
                ->join('pvt_assesment_team_judges', 'pvt_assesment_team_judges.event_team_id', '=', 'pvt_event_teams.id')
                ->join('pvt_assessment_events', function ($join) {
                    $join->on('pvt_assessment_events.id', '=', 'pvt_assesment_team_judges.assessment_event_id');
                })
                ->whereIn('categories.id', $categoryid)
                ->where('pvt_event_teams.event_id', $request->filterEvent)
                ->where('pvt_event_teams.status', '!=', 'tidak lolos Presentation')
                ->where('pvt_event_teams.status', '!=', 'tidak lolos On Desk')
                ->where('pvt_assessment_events.status_point', 'active')
                ->where('pvt_assesment_team_judges.stage', 'caucus')
                ->whereNotIn('papers.status_event', ['reject_group', 'reject_national', 'reject_international']);

            if (auth()->user()->role == "Juri" || $this->checkIfJudge()) {
                $data_row->join("judges", 'judges.id', '=', 'pvt_assesment_team_judges.judge_id')
                    ->where('judges.employee_id', auth()->user()->employee_id);
            }
            $data_row->groupBy('pvt_event_teams.id')
                ->select($arr_select_case);

            $dataTable = DataTables::of($data_row->get());
            $rawColumns[] = 'Total';
            $dataTable->addColumn('Total', function ($data_row) use ($arr_event_id) {
                // Mengambil nilai total_score_caucus berdasarkan event_team_id
                $data_total = pvtEventTeam::where('id', $data_row['event_team_id(removed)'])
                    ->select('total_score_caucus') // Ambil hanya kolom total_score_caucus
                    ->first(); // Ambil data pertama (seharusnya hanya ada satu hasil)

                // Cek apakah data_total tidak null
                if ($data_total) {
                    // Kembalikan input dengan nilai dari total_score_caucus
                    return $data_total->total_score_caucus;
                }

                // Jika tidak ada, kembalikan input kosong
                return '';
            });


            $rawColumns[] = 'Ranking';
            $dataTable->addColumn('Urutan', function ($data_row) use ($request, $categoryid) {

                // Mengambil data tim berdasarkan total_score_caucus per kategori
                $data_total = pvtEventTeam::join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                    ->join('categories', 'categories.id', '=', 'teams.category_id')
                    ->where('pvt_event_teams.event_id', $request->filterEvent)  // Filter berdasarkan event
                    ->whereIn('categories.id', $categoryid)                     // Filter berdasarkan kategori
                    ->whereNotNull('pvt_event_teams.total_score_caucus')  // Mengecualikan yang null
                    ->groupBy('pvt_event_teams.id', 'categories.id')            // Kelompokkan berdasarkan kategori
                    ->select(
                        DB::raw("DENSE_RANK() OVER (PARTITION BY categories.id ORDER BY pvt_event_teams.total_score_caucus DESC) AS \"Ranking\""), // Menghitung ranking per kategori
                        'pvt_event_teams.id as id',
                        'pvt_event_teams.total_score_caucus',
                        'categories.id as category_id'
                    )  // Menambahkan kategori ke dalam hasil
                    ->get()
                    ->keyBy('id')  // Ubah hasil query menjadi key-value pair dengan id sebagai key
                    ->toArray();



                // Cek apakah total_score_presentation null atau 0
                $eventTeamId = $data_row['event_team_id(removed)'];
                if (!isset($data_total[$eventTeamId]) || $data_total[$eventTeamId]['total_score_caucus'] == 0) {
                    return 'belum dinilai'; // Kembalikan jika belum ada penilaian atau nilai 0
                }

                // Kembalikan ranking untuk event_team_id saat ini
                return $data_total[$eventTeamId]['Ranking'];
            });

            $rawColumns[] = 'fix';
            $dataTable->addColumn('fix', function ($data_row) {
                if (auth()->user()->role === 'Admin' | auth()->user()->role === 'Superadmin' && $data_row['status(removed)'] === 'Caucus')
                    return '<input class="form-check" type="checkbox" id="checkbox-' . $data_row['event_team_id(removed)'] . '" name="pvt_event_team_id[]" value="' . $data_row['event_team_id(removed)'] . '">';
                else
                    return '-';
            });


            $rawColumns[] = 'action';
            $dataTable->addColumn('action', function ($data_row) {
                $inputPenilaianUrl = route('assessment.juri.value.caucus', ['id' => $data_row['event_team_id(removed)']]);
                $lihatSofiUrl = route('assessment.show.sofi.caucus', ['id' => $data_row['event_team_id(removed)']]);

                // Mengecek peran pengguna dan status peserta
                if (auth()->user()->role == 'Admin' || auth()->user()->role == 'Superadmin') {
                    $nextStepButton = $data_row['score_kosong(removed)'] == 0 ?
                        "<a class=\"btn btn-primary btn-xs mb-2\" href=\"$inputPenilaianUrl\">Pengaturan Juri</a>" :
                        "<a class=\"btn btn-primary btn-xs mb-2\" href=\"$inputPenilaianUrl\">Pengaturan Juri</a>";

                    return "$nextStepButton <a class=\"btn btn-info btn-xs " . ($data_row['status(removed)'] == 'Caucus' ? 'disabled' : '') . "\"\" href=\"$lihatSofiUrl\">Lihat SOFI</a>";
                } elseif (auth()->user()->role == 'Juri' || $this->checkIfJudge()) {
                    $inputPenilaianButton = "<a class=\"btn btn-primary btn-xs mb-2\" href=\"$inputPenilaianUrl\">Input Penilaian</a>";
                    return "$inputPenilaianButton <a class=\"btn btn-info btn-xs  " . ($data_row['status(removed)'] == 'Caucus' ? 'disabled' : '') . "\" href=\"$lihatSofiUrl\">Lihat SOFI</a>";
                } else {
                    // Jika bukan admin, superadmin, atau juri, hanya menampilkan tombol Lihat SOFI
                    return "<a class=\"btn btn-info btn-xs  " . ($data_row['status(removed)'] == 'Caucus' ? 'disabled' : '') . "\" href=\"$lihatSofiUrl\">Lihat SOFI</a>";
                }
            });

            $rawColumns[] = 'Summary';
            $dataTable->addColumn('Summary', function ($data_row) {
                $summaryExists = SummaryExecutive::where('pvt_event_teams_id', $data_row['event_team_id(removed)'])->exists();
                if ($summaryExists) {
                    return '<button class="btn btn-cyan btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#executiveSummary" onclick="setSummary(' . $data_row['team_id'] . ',' . $data_row['event_team_id(removed)'] . ')">Edit summary</button>';
                }
                return '<button class="btn btn-cyan btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#executiveSummary" onclick="setSummary(' . $data_row['team_id'] . ',' . $data_row['event_team_id(removed)'] . ')">Summary</button>';
            });


            $dataTable->rawColumns($rawColumns);

            $remove_column = [];
            foreach ($dataTable->original as $data_column) {
                foreach ($data_column->getAttributes() as $column => $value) {
                    if (strstr($column, "removed") !== false || $column === "team_id") {
                        $remove_column[] = $column;
                    }
                }
            }
            $dataTable->removeColumn($remove_column);

            return $dataTable->addIndexColumn()->toJson();
        } catch (Exception $e) {
            Log::debug($e);
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function get_presentasi_bod(Request $request)
    {
        try {

            $data_category = Category::select('id', 'category_name', 'category_parent');
            if (!is_null($request->filterCategory) || $request->filterCategory != "") {
                $data_category->where('id', $request->filterCategory);
            } else {
                $data_category->where('category_parent', '<>', 'IDEA BOX');
                // $data_category->where('id', 2);
            }
            $data_category = $data_category->get()
                ->toArray();

            $categoryid = array_column($data_category, 'id');

            $searchidea = array_column($data_category, 'category_parent');
            if (in_array("IDEA BOX", $searchidea)) {
                $category_parent = 'IDEA';
            } else {
                $category_parent = 'BI/II';
            }
            // dd($data_category);
            $arr_event_id = PvtAssessmentEvent::where('event_id', $request->filterEvent)
                // ->where('category', ($data_category['category_parent'] == 'IDEA BOX') ? 'IDEA' : 'BI/II')
                ->where('status_point', 'active')
                ->select('id', 'pdca', 'point')
                ->get()
                ->toArray();

            // dd($arr_event_id[0]['id']);
            $arr_select_case = [
                DB::raw('MIN(teams.id) as "team_id(removed)"'),
                DB::raw('MIN(team_name) as Tim'),
                DB::raw('MIN(innovation_title) as Judul'),
                DB::raw('MIN(category_name) as Kategori'),
                DB::raw('MIN(theme_name) as Tema'),
                DB::raw('MIN(val_peringkat) as "val_peringkat(removed)"'),
                DB::raw('MIN(summary_executives.file_ppt) AS "file_ppt(removed)"'),
                'pvt_event_teams.id AS event_team_id(removed)',
                'pvt_event_teams.status AS status',
            ];

            $data_row = Team::join('papers', 'papers.team_id', '=', 'teams.id')
                ->join('categories', 'categories.id', '=', 'teams.category_id')
                ->join('themes', 'themes.id', '=', 'teams.theme_id')
                ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
                ->leftJoin('keputusan_bods', 'keputusan_bods.pvt_event_teams_id', '=', 'pvt_event_teams.id')
                ->join('pvt_assesment_team_judges', 'pvt_assesment_team_judges.event_team_id', '=', 'pvt_event_teams.id')
                ->join('pvt_assessment_events', function ($join) {
                    $join->on('pvt_assessment_events.id', '=', 'pvt_assesment_team_judges.assessment_event_id');
                })
                ->join('summary_executives', 'pvt_event_teams.id', '=', 'summary_executives.pvt_event_teams_id')
                ->where('pvt_event_teams.event_id', $request->filterEvent)
                ->whereIn('pvt_event_teams.status', ['Presentation BOD', 'Juara'])
                ->where('pvt_assesment_team_judges.stage', 'caucus')
                ->whereNotIn('papers.status_event', ['reject_group', 'reject_national', 'reject_international'])
                ->whereIn('teams.category_id', $categoryid)
                ->groupBy('pvt_event_teams.id')
                ->select($arr_select_case);




            $dataTable = DataTables::of($data_row->get());

            $rawColumns[] = 'Ranking';
            $dataTable->addColumn('Ranking', function ($data_row) use ($request, $categoryid) {
                $data_total = pvtEventTeam::join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                    ->join('categories', 'categories.id', '=', 'teams.category_id')
                    ->join('pvt_assesment_team_judges', 'pvt_assesment_team_judges.event_team_id', '=', 'pvt_event_teams.id')
                    ->where('pvt_event_teams.event_id', $request->filterEvent)  // Filter berdasarkan event
                    ->whereIn('pvt_event_teams.status', ['Presentation BOD', 'Juara'])
                    ->where('pvt_assesment_team_judges.stage', 'caucus')
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
                if (!isset($data_total[$eventTeamId]) || $data_total[$eventTeamId]['final_score'] == 0) {
                    return 'belum dinilai'; 
                }

                // Kembalikan ranking untuk event_team_id saat ini
                return $data_total[$eventTeamId]['Ranking'];
            });

            $rawColumns[] = 'Nilai akhir keputusan BOD';
            $dataTable->addColumn('Nilai akhir keputusan BOD', function ($data_row) {
                $pvtEventTeam = PvtEventTeam::find($data_row['event_team_id(removed)']);
                $divStart = '<div class="d-flex flex-column align-items-center">';
                $divEnd = '</div>';
                $isAdmin = Auth::user()->role === "Admin" || Auth::user()->role === "Superadmin" ? '' : 'readonly';


                if ($pvtEventTeam->final_score !== null) {
                    $finalScore = '<input style="border: 1px dotted #e1e1e1;" 
                                        class="form-control small-input text-center mb-2" 
                                        type="text" 
                                        id="text-' . $data_row['event_team_id(removed)'] . '" 
                                        name="total_score_event[]" 
                                        value="' . $pvtEventTeam->final_score . '" 
                                        readonly>';

                    return $divStart . 
                            $finalScore . 
                            '<form class="d-flex flex-column align-items-center" 
                                method="post" 
                                action="' . route('assessment.updateScoreKeputusanBOD') . '">
                                ' . csrf_field() . '
                                ' . method_field('PUT') . '
                                <input type="hidden" value=\'' . htmlspecialchars(json_encode($data_row), ENT_QUOTES) . '\' 
                                    name="selected_data_team" />
                                <input class="form-control small-input mb-2 text-center" 
                                    value="0" 
                                    type="number" 
                                    name="val_peringkat" 
                                    min="0" 
                                    max="1000" '. $isAdmin .'>
                                ' . ((Auth::user()->role === "Superadmin" || Auth::user()->role === "Admin") ? 
                                    '<button type="submit" class="btn btn-sm btn-primary">Submit</button>' : '') . '
                            </form>' . 
                            $divEnd;
                } else {
                    $totalScore = '<input style="border: 1px dotted #e1e1e1;" 
                                        class="form-control small-input text-center mb-2" 
                                        type="text" 
                                        id="text-' . $data_row['event_team_id(removed)'] . '" 
                                        name="total_score_event[]" 
                                        value="' . $pvtEventTeam->total_score_caucus . '" 
                                        readonly>';

                    return $divStart . 
                            $totalScore . 
                            '<form class="d-flex flex-column align-items-center" 
                                method="post" 
                                action="' . route('assessment.updateScoreKeputusanBOD') . '">
                                ' . csrf_field() . '
                                ' . method_field('PUT') . '
                                <input type="hidden" value=\'' . htmlspecialchars(json_encode($data_row), ENT_QUOTES) . '\' 
                                    name="selected_data_team" />
                                <input class="form-control small-input mb-2 text-center" 
                                    value="0" 
                                    type="number" 
                                    name="val_peringkat" 
                                    min="0" 
                                    max="1000" '. $isAdmin .'>
                                ' . ((Auth::user()->role === "Superadmin" || Auth::user()->role === "Admin") ? 
                                    '<button type="submit" class="btn btn-sm btn-primary">Submit</button>' : '') . '
                            </form>' . 
                            $divEnd;
                }

            });

            $rawColumns[] = 'Fiks';
            $dataTable->addColumn('Fiks', function ($data_row) {
                if (auth()->user()->role === 'Admin' | auth()->user()->role === 'Superadmin' && $data_row['status'] === 'Presentation BOD')
                    return '<input class="form-check" type="checkbox" id="checkbox-' . $data_row['event_team_id(removed)'] . '" name="pvt_event_team_id[]" value="' . $data_row['event_team_id(removed)'] . '">';
                else
                    return '-';
            });



            $rawColumns[] = 'Summary';
            $dataTable->addColumn('Summary', function ($data_row) {
                $filePath = $data_row['file_ppt(removed)'];

                // Generate the file URL

                if ($filePath !== null) {
                    $fileUrl = Storage::url($filePath);
                    return '<button class="btn btn-green btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#executiveSummaryPPT" onclick="setSummaryPPT(' . $data_row['event_team_id(removed)'] . ')"><i class="fa fa-edit" aria-hidden="true"></i>&nbsp;Edit Summary</button>'
                        . '&nbsp; <p> <a href="' . route('assessment.executiveSummary', ['eventTeamId' => $data_row['event_team_id(removed)']]) . '" class="btn btn-info btn-sm" target="_blank"><i class="fa fa-eye" aria-hidden="true"></i>&nbsp;View PDF</a>';
                } else {
                    return '<button class="btn btn-cyan btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#executiveSummaryPPT" onclick="setSummaryPPT(' . $data_row['event_team_id(removed)'] . ')"><i class="fa fa-upload" aria-hidden="true"></i>&nbsp;Upload PDF</button>';
                }
            });

            $dataTable->rawColumns($rawColumns);

            $remove_column = [];
            foreach ($dataTable->original as $data_column) {
                foreach ($data_column->getAttributes() as $column => $value) {
                    if (strstr($column, "removed") !== false || $column === 'status') {
                        $remove_column[] = $column;
                    }
                }
            }
            $dataTable->removeColumn($remove_column);

            return $dataTable->addIndexColumn()->toJson();
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }
    public function get_penetapan_juara(Request $request)
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
                DB::raw('MIN(team_name) as team_name'),
                DB::raw('MIN(innovation_title) as Judul'),
                DB::raw('MIN(category_name) as Kategori'),
                'pvt_event_teams.id AS event_team_id(removed)',
                'pvt_event_teams.is_best_of_the_best AS is_best_of_the_best',
                'pvt_event_teams.is_honorable_winner AS is_honorable_winner',
                DB::raw('MAX(pvt_event_teams.final_score) as final_score'),
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
                ->groupBy('pvt_event_teams.id', 'pvt_event_teams.is_best_of_the_best', 'pvt_event_teams.is_honorable_winner')
                ->select($arr_select_case);

            // Jika filterCategory tidak null, tambahkan filter untuk kategori
            if ($request->filterCategory) {
                $data_row->where('categories.id', $request->filterCategory);
            }

            // Urutkan berdasarkan MIN(category_name) dan final_score
            $dataTable = DataTables::of($data_row->orderBy(DB::raw('MIN(category_name)')) // Gunakan alias dari SELECT
                ->orderBy('final_score', 'desc') // Kemudian urutkan berdasarkan final_score
                ->get());

            $rawColumns[] = 'Tim'; // Allow HTML rendering in the Tim column
            $dataTable->addColumn('Tim', function ($data_row) {
                // Display the team name with a badge if "Best of the Best"
                $badge = $data_row->is_best_of_the_best ? "<span class='badge bg-success'><i class='fas fa-trophy'></i> Best of the Best</span>" : "";
                return $data_row->tim . " " . $badge; // Concatenate the badge with the team name
            });
            $dataTable->rawColumns($rawColumns);

            $rawColumns[] = 'Ranking';
            $dataTable->addColumn('Ranking', function ($data_row) use ($request, $categoryid) {
                // Query ranking per kategori
                $data_total = pvtEventTeam::join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                    ->join('categories', 'categories.id', '=', 'teams.category_id')
                    ->where('pvt_event_teams.event_id', $request->filterEvent)
                    ->whereIn('categories.id', $categoryid)
                    ->whereNotNull('pvt_event_teams.final_score')
                    ->groupBy('pvt_event_teams.id', 'categories.id', 'pvt_event_teams.final_score')
                    ->select(
                        DB::raw("DENSE_RANK() OVER (PARTITION BY categories.id ORDER BY pvt_event_teams.final_score DESC) AS \"Ranking\""),
                        'pvt_event_teams.id as id',
                        'pvt_event_teams.final_score',
                        'categories.id as category_id'
                    )
                    ->get()
                    ->keyBy('id')
                    ->toArray();
            
                // Ambil ID dan data status
                $eventTeamId = $data_row['event_team_id(removed)'] ?? $data_row['event_team_id'] ?? null;
                $isBest = $data_row['is_best_of_the_best'] ?? false;
                $isHonorable = $data_row['is_honarable_winner'] ?? false;
            
                // Default ranking string
                $ranking = $data_total[$eventTeamId]['Ranking'] ?? '-';
            
                // Tambahkan badge jika perlu
                if ($isBest) {
                    return "Best of the Best";
                } elseif ($isHonorable) {
                    return "Juara Harapan";
                } else {
                    return "Juara $ranking";
                }
            });

            // Menghapus kolom yang mengandung kata "removed"
            $remove_column = [];
            foreach ($dataTable->original as $data_column) {
                foreach ($data_column->getAttributes() as $column => $value) {
                    if (strstr($column, "removed") !== false || $column === "team_id" || $column === "is_best_of_the_best") {
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



    public function coba()
    {
        try {
            $users = User::all();
            $companies = Company::all()
                ->keyBy('company_name')
                ->toArray();

            $coba_arr = [];

            foreach ($users as $user) {
                if (!isset($companies[$user['company_name']]))
                    $coba_arr[] = $user['name'] . " - " . $user['company_name'];
            }
            return response()->json([
                'success' => "success",
                'data'  => $coba_arr,
                'company' => $companies,
                'paprr' => Team::where('company_code', "2000")->pluck('id')[0],
                'compan' => Company::where('company_name', 'PT. Semen Indonesia,Tbk')->pluck('company_code')
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }
    public function get_count_tim()
    {
        try {
            //code...
        } catch (Exception $e) {
            //throw $th;
        }
    }

    public function getFile(Request $request)
    {
        $directory = $request->input('directory');

        if (!$directory) {
            return response()->json(['error' => 'path harus diisi'], 401);
        }
        try {
            return response()->file(storage_path('app/public/' . $directory));
        } catch (FileNotFoundException $e) {
            return response()->json(['error' => 'File tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getMetodologiPapers(Request $request)
    {
        $search = $request->input('search');
        $query = MetodologiPaper::query();

        if (!empty($search)) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $results = $query->select('id', 'name', 'max_user')->limit(100)->get();

        return response()->json($results);
    }
}