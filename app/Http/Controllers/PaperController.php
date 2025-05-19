<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Team;
use App\Models\Category;
use App\Models\Theme;
use App\Models\Event;
use App\Models\User;
use App\Models\Paper;
use App\Models\PvtMember;
use App\Models\ph2Member;
use App\Models\Company;
use App\Models\Comment;
// use App\Models\PvtAssessmentTeam;
use App\Models\pvtAssesmentTeamJudge;
use App\Models\PvtEventTeam;
use App\Models\Evidence;
use App\Models\DocumentSupport;
use App\Models\EventExternal;
use App\Mail\EmailNotificationPaperFasil;
use App\Mail\EmailNotificationBenefit;
use App\Mail\EmailNotificationBenefitGM;
use App\Mail\EmailNotificationFinal;
use App\Mail\EmailApprovalPaperFasil;
use App\Mail\EmailApprovalBenefit;
use App\Mail\EmailApprovalFinal;
use App\Http\Requests\registerRequests;
use App\Http\Requests\updateTeamPaperRequests;
use App\Models\History;
use App\Models\Judge;
use App\Models\MetodologiPaper;
use App\Notifications\PaperNotification;
use setasign\Fpdi\Tcpdf\Fpdi;
use TCPDF;
use setasign\Fpdi\PdfParser\StreamReader;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class PaperController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $checkStatus = Auth::user()->role;
        if ($checkStatus == 'Admin') {
            $data_company = Company::where('company_code', Auth::user()->company_code)->get();
        } elseif ($checkStatus == 'Superadmin') {
            $data_company = Company::all();
        } else {
            $data_company = [];
        }
        $data_category = Category::all();
        $data_theme = Theme::all();
        $status_inovasi = Paper::distinct('status_inovasi')->pluck('status_inovasi');
        $userEmployeeId = Auth::user()->employee_id;
        $is_judge = Judge::where('employee_id', $userEmployeeId)->exists();

        return view('auth.user.paper.index', [
            'data_company' => $data_company,
            'data_category' => $data_category,
            'data_theme' => $data_theme,
            'status_inovasi' => $status_inovasi,
            'is_judge' => $is_judge,
        ]);
    }

    public function detail_paper($team_id)
    {
        $data_team = Team::findOrFail($team_id)
            ->toArray();
        $data_paper = Paper::where('team_id', $team_id)
            ->first()
            ->toArray();

        $file_proofidea = null;
        if ($data_paper['proof_idea']) {
            $file_proofidea = Storage::get('public/' . $data_paper['proof_idea']);
        }

        $file_innovationphoto = null;
        if ($data_paper['innovation_photo']) {
            $file_innovationphoto = Storage::get('public/' . $data_paper['innovation_photo']);
        }

        $data_paper['proof_idea'] = $file_proofidea;
        $data_paper['innovation_photo'] = $file_innovationphoto;

        // dd($data_paper);
        return view('auth.user.paper.detail_paper', [
            'data_team' => $data_team,
            'data_paper' => $data_paper
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function registerTeam(Request $request)
    {
        $datas_category = Category::all();
        $datas_theme = Theme::all();
        $datas_event = Event::all();
        $datas_user = User::all();
        $fasil = User::where('job_level', 'Band 2')
            ->orWhere('job_level', 'Band 1')
            ->select('employee_id', 'name', 'company_name')
            ->get();

        $datas_company = Company::all();
        $userEmployeeId = Auth::user()->employee_id;
        $is_judge = Judge::where('employee_id', $userEmployeeId)->exists();

        if ($request->input('nextevent') == 'External') {
            $nextEvent = $request->input('nextevent');
            $teamID = $request->input('team_id');
            $datas_member = PvtMember::join('users', 'users.employee_id', 'pvt_members.employee_id')
                ->where('team_id', $teamID)
                ->get()
                ->toArray();

            $datas_outsource = ph2Member::where('team_id', $teamID)
                ->get()
                ->toArray();

            foreach ($datas_outsource as $outsoure) {
                $outsoure['status'] = 'outsource';
                $outsoure['employee_id'] = $outsoure['ph2_id'];
                $datas_member[] = $outsoure;
            }

            // dd($datas_member);
            $dt_team = Team::join('papers', 'papers.team_id', '=', 'teams.id')
                ->join('companies', 'companies.company_code', 'teams.company_code')
                ->join('pvt_members', 'pvt_members.team_id', 'teams.id')
                ->where('teams.id', $teamID)
                ->select(
                    'teams.id as team_id',
                    'papers.id as paper_id',
                    'teams.team_name',
                    'papers.innovation_title',
                    'papers.inovasi_lokasi',
                    'pvt_members.id',
                    'pvt_members.status as status_member',
                    'companies.company_code as code_company',
                    'companies.company_name'
                )
                ->first();
            return view('auth.user.paper.external_event', [
                'datas_category' => $datas_category,
                'datas_theme' => $datas_theme,
                'datas_event' => $datas_event,
                'datas_user' => $datas_user,
                'datas_company' => $datas_company,
                'data_member' => $datas_member,
                'dt_team' => $dt_team,
                'nextEvent' => $nextEvent,
                'is_judge' => $is_judge,
            ]);
        } elseif ($request->input('nextevent') == 'Group') {
            $nextEvent = $request->input('nextevent');
            $teamID = $request->input('team_id');
            $datas_member = PvtMember::join('users', 'users.employee_id', 'pvt_members.employee_id')
                ->where('team_id', $teamID)
                ->get()
                ->toArray();

            $datas_outsource = ph2Member::where('team_id', $teamID)
                ->get()
                ->toArray();

            foreach ($datas_outsource as $outsoure) {
                $outsoure['status'] = 'outsource';
                $outsoure['employee_id'] = $outsoure['ph2_id'];
                $datas_member[] = $outsoure;
            }

            // dd($datas_member);
            $dt_team = Team::join('papers', 'papers.team_id', '=', 'teams.id')
                ->join('companies', 'companies.company_code', 'teams.company_code')
                ->join('pvt_members', 'pvt_members.team_id', 'teams.id')
                ->join('categories', 'categories.id', '=', 'teams.category_id')
                ->join('themes', 'themes.id', '=', 'teams.theme_id')
                ->where('teams.id', $teamID)
                ->select(
                    'teams.id as team_id',
                    'papers.id as paper_id',
                    'teams.team_name',
                    'papers.innovation_title',
                    'papers.inovasi_lokasi',
                    'categories.id as category_id',
                    'themes.id as theme_id',
                    'categories.category_name as category_name',
                    'themes.theme_name as theme_name',
                    'companies.company_code as code_company',
                    'companies.company_name'
                )
                ->first()
                ->toArray();

            // dd($datas_member);
            return view('auth.user.paper.register_group', [
                'datas_category' => $datas_category,
                'datas_theme' => $datas_theme,
                'datas_event' => $datas_event,
                'datas_user' => $datas_user,
                'data_member' => $datas_member,
                'dt_team' => $dt_team,
                'nextEvent' => $nextEvent,
                'is_judge' => $is_judge,
            ]);
        } else {
            return view('auth.user.paper.register', [
                'datas_category' => $datas_category,
                'datas_theme' => $datas_theme,
                'datas_event' => $datas_event,
                'datas_user' => $datas_user,
                'fasil' => $fasil,
                'is_judge' => $is_judge,
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Team $newTeam, registerRequests $request)
    {
        try {
            $now = Carbon::now();
            // Memulai transaksi
            DB::beginTransaction();
            $teamCreatedAt = $request->input('team_created_at') ? Carbon::parse($request->input('team_created_at')) : $now;

            $newTeam = Team::create([
                'team_name' => $request->input('team_name'),
                'company_code' => Company::where('company_name', $request->input('company'))->pluck('company_code')[0],
                'category_id' => $request->input('category'),
                'theme_id' => $request->input('theme'),
                'phone_number' => $request->input('phone_number'),
                'status_lomba' => $request->input('status_lomba'),
                'inovasi_lokasi' => $request->input('inovasi_lokasi'),
                'created_at' => $teamCreatedAt,
            ]);

            History::create([
                'team_id' => $newTeam->id,
                'activity' => "Membentuk Tim " . $newTeam->team_name,
                'status' => 'created'
            ]);
            
            $step = MetodologiPaper::where('id', $request->input('metodologi_paper_id'))->pluck('step')[0];
            if ($step < 8) {

                Paper::create([
                    'innovation_title' => $request->input('innovation_title'),
                    'inovasi_lokasi' => $request->input('inovasi_lokasi'),
                    'team_id' => $newTeam->id,
                    'abstract' => $request->input('abstract'),
                    'problem' => $request->input('problem'),
                    'status_inovasi' => $request->input('status_inovasi'),
                    'problem_impact' => $request->input('problem_impact'),
                    'main_cause' => $request->input('main_cause'),
                    'solution' => $request->input('solution'),
                    'outcome' => $request->input('outcome'),
                    'performance' => $request->input('performance'),
                    'proof_idea' => $request->file('proof_idea')->storeAs(
                        'internal/' . $request->input('status_lomba') . '/' . $request->input('team_name') . '/proof_idea',
                        Str::slug(pathinfo($request->file('proof_idea')->getClientOriginalName(), PATHINFO_FILENAME)) . "." . $request->file('proof_idea')->getClientOriginalExtension(),
                        'public'
                    ),
                    'innovation_photo' => $request->file('innovation_photo')->storeAs(
                        'internal/' . $request->input('status_lomba') . '/' . $request->input('team_name') . '/innovation_photo',
                        Str::slug(pathinfo($request->file('innovation_photo')->getClientOriginalName(), PATHINFO_FILENAME)) . "." . $request->file('innovation_photo')->getClientOriginalExtension(),
                        'public'
                    ),
                    'metodologi_paper_id' => $request->input('metodologi_paper_id'),
                    'step_8' => '-',
                    'created_at' => $teamCreatedAt,
                ]);
            } else {
                // dd($request->input('inovasi_lokasi'));
                Paper::create([
                    'innovation_title' => $request->input('innovation_title'),
                    'inovasi_lokasi' => $request->input('inovasi_lokasi'),
                    'team_id' => $newTeam->id,
                    'abstract' => $request->input('abstract'),
                    'problem' => $request->input('problem'),
                    'status_inovasi' => $request->input('status_inovasi'),
                    'problem_impact' => $request->input('problem_impact'),
                    'main_cause' => $request->input('main_cause'),
                    'solution' => $request->input('solution'),
                    'outcome' => $request->input('outcome'),
                    'performance' => $request->input('performance'),
                    'proof_idea' => $request->file('proof_idea')->storeAs(
                        'internal/' . $request->input('status_lomba') . '/' . $request->input('team_name') . '/proof_idea',
                        Str::slug(pathinfo($request->file('proof_idea')->getClientOriginalName(), PATHINFO_FILENAME)) . "." . $request->file('proof_idea')->getClientOriginalExtension(),
                        'public'
                    ),
                    'innovation_photo' => $request->file('innovation_photo')->storeAs(
                        'internal/' . $request->input('status_lomba') . '/' . $request->input('team_name') . '/innovation_photo',
                        Str::slug(pathinfo($request->file('innovation_photo')->getClientOriginalName(), PATHINFO_FILENAME)) . "." . $request->file('innovation_photo')->getClientOriginalExtension(),
                        'public'
                    ),
                    'metodologi_paper_id' => $request->input('metodologi_paper_id'),
                    'created_at' => $teamCreatedAt,
                ]);
            }

            PvtMember::create([
                'team_id' => $newTeam->id,
                'employee_id' => $request->input('fasil'),
                'status' => 'facilitator'
            ]);

            PvtMember::create([
                'team_id' => $newTeam->id,
                'employee_id' => $request->input('leader'),
                'status' => 'leader'
            ]);

            if ($request->input('anggota') != null) {
                foreach ($request->input('anggota') as $input_anggota) {
                    // echo $input_anggota;
                    PvtMember::create([
                        'team_id' => $newTeam->id,
                        'employee_id' => $input_anggota,
                        'status' => 'member'
                    ]);
                }
            }

            if ($request->input('anggota_outsource') != null) {
                foreach ($request->input('anggota_outsource') as $input_anggota_outsource) {
                    if ($input_anggota_outsource != null) {
                        $new_ph2 = ph2Member::create([
                            'name' => $input_anggota_outsource,
                            'team_id' => $newTeam->id
                        ]);

                        ph2Member::where('id', $new_ph2->id)->update([
                            'ph2_id' => "ph2-" .  $now->year . $now->month . $now->day . "-" . $new_ph2->id
                        ]);
                    }
                }
            }
            DB::commit();

            // Mengirim notifikasi
            $user = Auth::user();
            $user->notify(new PaperNotification(
                'Team Created',
                'New Team ' . $request->input('team_name') . ' has been created.',
                route('paper.index')
            ));
            $user->notify(new PaperNotification(
                'Paper Created',
                'New Paper ' . $request->input('innovation_title') . ' has been created.',
                route('paper.index')
            ));
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('paper.index')->withErrors('Error: ' . $e->getMessage());
        }

        return redirect()->route('paper.index')->with('success', 'Registrasi telah berhasil! Selamat Berinovasi'); 
    }

    public function storeEventExternal(Request $request)
    {
        try {
            $now = Carbon::now();

            DB::beginTransaction();

            $newTeam = Team::create([
                'team_name' => $request->input('team_name'),
                'company_code' => $request->input('company'),
                'phone_number' => $request->input('phone_number'),
                'status_lomba' => $request->input('status_lomba')
            ]);

            PvtMember::create([
                'team_id' => $newTeam->id,
                'employee_id' => $request->input('fasilitator'),
                'status' => 'facilitator'
            ]);

            PvtMember::create([
                'team_id' => $newTeam->id,
                'employee_id' => $request->input('leader'),
                'status' => 'leader'
            ]);

            if ($request->input('anggota') != null) {
                foreach ($request->input('anggota') as $input_anggota) {
                    PvtMember::create([
                        'team_id' => $newTeam->id,
                        'employee_id' => $input_anggota,
                        'status' => 'member'
                    ]);
                }
            }

            EventExternal::create([
                'team_id' => $newTeam->id,
                'innovation_title' => $request->innovation_title,
                'inovasi_lokasi' => $request->inovasi_lokasi,
                'file_paper' => $request->file('file_paper')->storeAs(
                    'external/' . $request->input('status_lomba') . '/' . $request->input('team_name') . '/paper',
                    Str::slug(pathinfo($request->file('file_paper')->getClientOriginalName(), PATHINFO_FILENAME)) . "." . $request->file('file_paper')->getClientOriginalExtension(),
                    'public'
                ),
                'video' => $request->file('video')->storeAs(
                    'external/' . $request->input('status_lomba') . '/' . $request->input('team_name') . '/video',
                    Str::slug(pathinfo($request->file('video')->getClientOriginalName(), PATHINFO_FILENAME)) . "." . $request->file('video')->getClientOriginalExtension(),
                    'public'
                ),
                'ppt' => $request->file('ppt')->storeAs(
                    'external/' . $request->input('status_lomba') . '/' . $request->input('team_name') . '/ppt',
                    Str::slug(pathinfo($request->file('ppt')->getClientOriginalName(), PATHINFO_FILENAME)) . "." . $request->file('ppt')->getClientOriginalExtension(),
                    'public'
                ),
            ]);

            if ($request->input('anggota_outsource') != null) {
                foreach ($request->input('anggota_outsource') as $input_anggota_outsource) {
                    if ($input_anggota_outsource != null) {
                        $new_ph2 = ph2Member::create([
                            'name' => $input_anggota_outsource,
                            'team_id' => $newTeam->id
                        ]);

                        ph2Member::where('id', $new_ph2->id)->update([
                            'ph2_id' => "ph2-" .  $now->year . $now->month . $now->day . "-" . $new_ph2->id
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('paper.index')->with('success', 'Register successful');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('paper.index')->withErrors('Error: ' . $e->getMessage());
        }
    }

    // function stage 1-8
    public function createStages($id, $stage) //add
    {
        $array_stage = ['Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan'];
        $ket_step = ['Menentukan Tema dan Judul', 'Menganalisis Penyebab', 'Menentukan Solusi', 'Merencanakan Perbaikan', 'Menyusun & Melaksanakan Perbaikan', 'Mengevaluasi Solusi', 'Membuat Standar Baru', ''];

        $urut = explode("_", $stage)[1];
        $ket = explode("_", $stage)[1];
        $step = 'step_' . $urut;

        $urut = $array_stage[$urut - 1];
        $ket = $ket_step[$ket - 1];
        //$keterangan = isset($ket_step[$urut-1]) ? $ket_step[$urut-1] : '';
        $item = Paper::findOrFail($id);
        $paper_metodologi = MetodologiPaper::findOrFail($item->metodologi_paper_id);

        return view('auth.user.paper.stage.stage', [
            'stage' => $step,
            'urut' => $urut,
            'item' => $item,
            'ket' => $ket,
            'methodology' => $paper_metodologi->name,
        ]);
    }

    // Jika input sendiri
    public function storeStages(Request $request, $id, $stage)
    {
        try {
            $request->validate([
                'step' => 'file|max:30720',
            ]);

            $paper = Paper::with('metodologiPaper')->findOrFail($id);
            $team = Team::findOrFail($paper->team_id);

            $tcpdf = new TCPDF();
            $tcpdf->AddPage();
            $tcpdf->WriteHTML($request->step);

            $relativePath = 'public/internal/' . $team->status_lomba . '/' . $team->team_name;
            $storageFullPath = storage_path('app/' . $relativePath);
            if (!file_exists($storageFullPath)) {
                mkdir($storageFullPath, 0777, true);
            }
            $filePath = $storageFullPath . '/' . $stage . '.pdf';
            
            $tcpdf->Output($filePath, 'F');
            $paper->updateAndHistory([
                $stage => "w: " . $request->step
            ]);
            $stageNumber = preg_replace('/\D/', '', $stage);
            $stageNumberToInteger = (int) $stageNumber;
            if ($stageNumberToInteger === $paper->metodologiPaper->step && $this->isAllStepComplete($id)) {
                $team = Team::findOrFail($paper->team_id);

                // / Pastikan relasi Team sudah dimuat dengan benar
                if ($paper->team) {
                    $fasilId = PvtMember::where('team_id', $paper->team->id)
                        ->where('status', 'facilitator')
                        ->pluck('employee_id')
                        ->first();

                    $fasilData = User::where('employee_id', $fasilId)
                        ->select('name', 'email')
                        ->first();

                    $leaderId = PvtMember::where('team_id', $paper->team->id)
                        ->where('status', 'leader')
                        ->pluck('employee_id')
                        ->first();

                    $leaderData = User::where('employee_id', $leaderId)
                        ->select('name', 'email')
                        ->first();

                    $inovasi_lokasi = Paper::where('id', $id)
                        ->select('inovasi_lokasi')
                        ->first();

                    // Membuat objek
                    $mail = new EmailNotificationPaperFasil(
                        $paper,
                        'full_paper',
                        $paper->innovation_title,
                        $paper->team->team_name,
                        $leaderData,
                        $fasilData,
                        $inovasi_lokasi
                    );

                    // Mengirim email ke fasilitator
                    Mail::to($fasilData->email)->send($mail);
                }
            }
            //  Kode di bawwah ini mengirimkan notifikasi ke user saat ini menggunakan class PaperNotification
            //  PaperNotification berfungsi untuk membuat notifikasi ketika user mengupdate data
            $user = Auth::user();
            $user->notify(new PaperNotification(
                'Data Updated',
                'Successfully added ' . $stage,
                route('paper.index')
            ));
            return redirect()->route('paper.index')->with('success', 'Data successfully updated!');
        } catch (\Exception $e) {;
            return redirect()->route('paper.index')->withErrors('Error: ' . $e->getMessage());
        }
    }

    private function isAllStepComplete($id)
    {
        $paper = Paper::findOrFail($id);

        if (!is_null($paper->full_paper)) {
            return true;
        }

        return false;
    }

    // Untuk upload file
    public function storeFileStages(Request $request, $id, $stage)
    {
        try {
            $request->validate([
                'file_stage' => 'required|file|mimes:pdf|max:30720',
            ]);

            $paper = Paper::findOrFail($id);
            $team = Team::findOrFail($paper->team_id);
            $paper->$stage = '';
            $paper->save();

            $paper->updateAndHistory([
                $stage => $request->file('file_stage')->storeAs(
                    'internal/' . $team->status_lomba . '/' . $team->team_name,
                    $stage . "." . $request->file('file_stage')->getClientOriginalExtension(),
                    'public'
                ),
            ]);

            if ($stage === 'full_paper') {
                $steps = [
                    'step_1',
                    'step_2',
                    'step_3',
                    'step_4',
                    'step_5',
                    'step_6',
                    'step_7',
                    'step_8',
                ];

                foreach ($steps as $step) {
                    $paper->$step = '-';
                }

                $paper->save();
            }

            $user = Auth::user();
            $user->notify(new PaperNotification(
                'Paper Updated',
                'Full paper successfully updated',
                route('paper.index')
            ));

            return redirect()->route('paper.index')->with('success', 'Full Paper berhasil diunggah dan diperbarui!');
        } catch (\Exception $e) {
            return redirect()
                ->route('paper.index')
                ->withErrors('Error: ' . $e->getMessage());
        }
    }

    public function showStep($id, $stage)
    {
        try {
            $paper = Paper::findOrFail($id);
            $fpdi = new Fpdi();

            if ($stage == 'full') {
                $filePath = storage_path('app/public/' . ltrim(Paper::where('id', '=', $id)->pluck('full_paper')[0], '/'));
                if (!file_exists($filePath)) {
                    throw new Exception("Error, file tidak ada");
                }

                // Ambil Data User Saat Ini
                $currentDateTime = Carbon::now()->format('l, d F Y H:i:s');
                $userEmail = auth()->user()->email;
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

                return response($fpdi->Output($paper->innovation_title, 'I'), 200)->header('Content-Type', 'application/pdf');
            }

            $t = $paper->full_paper;
            if ($t) {
                return redirect()->route('paper.show.stages', [$id, $paper->innovation_title]);
            }

            $item = Paper::where('id', '=', $id)->select($stage)->get()[0];
            $team = Team::findOrFail($paper->team_id);

            foreach ($item->toArray() as $name_column => $column) {
                if ($column == null) {
                    continue;
                }

                // $tcpdf->AddPage();
                $fpdi->AddPage();
                if ($column[0] == 'w') {
                    $fullPath = storage_path('app/public/internal/' . $team->status_lomba . '/' . $team->team_name . '/' . $name_column . '.pdf');
                    if (!file_exists($fullPath)) {
                        $fullPath = storage_path('app/internal/' . $team->status_lomba . '/' . $team->team_name . '/' . $name_column . '.pdf');
                    }
                    // Ambil Data User Saat Ini
                    $currentDateTime = Carbon::now()->format('l, d F Y H:i:s');
                    $userEmail = Auth::user()->email;
                    $userIp = request()->ip();

                    $watermarkText = "{$currentDateTime}\nDilihat oleh {$userEmail}\nIP: {$userIp}";

                    $pageCount = $fpdi->setSourceFile($fullPath);
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
                    return response($fpdi->Output($fullPath, 'I'), 200)->header('Content-Type', 'application/pdf');

                } elseif ($column[0] == 'f') {
                    return $this->addWatermarks($id);

                } elseif ($column[0] == '-') {
                    return response("<script>alert('halo semuanya');</script>", 200)
                        ->header('Content-Type', 'text/html');
                }
            }

            return response($fpdi->Output(), 200)->header('Content-Type', 'application/pdf');
        } catch (\Exception $e) {
            // dd($e->getMessage());
            return redirect()->route('paper.index')->withErrors('Error: ' . $e->getMessage());
        }
    }

    public function storeBenefit(Request $request, $id)
    {
        // Menemukan record Paper dengan ID yang diberikan
        $record = Paper::with('team')->findOrFail($id);

        // Memperbarui record dengan data yang diberikan
        $record->financial = $request->financial;
        $record->potential_benefit = $request->potential_benefit;
        $record->non_financial = $request->non_financial;
        $record->potensi_replikasi = $request->input('potensi_replikasi');

        // Mengelola file review jika ada
        if ($request->file('file_review')) {
            $teamName = $record->team ? $record->team->team_name : 'unknown_team';
            $record->file_review = $request->file('file_review')->storeAs(
                'file_review',
                $teamName . '.' . $request->file('file_review')->extension(),
                'public'
            );
        }

        // Memperbarui status dan menyimpan perubahan
        $record->status = 'upload benefit';
        $record->updateAndHistory([], 'update benefit form');

        // Pastikan relasi Team sudah dimuat dengan benar
        if ($record->team) {
            // Mengambil ID fasilitator dan leader
            $fasilId = PvtMember::where('team_id', $record->team->id)
                ->where('status', 'facilitator')
                ->pluck('employee_id')
                ->first();

            $leaderId = PvtMember::where('team_id', $record->team->id)
                ->where('status', 'leader')
                ->pluck('employee_id')
                ->first();

            // Mengambil data fasilitator dan leader
            $fasilData = User::where('employee_id', $fasilId)
                ->select('name', 'email')
                ->first();

            $leaderData = User::where('employee_id', $leaderId)
                ->select('name', 'email')
                ->first();

            // Membuat objek EmailNotificationBenefit
            $mail = new EmailNotificationBenefit(
                $record,
                $record->status,
                $record->innovation_title,
                $record->team->team_name,
                $leaderData,
                $record->financial,
                $record->potential_benefit,
                $record->potensi_replikasi,
                $record->non_financial,
                $fasilData->name,
                $fasilData,
                $record->inovasi_lokasi
            );

            // Mengirim email ke fasilitator
            Mail::to($fasilData->email)->send($mail);

            $user = Auth::user();
            $user->notify(new PaperNotification(
                'Data Updated',
                'Success add benefit.',
                route('paper.index')
            ));
        } else {
            throw new \Exception('Paper tidak memiliki relasi dengan Team.');
        }

        return redirect()->route('paper.index')->with('success', 'Status Approval telah berhasil diperbarui');
    }

    public function approvePaper(Request $request, $id)
    {
        $paper = Paper::with('team')->findOrFail($id);
        $paper->status = 'accepted paper by facilitator';
        $paper->save();

        return redirect()->route('paper.approvePaperFasil', [$id, 'status' => 'accepted paper by facilitator']);
        //return redirect()->back()->with('success', 'Paper approved successfully!');
    }

    public function rejectPaper(Request $request, $id)
    {
        $paper = Paper::with('team')->findOrFail($id);
        $paper->status = 'rejected paper by facilitator';
        $paper->save();

        return redirect()->route('paper.approvePaperFasil', [$id, 'status' => 'rejected paper by facilitator']);
        //return redirect()->back()->with('success', 'Paper rejected successfully!');
    }

    public function approvePaperFasil(Request $request, $id)
    {
        try {
            $paper = Paper::with('team')->findOrFail($id);

            // Jika status adalah revisi
            if ($request->status === 'revision paper by facilitator') {
                if ($request->has('revision_steps')) {
                    // Revisi langkah: kosongkan langkah yang dipilih
                    foreach ($request->revision_steps as $step) {
                        $stepColumn = 'step_' . $step;
                        $paper->$stepColumn = null; // Set langkah ke null
                        $paper->full_paper = null;
                    }
                } elseif ($request->has('full_paper')) {
                    // Revisi full_paper: kosongkan full_paper
                    $paper->full_paper = null;
                }
            }

            // Set status paper
            $paper->status = $request->status;

            // Update paper dan tambahkan riwayat
            $paper->updateAndHistory([], $request->status);

            // Update atau buat komentar
            Comment::updateOrCreate(
                [
                    'paper_id' => $id,
                    'writer' => "facilitator on Paper",
                ],
                [
                    'comment' => $request->comment,
                ]
            );

            // Proses pengiriman email jika paper memiliki tim
            if ($paper->team) {
                $leaderId = PvtMember::where('team_id', $paper->team->id)
                    ->where('status', 'leader')
                    ->pluck('employee_id')
                    ->first();

                $leaderData = User::where('employee_id', $leaderId)
                    ->select('name', 'email')
                    ->first();

                $inovasi_lokasi = $paper->inovasi_lokasi;

                $mail = new EmailApprovalPaperFasil(
                    $paper,
                    $request->status,
                    $paper->innovation_title,
                    $paper->team->team_name,
                    $leaderData,
                    $inovasi_lokasi
                );

                Mail::to($leaderData->email)->send($mail);
            } else {
                throw new \Exception('Paper tidak memiliki relasi dengan Team.');
            }

            return redirect()->route('paper.index')->with('success', 'Status Approval telah berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->route('paper.index')->withErrors('Error: ' . $e->getMessage());
        }
    }


    public function approveBenefit(Request $request, $id)
    {
        $record = Paper::with('team')->findOrFail($id);
        //$paper = Paper::with('team')->findOrFail($id);
        //$paper->status = 'accepted benefit by facilitator';
        $record->status = 'accepted benefit by facilitator';
        //$paper->save();
        $record->save();

        return redirect()->route('paper.approveBenefitFasil', [$id, 'status' => 'accepted benefit by facilitator']);
        //return redirect()->back()->with('success', 'Paper approved successfully!');
    }

    public function rejectBenefit(Request $request, $id)
    {
        $record = Paper::with('team')->findOrFail($id);
        //$paper = Paper::with('team')->findOrFail($id);
        //$paper->status = 'rejected benefit by facilitator';
        $record->status = 'rejected benefit by facilitator';
        //$paper->save();
        $record->save();

        return redirect()->route('paper.approveBenefitFasil', [$id, 'status' => 'rejected benefit by facilitator']);
        //return redirect()->back()->with('success', 'Paper rejected successfully!');
    }

    public function approveBenefitFasil(Request $request, $id)
    {
        try {
            $paper = Paper::with('team')->findOrFail($id);
            $paper->status = $request->status;
            $paper->updateAndHistory([], $request->status);

            Comment::updateOrCreate([
                'paper_id' => $id,
                'writer' => "facilitator on Benefit",
            ], [
                'comment' => $request->comment
            ]);

            $benefitFinancial = $paper->financial;
            $benefitPotential = $paper->potential_benefit;
            $potensiReplikasi = $paper->potensi_replikasi;
            $benefitNonFinancial = $paper->non_financial;

            // Pastikan relasi Team sudah dimuat dengan benar
            if ($paper->team) {
                $gmId = PvtMember::where('team_id', $paper->team->id)
                    ->where('status', 'gm')
                    ->pluck('employee_id')
                    ->first();

                $gmData = User::where('employee_id', $gmId)
                    ->select('name', 'email')
                    ->first();

                $leaderId = PvtMember::where('team_id', $paper->team->id)
                    ->where('status', 'leader')
                    ->pluck('employee_id')
                    ->first();

                $leaderData = User::where('employee_id', $leaderId)
                    ->select('name', 'email')
                    ->first();

                $inovasi_lokasi = Paper::where('id', $id)
                    ->select('inovasi_lokasi')
                    ->first();

                // Membuat objek EmailApproval
                $mail = new EmailApprovalBenefit(
                    $paper,
                    $request->status,
                    $paper->innovation_title,
                    $paper->team->team_name,
                    $gmData,
                    $benefitFinancial,
                    $benefitPotential,
                    $potensiReplikasi,
                    $benefitNonFinancial,
                    $leaderData,
                    $inovasi_lokasi
                );

                // Mengirim email ke inovator (ketua tim)
                Mail::to($leaderData->email)->send($mail);
            } else {
                throw new \Exception('Paper tidak memiliki relasi dengan Team.');
            }

            if ($request->status == 'accepted benefit by facilitator') {
                // Pastikan relasi Team sudah dimuat dengan benar
                if ($paper->team) {
                    $gmId = PvtMember::where('team_id', $paper->team->id)
                        ->where('status', 'gm')
                        ->pluck('employee_id')
                        ->first();

                    $gmData = User::where('employee_id', $gmId)
                        ->select('name', 'email')
                        ->first();

                    $leaderId = PvtMember::where('team_id', $paper->team->id)
                        ->where('status', 'leader')
                        ->pluck('employee_id')
                        ->first();

                    $leaderData = User::where('employee_id', $leaderId)
                        ->select('name', 'email')
                        ->first();

                    $inovasi_lokasi = Paper::where('id', $id)
                        ->select('inovasi_lokasi')
                        ->first();

                    // Membuat objek EmailNotificationBenefitGM
                    $mail = new EmailNotificationBenefitGM(
                        $paper,
                        $request->status,
                        $paper->innovation_title,
                        $paper->team->team_name,
                        $gmData,
                        $benefitFinancial,
                        $benefitPotential,
                        $potensiReplikasi,
                        $benefitNonFinancial,
                        $leaderData,
                        $inovasi_lokasi
                    );

                    // Mengirim email ke general manager
                    Mail::to($gmData->email)->send($mail);
                } else {
                    throw new \Exception('Paper tidak memiliki relasi dengan Team.');
                }
            }

            return redirect()->route('paper.index')->with('success', 'Status Approval telah berhasil diperbarui');
        } catch (\Exception $e) {
            Log::debug($e);
            return redirect()->route('paper.index')->withErrors('Error: Gagal approve benefit! Pastikan semua data yang diperlukan telah diisi dengan benar.');
        }
    }

    public function approvebenefitbyGM(Request $request, $id)
    {
        $paper = Paper::with('team')->findOrFail($id);
        $paper->status = 'accepted benefit by general manager';
        $paper->save();

        return redirect()->route('paper.approveBenefitGM', [$id, 'status' => 'accepted benefit by general manager']);
        //return redirect()->back()->with('success', 'Paper approved successfully!');
    }

    public function rejectbenefitbyGM(Request $request, $id)
    {
        $paper = Paper::with('team')->findOrFail($id);
        $paper->status = 'rejected benefit by general manager';
        $paper->save();

        return redirect()->route('paper.approveBenefitGM', [$id, 'status' => 'rejected benefit by general manager']);
        //return redirect()->back()->with('success', 'Paper rejected successfully!');
    }

    public function approveBenefitGM(Request $request, $id)
    {
        try {
            $paper = Paper::with('team')->findOrFail($id);
            $revisionType = $request->input('revision_type');

            if (is_array($revisionType)) {
                if (in_array('benefit', $revisionType)) {
                    $paper->status = 'revision benefit by general manager';
                    $paper->updateAndHistory([], 'revision benefit by general manager');

                    Comment::updateOrCreate(
                        [
                            'paper_id' => $id,
                            'writer' => "General Manager Revisi Benefit",
                        ],
                        [
                            'comment' => $request->comment
                        ]
                    );
                }

                if (in_array('paper', $revisionType)) {
                    $paper->status = 'revision paper by general manager';
                    $paper->updateAndHistory([], 'revision paper by general manager');

                    Comment::updateOrCreate(
                        [
                            'paper_id' => $id,
                            'writer' => "General Manager Revisi Makalah",
                        ],
                        [
                            'comment' => $request->comment
                        ]
                    );
                    if ($request->has('revision_steps')) {
                        foreach ($request->revision_steps as $step) {
                            $stepColumn = 'step_' . $step;
                            $paper->$stepColumn = null;
                            $paper->full_paper = null;
                        }
                    } elseif ($request->has('full_paper')) {
                        $paper->full_paper = null;
                    }
                }

                if ($revisionType === ['benefit', 'paper']) {
                    $paper->status = 'revision paper and benefit by general manager';
                    $paper->updateAndHistory([], 'revision paper and benefit by general manager');

                    Comment::updateOrCreate(
                        [
                            'paper_id' => $id,
                            'writer' => "General Manager Revisi Makalah dan Benefit",
                        ],
                        [
                            'comment' => $request->comment
                        ]
                    );

                    if ($request->has('revision_steps')) {
                        foreach ($request->revision_steps as $step) {
                            $stepColumn = 'step_' . $step;
                            $paper->$stepColumn = null;
                            $paper->full_paper = null;
                        }
                    } elseif ($request->has('full_paper')) {
                        $paper->full_paper = null;
                    }
                }
            } else {
                $paper->status = $request->status;
                $paper->updateAndHistory([], $request->status);
            }

            $benefitFinancial = $paper->financial;
            $benefitPotential = $paper->potential_benefit;
            $potensiReplikasi = $paper->potensi_replikasi;
            $benefitNonFinancial = $paper->non_financial;

            if ($paper->team) {
                $gmId = PvtMember::where('team_id', $paper->team->id)
                    ->where('status', 'gm')
                    ->pluck('employee_id')
                    ->first();

                $gmData = User::where('employee_id', $gmId)
                    ->select('name', 'email')
                    ->first();

                $leaderId = PvtMember::where('team_id', $paper->team->id)
                    ->where('status', 'leader')
                    ->pluck('employee_id')
                    ->first();

                $leaderData = User::where('employee_id', $leaderId)
                    ->select('name', 'email', 'company_code')
                    ->first();

                $inovasi_lokasi = Paper::where('id', $id)
                    ->select('inovasi_lokasi')
                    ->first();

                $mail = new EmailApprovalBenefit(
                    $paper,
                    $paper->status,
                    $paper->innovation_title,
                    $paper->team->team_name,
                    $gmData,
                    $benefitFinancial,
                    $benefitPotential,
                    $potensiReplikasi,
                    $benefitNonFinancial,
                    $leaderData,
                    $inovasi_lokasi
                );

                Mail::to($leaderData->email)->send($mail);
            } else {
                throw new \Exception('Paper tidak memiliki relasi dengan Team.');
            }

            if ($request->status == 'accepted benefit by general manager') {
                if ($paper->team) {
                    $admins = User::where('company_code', $leaderData->company_code)
                        ->where('role', 'Admin')
                        ->select('name', 'email')
                        ->get();

                    foreach ($admins as $admin) {
                        $mail = new EmailNotificationFinal(
                            $paper,
                            $request->status,
                            $paper->innovation_title,
                            $paper->team->team_name,
                            $gmData,
                            $benefitFinancial,
                            $benefitPotential,
                            $potensiReplikasi,
                            $benefitNonFinancial,
                            $leaderData,
                            $admin,
                            $inovasi_lokasi
                        );

                        Mail::to($admin->email)->send($mail);
                    }
                } else {
                    throw new \Exception('Paper tidak memiliki relasi dengan Team.');
                }
            }

            return redirect()->route('paper.index')->with('success', 'Status Approval telah berhasil diperbarui');
        } catch (\Exception $e) {
            Log::debug($e);
            return redirect()->route('paper.index')->withErrors('Error: Gagal approve benefit! Pastikan semua data yang diperlukan telah diisi dengan benar.');
        }
    }

    public function approvePaperAdmin(Request $request, $id)
    {
        $paper = Paper::with('team')->findOrFail($id);
        $revisionType = $request->input('revision_type');

        if (is_array($revisionType)) {
            // Cek jika array mengandung nilai "benefit"
            if (in_array('benefit', $revisionType)) {
                $paper->status = 'revision benefit by innovation admin';
                $paper->updateAndHistory([], 'revision benefit by innovation admin');

                Comment::updateOrCreate(
                    [
                        'paper_id' => $id,
                        'writer' => "Admin  Revisi Benefit",
                    ],
                    [
                        'comment' => $request->comment
                    ]
                );
            }

            // Cek jika array mengandung nilai "paper"
            if (in_array('paper', $revisionType)) {
                // Lakukan sesuatu jika ada "paper"
                $paper->status = 'revision paper by innovation admin';
                $paper->updateAndHistory([], 'revision paper by innovation admin');

                Comment::updateOrCreate(
                    [
                        'paper_id' => $id,
                        'writer' => "Admin Revisi Makalah",
                    ],
                    [
                        'comment' => $request->comment
                    ]
                );
                if ($request->has('revision_steps')) {
                    // Revisi langkah: kosongkan langkah yang dipilih
                    foreach ($request->revision_steps as $step) {
                        $stepColumn = 'step_' . $step;
                        $paper->update([
                            'full_paper' => null,
                            $stepColumn => null
                        ]);
                    }
                } elseif ($revisionType === ['paper']) {
                    // Revisi full_paper: kosongkan full_paper
                    $paper->update([
                        'full_paper' => null
                    ]);
                }
            }

            // Cek jika array hanya memiliki 2 elemen tertentu
            if ($revisionType === ['benefit', 'paper']) {
                $paper->status = 'revision paper and benefit by innovation admin';
                $paper->updateAndHistory([], 'revision paper and benefit by innovation admin');

                Comment::updateOrCreate(
                    [
                        'paper_id' => $id,
                        'writer' => "Admin Revisi Makalah dan Benefit",
                    ],
                    [
                        'comment' => $request->comment
                    ]
                );

                if ($request->has('revision_steps')) {
                    // Revisi langkah: kosongkan langkah yang dipilih
                    foreach ($request->revision_steps as $step) {
                        $stepColumn = 'step_' . $step;
                        $paper->$stepColumn = null; // Set langkah ke null
                        $paper->full_paper = null;
                    }
                } elseif ($request->has('full_paper')) {
                    // Revisi full_paper: kosongkan full_paper
                    $paper->full_paper = null;
                }
            }
        } else {
            if ($request->status == "accept" || $request->status == "reject") {
                $paper->status = $request->status . "ed by " . $request->evaluatedBy;
                $msg = "change to " . $request->status . "ed by " . $request->evaluatedBy;
                $paper->updateAndHistory([], $msg);
            }
        }
        
        Comment::UpdateOrCreate([
            'paper_id' => $id,
            'writer' => $request->evaluatedBy,
        ], [
            'comment' => $request->comment
        ]);

        if ($request->status == "accept") {
            $team_id = Paper::where('id', $id)->pluck('team_id')[0];
            $team = Team::findOrFail($team_id);
            $event_id = Event::where('id', $request->event_id)->pluck('id')[0];
            $eventData = Event::findOrFail($event_id);
            $team->update([
                'status_lomba' => $eventData->type
            ]);
            $idEventTeam = PvtEventTeam::updateOrCreate([
                'team_id' => $team_id,
                'event_id' => $event_id,
                // 'year' => $request->year
            ])['id'];

            $category = PvtEventTeam::join('teams', 'pvt_event_teams.team_id', '=', 'teams.id')
                ->join('categories', 'categories.id', '=', 'teams.category_id')
                ->where('pvt_event_teams.id', $idEventTeam)
                ->pluck('category_parent')
                ->first();
            
            History::create([
                'team_id' => $team_id,
                'activity' => "Accepted to Event Internal",
                'status' => 'Accepted'
            ]);

            if ($category == 'IDEA BOX')
                $category = 'IDEA';
            else
                $category = 'BI/II';

            $data_assessment_event = PvtEventTeam::join('pvt_assessment_events', function ($join) {
                $join->on('pvt_assessment_events.event_id', '=', 'pvt_event_teams.event_id');
                // ->on('pvt_assessment_events.year', '=', 'pvt_event_teams.year');
            })
                ->where('pvt_event_teams.id', $idEventTeam)
                ->where('pvt_assessment_events.category', $category)
                ->where('pvt_assessment_events.status_point', 'active')
                ->where('pvt_assessment_events.stage', 'on desk')
                ->pluck(
                    'pvt_assessment_events.id as assessment_event_id',
                )
                ->toArray();
            // dd($data_assessment_event);
            foreach ($data_assessment_event as $assessmentEvent) {
                pvtAssesmentTeamJudge::updateOrCreate([
                    'event_team_id' => $idEventTeam,
                    'assessment_event_id'   => $assessmentEvent,
                    'stage' => 'on desk'
                ]);
            }
        }

        $benefitFinancial = $paper->financial;
        $benefitPotential = $paper->potential_benefit;
        $potensiReplikasi = $paper->potensi_replikasi;
        $benefitNonFinancial = $paper->non_financial;

        // Pastikan relasi Team sudah dimuat dengan benar
        if ($paper->team) { //email terkirim ke yg terassign di event yg sedang berlangsung

            $gmId = PvtMember::where('team_id', $paper->team->id)
                ->where('status', 'gm')
                ->pluck('employee_id')
                ->first();

            $gmData = User::where('employee_id', $gmId)
                ->select('name', 'email')
                ->first();

            $leaderId = PvtMember::where('team_id', $paper->team->id)
                ->where('status', 'leader')
                ->pluck('employee_id')
                ->first();

            $leaderData = User::where('employee_id', $leaderId)
                ->select('name', 'email')
                ->first();

            $inovasi_lokasi = Paper::where('id', $id)
                ->select('inovasi_lokasi')
                ->first();

            // Membuat objek EmailApproval
            $mail = new EmailApprovalFinal(
                $paper,
                $paper->status,
                $paper->innovation_title,
                $paper->team->team_name,
                $gmData,
                $benefitFinancial,
                $benefitPotential,
                $potensiReplikasi,
                $benefitNonFinancial,
                $leaderData,
                $inovasi_lokasi
            );

            // Mengirim email ke inovator (ketua tim)
            Mail::to($leaderData->email)->send($mail);
        } else {
            throw new \Exception('Paper tidak memiliki relasi dengan Team.');
        }
        return redirect()->route('paper.index')->with('success', 'Status Approval telah berhasil diperbarui');
    }

    public function event()
    {
        $data_event = Event::whereIn('type', ['group', 'national', 'international'])->get();
        $currentYear = Carbon::now()->year;
        $years = [];
        for ($i = $currentYear; $i <= $currentYear + 2; $i++) {
            $years[$i] = $i;
        }
        $idSIGGIA = 8;

        return view('auth.user.paper.competition', [
            "data_event" => $data_event,
            "years" => $years,
            "idSigGroup" => $idSIGGIA
        ]);
    }

    public function assign_new_event(Request $request)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            if ($request->event == 'Nasional' || $request->event == 'Internasional') {
                $dataEvidence = Evidence::Create([
                    'team_id' => $request->team_id,
                    'event_name' => $request->event,
                    'year' => $request->year,
                ]);
            }

            DB::commit();
            return redirect()->route('paper.event')->with('success', 'Data berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            // dd($e->getMessage());
            return redirect()->route('paper.event')->withErrors('Error: ' . $e->getMessage());
        }
    }

    public function rollback_status(Request $request, $paper_id)
    {
        $rollbackOption = $request->input('rollback_option');
        $comment = $request->input('comment');
        try {
            DB::beginTransaction();

            $paper = Paper::findOrFail($paper_id);

            if ($rollbackOption == 'full_paper') {
                $paper->status_rollback = "rollback paper";
                if ($paper->step_1 == '-') {
                    $paper->step_1 = null;
                }
                if ($paper->step_2 == '-') {
                    $paper->step_2 = null;
                }
                if ($paper->step_3 == '-') {
                    $paper->step_3 = null;
                }
                if ($paper->step_4 == '-') {
                    $paper->step_4 = null;
                }
                if ($paper->step_5 == '-') {
                    $paper->step_5 = null;
                }
                if ($paper->step_6 == '-') {
                    $paper->step_6 = null;
                }
                if ($paper->step_7 == '-') {
                    $paper->step_7 = null;
                }
                if ($paper->step_8 == '-') {
                    $paper->step_8 = null;
                }
                $msg = "change to " . $paper->status . " by " . $request->evaluatedBy . " (rollback) ";

                for ($i = 1; $i <= 8; $i++) {
                    $stepField = 'step_' . $i;
                    $initialStepField = 'step_' . $i . '_initial';
                    if (isset($paper->$initialStepField)) {
                        $paper->$stepField = $paper->$initialStepField;
                    }
                }

                $paper->updateAndHistory([], $msg);
            } elseif ($rollbackOption == 'benefit') {
                $paper->status_rollback = "rollback benefit";
                $msg = "change to " . $paper->status . " by " . $request->evaluatedBy . " (rollback) ";
                $paper->updateAndHistory([], $msg);
            }

            $paper->save();

            Comment::UpdateOrCreate([
                'paper_id' => $paper_id,
                'writer' => $request->evaluatedBy,
            ], [
                'comment' => $request->comment
            ]);

            DB::commit();
            return redirect()->route('paper.index')->with('success', 'Data berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            // dd($e->getMessage());
            return redirect()->route('paper.index')->withErrors('Error: ' . $e->getMessage());
        }
    }

    public function update2(Request $request, $id)
    {
        // ...
        $paper = Paper::find($id);
        for ($i = 1; $i <= 8; $i++) {
            $stepField = 'step_' . $i;
            $initialStepField = 'step_' . $i . '_initial';
            if ($request->has($stepField)) {
                $paper->$stepField = $request->input($stepField);
                $paper->$initialStepField = $request->input($stepField);
            }
        }
        // ...
        $paper->save();
    }

    public function update(updateTeamPaperRequests $request, $id)
    {
        try {
            $data = Team::findOrFail($id);
            $data_paper = Paper::where('team_id', $id);
            $data->update([
                'team_name' => $request->input('team_name'),
                'category_id' => $request->input('category'),
                'theme_id' => $request->input('theme'),
            ]);

            $data_paper->update([
                'innovation_title' => $request->input('innovation_title'),
                'status_inovasi' =>
                $request->input('status_inovasi'),
            ]);

            // Mengirim notifikasi
            $user = Auth::user();
            $user->notify(new PaperNotification(
                'Data Updated',
                'The paper has been updated.',
                route('paper.index')
            ));

            return redirect()->route('paper.index')->with('success', 'Data berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->route('paper.index')->withErrors('Error: ' . $e->getMessage());
        }
    }

    public function uploadDocument(Request $request)
    {
        $validated = $request->validate([
            'document_support' => [
                'required',
                'array', // Input sebagai array jika mengijinkan multiple file
            ],
            'document_support.*' => [
                'required',
                'file', // Validasi bahwa ini adalah file
                'mimes:pdf,jpg,jpeg,png,mp4,avi,mkv', // Validasi untuk menerima format pdf, gambar, atau video
                function ($attribute, $value, $fail) {
                    $fileType = $value->getMimeType();
                    $fileSize = $value->getSize() / 1024; // ukuran dalam KB

                    // Validasi berdasarkan tipe file
                    if (str_contains($fileType, 'pdf') && $fileSize > 30720) { // PDF maksimal 30MB (30720 KB)
                        $fail("The {$attribute} must not exceed 10MB.");
                    } elseif (str_contains($fileType, 'image') && $fileSize > 5120) { // Gambar maksimal 5MB (5120 KB)
                        $fail("The {$attribute} must not exceed 5MB.");
                    } elseif (str_contains($fileType, 'video') && $fileSize > 51200) { // Video maksimal 50MB (51200 KB)
                        $fail("The {$attribute} must not exceed 50MB.");
                    }
                },
            ],
        ]);

        $uploadedFiles = [];
        foreach ($request->file('document_support') as $file) {
            $path = $file->store('document_support', 'public');
            $uploadedFiles[] = [
                'paper_id' => $request->input('paper_id'),
                'file_name' => $file->getClientOriginalName(),
                'path' => $path,
            ];
        }

        try {
            DB::beginTransaction();
            foreach ($uploadedFiles as $fileData) {
                $document = new DocumentSupport([
                    'paper_id' => $request->input('paper_id'),
                    'file_name' => $fileData['file_name'],
                    'path' => $fileData['path'],
                ]);
                $document->save();
            }
            DB::commit();
            return redirect()->back()->with('success', 'Document upload success.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['upload' => 'Failed to upload documents. Error: ' . $e->getMessage()]);
        }
    }




    public function deleteDocument(Request $request)
    {
        $document = DocumentSupport::find($request->id);
        if (!$document) {
            return redirect()->back()->withErrors('Invalid document');
        }

        $document->delete();

        return redirect()->back()->withSuccess("Document deleted successfully");
    }

    public function checkIsCompressed(Request $request)
    {
        try {
            // Validate the file input
            $request->validate([
                'file_stage' => 'required|file|mimes:pdf|max:20480', // Adjust the max size as needed
            ]);

            // Get the uploaded file
            $uploadedFile = $request->file('file_stage');

            // Create a StreamReader from the uploaded file
            $stream = StreamReader::createByFile($uploadedFile->getPathname());

            // Initialize FPDI with the StreamReader
            $pdf = new Fpdi();
            $pdf->setSourceFile($stream);

            // If it reaches here, the file is likely not compressed in a way that FPDI can't handle
            return false;
        } catch (\Exception $e) {
            // If an exception is thrown, it might indicate a compressed PDF that FPDI couldn't process
            return true;
        }
    }

    public function getEventsByCompanyCode($companyCode)
    {
        try {
            // Cari perusahaan berdasarkan company_code
            $company = Company::where('company_code', $companyCode)->first();

            // Jika perusahaan tidak ditemukan, lempar error
            if (!$company) {
                return response()->json(['error' => 'Perusahaan tidak ditemukan'], 404);
            }

            // Ambil daftar event terkait perusahaan
            $events = $company->events()->get();

            // Kembalikan response dengan data event
            return response()->json([
                'success' => true,
                'company' => $company->company_name,
                'events' => $events,
            ]);
        } catch (\Exception $e) {
            // Tangani error
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function fixatePaper($id)
    {
        $paper = Paper::findOrFail($id);

        if (!$paper->team) {
            return response()->json(['success' => false, 'message' => 'Paper tidak memiliki tim.'], 400);
        }

        // Update status fiksasi
        $paper->status = 'upload full paper';
        $paper->save();

        // Ambil fasilitator dan leader
        $fasilData = PvtMember::where('team_id', $paper->team->id)
            ->where('status', 'facilitator')
            ->first();

        $leaderData = PvtMember::where('team_id', $paper->team->id)
            ->where('status', 'leader')
            ->first();

        if (!$fasilData || !$leaderData) {
            return response()->json(['success' => false, 'message' => 'Fasilitator atau Leader tidak ditemukan.'], 400);
        }

        $fasilUser = User::where('employee_id', $fasilData->employee_id)->first();
        $leaderUser = User::where('employee_id', $leaderData->employee_id)->first();

        if (!$fasilUser || !$leaderUser) {
            return response()->json(['success' => false, 'message' => 'Data user tidak valid.'], 400);
        }

        // Kirim email ke fasilitator
        try {
            Mail::to($fasilUser->email)->send(new EmailNotificationPaperFasil(
                $paper,
                'full_paper',
                $paper->innovation_title,
                $paper->team->team_name,
                $leaderUser->name,
                $fasilUser->name,
                $paper->inovasi_lokasi
            ));

            return response()->json(['success' => true, 'message' => 'Makalah berhasil difiksasi dan email dikirim.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengirim email.', 'error' => $e->getMessage()], 500);
        }
    }

    public function addWatermarks($paperId, $stage = 'full_paper') {
        try {
            // Ensure the filepath is correctly formatted by removing "f: " and trimming any leading/trailing slashes
            $filePath = storage_path('app/public/' . ltrim(Paper::where('id', '=', $paperId)->pluck($stage)[0], '/'));

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

    public function templatePreview($methodology, $step) {
        $filePath = storage_path('app/public/paper_template/' . $methodology . '/' . $step . '.pdf');

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File tidak ditemukan.'], 404);
        }
        
        return response()->file($filePath, [
            'Content-Disposition' => 'inline;'
        ]);
    }

    public function templateDownload($methodology, $step) {
        $filePath = storage_path('app/public/paper_template/' . $methodology . '/' . $step . '.docx');

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File tidak ditemukan.'], 404);
        }

        return response()->download($filePath, basename($methodology . '-' . $step . '.docx'));
    }
}