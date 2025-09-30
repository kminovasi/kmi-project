<?php

namespace App\Http\Controllers;

use Log;
use Mpdf\Mpdf;
use Carbon\Carbon;
use App\Models\Team;
use App\Models\Event;
use App\Models\BodEvent;
use App\Models\Category;
use App\Models\Evidence;
use App\Models\PvtMember;
use App\Models\BeritaAcara;
use App\Models\PvtEventTeam;
use Illuminate\Http\Request;
use setasign\Fpdi\Tcpdf\Fpdi;
use Illuminate\Support\Str;
use App\Models\PvtAssessmentEvent;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BeritaAcaraController extends Controller
{
    public function index()
    {
        $data = BeritaAcara::join('events', 'berita_acaras.event_id', 'events.id')
            ->select('berita_acaras.*', 'events.id as eventID', 'events.event_name', 'events.event_name', 'events.year', 'events.date_start', 'events.date_end')
            ->get();
        $event = Event::where('status', 'active')->get();
        return view('auth.admin.berita-acara.index', ['data' => $data, 'event' => $event]);
    }
    public function store(Request $request)
    {
        try {

            $request->validate([
            'event_id'        => ['required','exists:events,id'],
            'no_surat'        => ['required','string','max:255'],
            'jenis_event'     => ['required','in:Internal,Grup,Eksternal,Group'],
            'rapat_juri'      => ['required','date'],
            'rapat_direktur'  => ['nullable','date'],
            'penetapan_juara' => ['required','date'],
        ]);

            // Cek apakah BodEvent sudah ada
            $bodEventExists = BodEvent::where('event_id', $request->input('event_id'))->exists();

            // Jika BodEvent belum ada, batalkan proses dan kembalikan pesan error
            if (!$bodEventExists) {
                return redirect()->route('assessment.penetapanJuara')
                    ->withErrors(['BOD belum di pilih untuk event ini.'])
                    ->with('bodEventUrl', route('management-system.role.bod.event.create')); // Simpan URL untuk tombol
            }

            // Cek apakah sudah ada berita acara untuk event ini
            $existingBeritaAcara = BeritaAcara::where('event_id', $request->input('event_id'))->exists();

            // Jika berita acara sudah ada, kembalikan pesan error
            if ($existingBeritaAcara) {
                return redirect()->route('assessment.penetapanJuara')
                    ->withErrors(['Error: Berita acara untuk event ini sudah ada.']);
            }

            DB::beginTransaction();
             BeritaAcara::create([
            'event_id'          => $request->input('event_id'),
            'no_surat'          => $request->input('no_surat'),
            'jenis_event'       => $request->input('jenis_event'),
            'penetapan_juara'   => Carbon::parse($request->input('penetapan_juara'))->format('Y-m-d'),
            'rapat_juri_at'     => Carbon::parse($request->input('rapat_juri'))->format('Y-m-d'),
            'rapat_direktur_at' => $request->filled('rapat_direktur')
                                    ? Carbon::parse($request->input('rapat_direktur'))->format('Y-m-d')
                                    : null,
        ]);

            // Update status event menjadi "finish"
            Event::where('id', $request->input('event_id'))
                ->update(['status' => 'finish']);

            // Proses penetapan juara berdasarkan kategori yang sudah ada di method sebelumnya

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('assessment.penetapanJuara')->withErrors('Error: ' . $e->getMessage());
        }

        return redirect()->route('assessment.penetapanJuara')->with('success', 'Berita Acara Berhasil Di Buatt');
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Temukan berita acara berdasarkan ID
            $beritaAcara = BeritaAcara::findOrFail($id);

            // Hapus berita acara
            $beritaAcara->delete();

            DB::commit();
            return redirect()->route('assessment.penetapanJuara')->with('success', 'Berita Acara berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('assessment.penetapanJuara')->withErrors('Error: ' . $e->getMessage());
        }
    }

    public function viewUploadedPDF($path)
    {
        $relativePath = urldecode($path); // contoh: dokumen/file.pdf
        $storagePath = storage_path('app/public/' . $relativePath);
    
        if (!file_exists($storagePath)) {
            abort(404, 'File not found.');
        }
    
        return response()->file($storagePath, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function showPDF($id)
    {
        $category = Category::all();
        $data = BeritaAcara::join('events', 'berita_acaras.event_id', 'events.id')
            ->where('berita_acaras.id', $id)
            ->select('berita_acaras.*', 'events.id as eventID', 'events.event_name', 'events.year', 'events.date_start', 'events.date_end')
            ->firstOrFail(); 
    
        $idEvent = $data->eventID;
    
        $carbonInstance = Carbon::parse($data->penetapan_juara);
        setlocale(LC_TIME, 'id_ID');
        $day = $carbonInstance->isoFormat('dddd');
        $date = numberToWords($carbonInstance->isoFormat('D'));
        $month = $carbonInstance->isoFormat('MMMM');
        $year = numberToWords($carbonInstance->isoFormat('YYYY'));
    
        $carbonInstance_startDate = Carbon::parse($data->date_start);
        $carbonInstance_endDate = Carbon::parse($data->date_end);
        $carbonInstance_rapatJuri = Carbon::parse($data->rapat_juri_at);
        $carbonInstance_rapatDirektur = Carbon::parse($data->rapat_direktur_at);
        $carbonInstance_penetapanJuara = Carbon::parse($data->penetapan_juara);
        
    
        // Ambil daftar kategori yang bukan IDEA BOX
        $categoryID_list = Category::whereNot('category_parent', 'IDEA BOX')->orderBy('category_name', 'ASC')->pluck('id')->toArray();
    
        // Cek apakah ada event assessment BI dan IDEA yang aktif
        $assessment_event_poin_bi = PvtAssessmentEvent::where('event_id', $idEvent)
            ->where('category', 'BI/II')
            ->where('status_point', 'active')
            ->exists();
    
        $assessment_event_poin_idea = PvtAssessmentEvent::where('event_id', $idEvent)
            ->where('category', 'IDEA')
            ->where('status_point', 'active')
            ->exists();
    
        // Ambil data juara berdasarkan kategori
        $juara = [];
        foreach ($categoryID_list as $categoryID) {
            $category_name = Category::where('id', '=', $categoryID)->value('category_name');
    
            if ($assessment_event_poin_bi) {
                $juara[$category_name] = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                    ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                    ->join('papers', 'papers.team_id', '=', 'teams.id')
                    ->join('companies', 'companies.company_code', '=', 'teams.company_code')
                    ->where('teams.category_id', '=', $categoryID)
                    ->where('pvt_event_teams.status', '=', 'Juara')
                    ->where('pvt_event_teams.event_id', '=', $idEvent)
                    ->where('pvt_assesment_team_judges.stage', '=', 'presentation')
                    ->where('pvt_event_teams.is_honorable_winner', '!=', true)
                    ->groupBy('pvt_event_teams.id', 'teams.team_name', 'papers.innovation_title', 'companies.company_name', 'pvt_event_teams.is_honorable_winner','pvt_event_teams.is_best_of_the_best', 'pvt_event_teams.final_score')
                    ->select(
                        'teams.team_name as teamname',
                        'papers.innovation_title',
                        'companies.company_name',
                        'pvt_event_teams.final_score',
                        DB::raw('RANK() OVER (ORDER BY pvt_event_teams.final_score DESC) as ranking')
                    )
                    ->orderBy('pvt_event_teams.final_score', 'DESC')
                    ->take(3)
                    ->get()
                    ->toArray();
            } else {
                $juara[$category_name] = [];
            }
        }
    
        // Kategori IDEA BOX
        if ($assessment_event_poin_idea) {
            $juara["IDEA"] = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                ->join('categories', 'teams.category_id', '=', 'categories.id')
                ->join('papers', 'papers.team_id', '=', 'teams.id')
                ->join('companies', 'companies.company_code', '=', 'teams.company_code')
                ->where('categories.category_parent', '=', 'IDEA BOX')
                ->where('pvt_event_teams.status', '=', 'Juara')
                ->where('pvt_event_teams.event_id', '=', $idEvent)
                ->where('pvt_assesment_team_judges.stage', '=', 'presentation')
                ->where('pvt_event_teams.is_honorable_winner', '!=', true)   
                ->groupBy(
                    'pvt_event_teams.id', 
                    'teams.team_name', 
                    'papers.innovation_title', 
                    'companies.company_name', 
                    'pvt_event_teams.is_honorable_winner',
                    'pvt_event_teams.is_best_of_the_best', 
                    'pvt_event_teams.final_score'
                )
                ->select(
                    'teams.team_name as teamname', 
                    'papers.innovation_title', 
                    'companies.company_name',
                    'pvt_event_teams.is_honorable_winner',
                    'pvt_event_teams.is_best_of_the_best',
                    'pvt_event_teams.final_score',
                    DB::raw('RANK() OVER (ORDER BY pvt_event_teams.final_score DESC) as ranking')
                )
                ->orderByRaw('COALESCE(pvt_event_teams.final_score, 0) DESC')
                ->take(3)
                ->get()
                ->toArray();
        } else {
            $juara["IDEA"] = [];
        }

        // Best Of The Best
        if ($assessment_event_poin_bi) {
            $juara['Juara Harapan'] = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                ->join('papers', 'papers.team_id', '=', 'teams.id')
                ->join('companies', 'companies.company_code', '=', 'teams.company_code')
                ->where('pvt_event_teams.event_id', $idEvent)
                ->where('pvt_event_teams.is_honorable_winner', '=', true)
                ->groupBy('pvt_event_teams.id', 'teams.team_name', 'papers.innovation_title', 'companies.company_name', 'pvt_event_teams.is_honorable_winner','pvt_event_teams.is_best_of_the_best', 'pvt_event_teams.final_score')
                ->select(
                    'teams.team_name as teamname',
                    'papers.innovation_title',
                    'companies.company_name',
                    'pvt_event_teams.is_honorable_winner',
                    'pvt_event_teams.is_best_of_the_best',
                    'pvt_event_teams.final_score',
                    DB::raw('RANK() OVER (ORDER BY COALESCE(pvt_event_teams.final_score, 0) DESC) as ranking') // Tambahkan ranking
                )
                ->orderByRaw('COALESCE(pvt_event_teams.final_score, 0) DESC')
                ->get()
                ->toArray();
    
            $juara['Best Of The Best'] = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                ->join('papers', 'papers.team_id', '=', 'teams.id')
                ->join('companies', 'companies.company_code', '=', 'teams.company_code')
                ->where('pvt_event_teams.event_id', $idEvent)
                ->where('pvt_event_teams.is_best_of_the_best', '=', true)
                ->groupBy('pvt_event_teams.id', 'teams.team_name', 'papers.innovation_title', 'companies.company_name', 'pvt_event_teams.is_honorable_winner','pvt_event_teams.is_best_of_the_best', 'pvt_event_teams.final_score')
                ->select(
                    'teams.team_name as teamname',
                    'papers.innovation_title',
                    'companies.company_name',
                    'pvt_event_teams.is_honorable_winner',
                    'pvt_event_teams.is_best_of_the_best',
                    'pvt_event_teams.final_score',
                    DB::raw('RANK() OVER (ORDER BY COALESCE(pvt_event_teams.final_score, 0) DESC) as ranking') // Tambahkan ranking
                )
                ->orderByRaw('COALESCE(pvt_event_teams.final_score, 0) DESC')
                ->take(1)
                ->get()
                ->toArray();
        } else {
            $juara['Best Of The Best'] = [];
        }

        // KEPUTUSAN BOD 
        $juara['Keputusan BOD'] = PvtEventTeam::join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->join('papers', 'papers.team_id', '=', 'teams.id')
            ->join('companies', 'companies.company_code', '=', 'teams.company_code')
            ->where('pvt_event_teams.event_id', $idEvent)
            ->whereNotNull('pvt_event_teams.keputusan_bod')
            ->whereRaw("TRIM(COALESCE(pvt_event_teams.keputusan_bod, '')) <> ''")
            ->select(
                'teams.team_name as teamname',
                'papers.innovation_title',
                'companies.company_name',
                'pvt_event_teams.keputusan_bod as keputusan_bod',
                'pvt_event_teams.final_score'
            )
            ->orderByRaw('COALESCE(pvt_event_teams.final_score,0) DESC')
            ->get()
            ->toArray();

        $judges = DB::table('judges as j')
            ->join('users as u', 'u.employee_id', '=', 'j.employee_id') 
            ->where('j.event_id', $idEvent)
            ->select('u.name', 'u.position_title')   
            ->distinct()
            ->orderBy('u.name')                      
            ->get();
    
        // Ambil data BOD
        $bods = BodEvent::join('users', 'users.employee_id', '=', 'bod_events.employee_id')
            ->where('event_id', '=', $idEvent)
            ->select('users.name', 'users.position_title')
            ->get()
            ->toArray();
    
        // Generate PDF
        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-P', 'margin_left'  => 20,'margin_right'  => 20,'margin_top'  => 20,'margin_bottom'  => 20]);
        $html = view('auth.admin.berita-acara.pdf', compact(
            'data', 'day', 'date', 'month', 'year',
            'carbonInstance', 'juara', 'category', 'bods',
            'carbonInstance_startDate', 'carbonInstance_endDate', 'judges',
            'carbonInstance_rapatJuri','carbonInstance_rapatDirektur','carbonInstance_penetapanJuara'
        ))->render();
    
        $mpdf->WriteHTML($html);
        $filename = str_replace(' ', '_', $data->event_name) . '_Berita_Acara.pdf';
    
        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }

    public function downloadPdf($id)
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
        $carbonInstance_rapatJuri = Carbon::parse($data->rapat_juri_at);
        $carbonInstance_rapatDirektur = Carbon::parse($data->rapat_direktur_at);
        $carbonInstance_penetapanJuara = Carbon::parse($data->penetapan_juara);

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
        foreach ($categoryID_list as $categoryID) {
            $category_name = Category::where('id', '=', $categoryID)->pluck('category_name')[0];
            if ($assessment_event_poin_bi) {
                $juara[$category_name] = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                    ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                    ->join('papers', 'papers.team_id', '=', 'teams.id')
                    ->join('companies', 'companies.company_code', 'teams.company_code')
                    ->where('teams.category_id', '=', $categoryID)
                    ->where('pvt_event_teams.status', '=', 'Juara')
                    ->where('pvt_event_teams.event_id', '=', $idEvent)
                    ->where('pvt_assesment_team_judges.stage', 'presentation')
                    ->groupBy('pvt_event_teams.id', 'teams.team_name', 'papers.innovation_title', 'companies.company_name')
                    ->select('teams.team_name as teamname', 'papers.innovation_title', 'companies.company_name')
                    ->orderByRaw('ROUND(ROUND(SUM(pvt_assesment_team_judges.score), 2) / COUNT(CASE WHEN pvt_assesment_team_judges.assessment_event_id = ? THEN pvt_assesment_team_judges.assessment_event_id END), 2) DESC', [$assessment_event_poin_bi])
                    ->take(3)
                    ->get()
                    ->toArray();
            } else {
                $juara[$category_name] = [];
            }
        }
        if ($assessment_event_poin_idea) {
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
                ->take(3)
                ->get()
                ->toArray();
        } else {
            $juara["IDEA"] = [];
        }

        if ($assessment_event_poin_bi) {
            $juara['Best Of The Best'] = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                ->join('papers', 'papers.team_id', '=', 'teams.id')
                ->join('companies', 'companies.company_code', 'teams.company_code')
                ->where('pvt_event_teams.event_id', '=', $idEvent)
                ->where('pvt_event_teams.is_best_of_the_best', '=', true)
                ->groupBy('pvt_event_teams.id', 'teams.team_name', 'papers.innovation_title', 'companies.company_name')
                ->select('teams.team_name as teamname', 'papers.innovation_title', 'companies.company_name')
                ->take(1)
                ->get()
                ->toArray();
        } else {
            $juara['Best Of The Best'] = [];
        }

        $judges = DB::table('judges as j')
            ->join('users as u', 'u.employee_id', '=', 'j.employee_id') 
            ->where('j.event_id', $idEvent)
            ->select('u.name', 'u.position_title')   
            ->distinct()
            ->orderBy('u.name')                      
            ->get();

        $bods = BodEvent::join('users', 'users.employee_id', '=', 'bod_events.employee_id')
            ->where('event_id', '=', $idEvent)
            ->select('users.name', 'users.position_title')
            ->get()
            ->toArray();


        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);
        $html = view('auth.admin.berita-acara.pdf', compact(
            'data',
            'day',
            'date',
            'month',
            'year',
            'carbonInstance',
            'juara',
            'category',
            'bods',
            'carbonInstance_startDate',
            'carbonInstance_endDate',
            'carbonInstance_rapatJuri','carbonInstance_rapatDirektur','carbonInstance_penetapanJuara'
        ))->render();

        $mpdf->WriteHTML($html);
        $content = $mpdf->Output('', 'S');

        $filename = str_replace(' ', '_', $data->event_name) . '_Berita_Acara.pdf';

        return response($content)
            ->header('Content-Type', 'application/pdf', 'docx')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function buildBeritaAcaraPayload(int $id): array
    {
        $category = Category::all();

        $data = BeritaAcara::join('events', 'berita_acaras.event_id', 'events.id')
            ->where('berita_acaras.id', $id)
            ->select('berita_acaras.*', 'events.id as eventID', 'events.event_name', 'events.year', 'events.date_start', 'events.date_end')
            ->firstOrFail();

        $idEvent = $data->eventID;

        // Tanggal
        $carbonInstance = Carbon::parse($data->penetapan_juara);
        setlocale(LC_TIME, 'id_ID');
        $day   = $carbonInstance->isoFormat('dddd');
        $date  = numberToWords($carbonInstance->isoFormat('D'));
        $month = $carbonInstance->isoFormat('MMMM');
        $year  = numberToWords($carbonInstance->isoFormat('YYYY'));
        $carbonInstance_startDate = Carbon::parse($data->date_start);
        $carbonInstance_endDate   = Carbon::parse($data->date_end);
        $carbonInstance_rapatJuri = Carbon::parse($data->rapat_juri_at);
        $carbonInstance_rapatDirektur = Carbon::parse($data->rapat_direktur_at);
        $carbonInstance_penetapanJuara = Carbon::parse($data->penetapan_juara);

        // Cek assessment aktif
        $assessment_event_poin_bi = PvtAssessmentEvent::where('event_id', $idEvent)
            ->where('category', 'BI/II')->where('status_point', 'active')->exists();

        $assessment_event_poin_idea = PvtAssessmentEvent::where('event_id', $idEvent)
            ->where('category', 'IDEA')->where('status_point', 'active')->exists();

        // Kategori (selain IDEA BOX)
        $categoryID_list = Category::whereNot('category_parent', 'IDEA BOX')
            ->orderBy('category_name', 'ASC')->pluck('id')->toArray();

        // Juara per kategori
        $juara = [];
        foreach ($categoryID_list as $categoryID) {
            $category_name = Category::where('id', $categoryID)->value('category_name');
            if ($assessment_event_poin_bi) {
                $juara[$category_name] = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                    ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                    ->join('papers', 'papers.team_id', '=', 'teams.id')
                    ->join('companies', 'companies.company_code', '=', 'teams.company_code')
                    ->where('teams.category_id', $categoryID)
                    ->where('pvt_event_teams.status', 'Juara')
                    ->where('pvt_event_teams.event_id', $idEvent)
                    ->where('pvt_assesment_team_judges.stage', 'presentation')
                    ->where('pvt_event_teams.is_honorable_winner', '!=', true)
                    ->groupBy('pvt_event_teams.id', 'teams.team_name', 'papers.innovation_title', 'companies.company_name', 'pvt_event_teams.is_honorable_winner','pvt_event_teams.is_best_of_the_best', 'pvt_event_teams.final_score')
                    ->select(
                        'teams.team_name as teamname',
                        'papers.innovation_title',
                        'companies.company_name',
                        'pvt_event_teams.final_score',
                        DB::raw('RANK() OVER (ORDER BY pvt_event_teams.final_score DESC) as ranking')
                    )
                    ->orderBy('pvt_event_teams.final_score', 'DESC')
                    ->take(3)->get()->toArray();
            } else {
                $juara[$category_name] = [];
            }
        }

        // IDEA BOX
        if ($assessment_event_poin_idea) {
            $juara["IDEA"] = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                ->join('categories', 'teams.category_id', '=', 'categories.id')
                ->join('papers', 'papers.team_id', '=', 'teams.id')
                ->join('companies', 'companies.company_code', '=', 'teams.company_code')
                ->where('categories.category_parent', 'IDEA BOX')
                ->where('pvt_event_teams.status', 'Juara')
                ->where('pvt_event_teams.event_id', $idEvent)
                ->where('pvt_assesment_team_judges.stage', 'presentation')
                ->where('pvt_event_teams.is_honorable_winner', '!=', true)
                ->groupBy('pvt_event_teams.id','teams.team_name','papers.innovation_title','companies.company_name','pvt_event_teams.is_honorable_winner','pvt_event_teams.is_best_of_the_best','pvt_event_teams.final_score')
                ->select(
                    'teams.team_name as teamname',
                    'papers.innovation_title',
                    'companies.company_name',
                    'pvt_event_teams.is_honorable_winner',
                    'pvt_event_teams.is_best_of_the_best',
                    'pvt_event_teams.final_score',
                    DB::raw('RANK() OVER (ORDER BY pvt_event_teams.final_score DESC) as ranking')
                )
                ->orderByRaw('COALESCE(pvt_event_teams.final_score,0) DESC')
                ->take(3)->get()->toArray();
        } else {
            $juara["IDEA"] = [];
        }

        // Juara Harapan & BoB
        if ($assessment_event_poin_bi) {
            $juara['Juara Harapan'] = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                ->join('papers', 'papers.team_id', '=', 'teams.id')
                ->join('companies', 'companies.company_code', '=', 'teams.company_code')
                ->where('pvt_event_teams.event_id', $idEvent)
                ->where('pvt_event_teams.is_honorable_winner', true)
                ->groupBy('pvt_event_teams.id','teams.team_name','papers.innovation_title','companies.company_name','pvt_event_teams.is_honorable_winner','pvt_event_teams.is_best_of_the_best','pvt_event_teams.final_score')
                ->select(
                    'teams.team_name as teamname','papers.innovation_title','companies.company_name',
                    'pvt_event_teams.is_honorable_winner','pvt_event_teams.is_best_of_the_best',
                    'pvt_event_teams.final_score',
                    DB::raw('RANK() OVER (ORDER BY COALESCE(pvt_event_teams.final_score,0) DESC) as ranking')
                )
                ->orderByRaw('COALESCE(pvt_event_teams.final_score,0) DESC')
                ->get()->toArray();

            $juara['Best Of The Best'] = PvtEventTeam::join('pvt_assesment_team_judges', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
                ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
                ->join('papers', 'papers.team_id', '=', 'teams.id')
                ->join('companies', 'companies.company_code', '=', 'teams.company_code')
                ->where('pvt_event_teams.event_id', $idEvent)
                ->where('pvt_event_teams.is_best_of_the_best', true)
                ->groupBy('pvt_event_teams.id','teams.team_name','papers.innovation_title','companies.company_name','pvt_event_teams.is_honorable_winner','pvt_event_teams.is_best_of_the_best','pvt_event_teams.final_score')
                ->select(
                    'teams.team_name as teamname','papers.innovation_title','companies.company_name',
                    'pvt_event_teams.is_honorable_winner','pvt_event_teams.is_best_of_the_best',
                    'pvt_event_teams.final_score',
                    DB::raw('RANK() OVER (ORDER BY COALESCE(pvt_event_teams.final_score,0) DESC) as ranking')
                )
                ->orderByRaw('COALESCE(pvt_event_teams.final_score,0) DESC')
                ->take(1)->get()->toArray();
        } else {
            $juara['Best Of The Best'] = [];
        }

        // Semua juri yang terdaftar/menilai pada event
        $judges = DB::table('judges as j')
            ->join('users as u', 'u.employee_id', '=', 'j.employee_id')
            ->where('j.event_id', $idEvent)
            ->select('u.name', 'u.position_title')
            ->distinct()->orderBy('u.name')->get();

        // BOD penetap
        $bods = BodEvent::join('users', 'users.employee_id', '=', 'bod_events.employee_id')
            ->where('event_id', $idEvent)
            ->select('users.name', 'users.position_title')
            ->get()->toArray();

        return compact(
            'data','day','date','month','year','carbonInstance',
            'juara','category','bods','carbonInstance_startDate','carbonInstance_endDate',
            'judges', 'carbonInstance_rapatJuri','carbonInstance_rapatDirektur','carbonInstance_penetapanJuara'
        );
    }

    public function downloadWord(int $id)
    {
        $payload  = $this->buildBeritaAcaraPayload($id);
        $filename = Str::slug($payload['data']->event_name).'_Berita_Acara.doc';

        $html = view('auth.admin.berita-acara.word', $payload)->render();

        return response($html)
            ->header('Content-Type', 'application/msword; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

}