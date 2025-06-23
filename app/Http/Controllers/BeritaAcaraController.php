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

            // Proses penyimpanan berita acara
            BeritaAcara::create([
                'event_id' => $request->input('event_id'),
                'no_surat' => $request->input('no_surat'),
                'jenis_event' => $request->input('jenis_event'),
                'penetapan_juara' => $request->input('penetapan_juara')
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

        return redirect()->route('assessment.penetapanJuara')->with('success', 'Berita Acara Berhasil Di Buat');
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
        // Ambil data utama
        $category = Category::all();
        $data = BeritaAcara::join('events', 'berita_acaras.event_id', 'events.id')
            ->where('berita_acaras.id', $id)
            ->select('berita_acaras.*', 'events.id as eventID', 'events.event_name', 'events.year', 'events.date_start', 'events.date_end')
            ->firstOrFail(); // Gunakan firstOrFail() untuk mencegah error jika data tidak ditemukan

        $idEvent = $data->eventID;

        // Ubah tanggal ke format lokal
        $carbonInstance = Carbon::parse($data->penetapan_juara);
        setlocale(LC_TIME, 'id_ID');
        $day = $carbonInstance->isoFormat('dddd');
        $date = numberToWords($carbonInstance->isoFormat('D'));
        $month = $carbonInstance->isoFormat('MMMM');
        $year = numberToWords($carbonInstance->isoFormat('YYYY'));

        $carbonInstance_startDate = Carbon::parse($data->date_start);
        $carbonInstance_endDate = Carbon::parse($data->date_end);

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
                        DB::raw('RANK() OVER (ORDER BY pvt_event_teams.final_score) as rank')
                    )
                    ->orderBy('rank', 'ASC')
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
                ->groupBy('pvt_event_teams.id', 'teams.team_name', 'papers.innovation_title', 'companies.company_name', 'pvt_event_teams.is_honorable_winner','pvt_event_teams.is_best_of_the_best', 'pvt_event_teams.final_score')
                ->select(
                    'teams.team_name as teamname', 
                    'papers.innovation_title', 
                    'companies.company_name',
                    'pvt_event_teams.is_honorable_winner',
                    'pvt_event_teams.is_best_of_the_best',
                    'pvt_event_teams.final_score',
                    DB::raw('RANK() OVER (ORDER BY COALESCE(pvt_event_teams.final_score, 0) DESC) as rank') // Tambahkan ranking
                )
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
                    DB::raw('RANK() OVER (ORDER BY COALESCE(pvt_event_teams.final_score, 0) DESC) as rank') // Tambahkan ranking
                )
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
                    DB::raw('RANK() OVER (ORDER BY COALESCE(pvt_event_teams.final_score, 0) DESC) as rank') // Tambahkan ranking
                )
                ->take(1)
                ->get()
                ->toArray();
        } else {
            $juara['Best Of The Best'] = [];
        }

        // Ambil data BOD
        $bods = BodEvent::join('users', 'users.employee_id', '=', 'bod_events.employee_id')
            ->where('event_id', '=', $idEvent)
            ->select('users.name', 'users.position_title')
            ->get()
            ->toArray();

        // Generate PDF
        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);
        $html = view('auth.admin.berita-acara.pdf', compact(
            'data', 'day', 'date', 'month', 'year', 
            'carbonInstance', 'juara', 'category', 'bods',
            'carbonInstance_startDate', 'carbonInstance_endDate'
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
            'carbonInstance_endDate'
        ))->render();

        $mpdf->WriteHTML($html);
        $content = $mpdf->Output('', 'S');

        $filename = str_replace(' ', '_', $data->event_name) . '_Berita_Acara.pdf';

        return response($content)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}