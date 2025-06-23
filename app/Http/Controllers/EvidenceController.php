<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Paper;
use App\Models\Team;
use Illuminate\Http\Request;
use setasign\Fpdi\Tcpdf\Fpdi;
use TCPDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EvidenceController extends Controller
{
    function index()
    {
        $categories = Category::all();

        return view('auth.admin.dokumentasi.evidence.index', compact('categories'));
    }


    function List_paper($categoryId, Request $request)
    {

        $category = Category::find($categoryId);

        // mendapatkan semua data paper dengan kategori dan event yang status finish


        return view('auth.admin.dokumentasi.evidence.list-innovations', compact('category'));
    }

    function paper_detail($id)
    {

        $team = Team::findOrFail($id);

        // Ambil tim berdasarkan team_id
        $papers = DB::table('teams')
            ->leftJoin('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->leftJoin('papers', 'teams.id', '=', 'papers.team_id')
            ->leftJoin('events', 'pvt_event_teams.event_id', '=', 'events.id')
            ->leftJoin('themes', 'teams.theme_id', '=', 'themes.id')
            ->leftJoin('document_supportings', 'papers.id', '=', 'document_supportings.paper_id')
            ->where('teams.id', $id)
            ->select(
                'papers.*',
                'pvt_event_teams.final_score',
                'pvt_event_teams.total_score_on_desk',
                'pvt_event_teams.total_score_presentation',
                'pvt_event_teams.total_score_caucus',
                'pvt_event_teams.is_best_of_the_best',
                'themes.theme_name',
                'events.event_name',
                'document_supportings.path',
                'teams.id as team_id',
                'papers.id as paper_id'
            )
            ->limit(1)
            ->get();
        
        // Mengambil elemen pertama dari koleksi
        $paper = $papers->first();

        // Mengakses properti team_id
        $teamId = $paper->team_id;

        // mendapatkan data member berdasarkan id team
        $teamMember = $team->pvtMembers()->with('user')->get();
        
        $outsourceMember = DB::table('ph2_members')
            ->where('team_id', $teamId)
            ->get()
            ->toArray();

        return view('auth.admin.dokumentasi.evidence.detail-team', compact(
            'teamMember', 
            'papers', 
            'teamId', 
            'team',
            'outsourceMember'
        ));
    }

    public function download($id)
    {
        $paper = Paper::findOrFail($id);


        // Ambil informasi untuk watermark
        $currentDateTime = Carbon::now()->format('l, d F Y H:i:s'); // Tanggal dan jam
        $userEmail = auth()->user()->email; // Email pengguna
        $userIp = request()->ip(); // IP pengguna

        // Gabungkan semua informasi ke dalam satu string watermark
        $watermarkText = "{$currentDateTime}\nDidownload oleh {$userEmail}\nIP: {$userIp}";

        $filePath = storage_path('app/public/' . str_replace('f: ', '', $paper->full_paper));

        // Buat objek FPDI
        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($filePath);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $tplIdx = $pdf->importPage($pageNo);
            $pdf->AddPage();
            $pdf->useTemplate($tplIdx, 0, 0);

            // Tambahkan watermark
            $pdf->SetAlpha(0.1); // Transparansi watermark
            $pdf->SetFont('helvetica', 'B', 40);
            $pdf->SetTextColor(255, 0, 0);

            // Memulai transformasi untuk rotasi
            $pdf->StartTransform();
            $pdf->Rotate(45, 150, 50); // Atur sudut, x, y sesuai kebutuhan
            $pdf->MultiCell(160, 180, $watermarkText, 0, 'C'); // Atur posisi watermark
            $pdf->StopTransform(); // Akhiri transformasi

            $pdf->SetAlpha(1); // Reset transparansi
        }

        // Berikan file PDF langsung sebagai respons unduhan
        return response()->make($pdf->Output($paper->innovation_title . '_watermarked.pdf', 'I'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $paper->innovation_title . '_watermarked.pdf"'
        ]);
    }
}