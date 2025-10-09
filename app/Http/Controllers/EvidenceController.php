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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\ReplicationRequest;


class EvidenceController extends Controller
{
    function index()
    {
        $categories = Category::orderBy('category_name', 'asc')->get();

        return view('auth.admin.dokumentasi.evidence.index', compact('categories'));
    }


    function List_paper($categoryId, Request $request)
    {

        $category = Category::find($categoryId);
        return view('auth.admin.dokumentasi.evidence.list-innovations', compact('category'));
    }

    function paper_detail($id)
    {

        $team = Team::findOrFail($id);
        $loggedEmployeeId = Auth::user()->employee_id;

        //  Log::debug('paper_detail called', [
        // 'logged_user_id' => Auth::id(),
        // 'logged_employee_id' => $loggedEmployeeId,
        // 'team_id' => $team->id,
        // ]);

        $isMember = DB::table('pvt_members')
            ->where('team_id', $team->id)
            ->where('employee_id', $loggedEmployeeId)
            ->exists();

        //  Log::debug('Check if logged user is team member', [
        // 'is_member' => $isMember,
        // ]);

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

        $replicatedBy = ReplicationRequest::with('creator')
            ->where('team_id', $teamId)
            ->where('status', 'approved')
            ->where('replication_status', 'replicated')
            ->orderByDesc('created_at')
            ->get();

        return view('auth.admin.dokumentasi.evidence.detail-team', compact(
            'teamMember', 
            'papers', 
            'teamId', 
            'team',
            'outsourceMember',
            'isMember',
            'replicatedBy'
        ));
    }

        public function download($id)
        {
            if (!in_array(Auth::user()->role, ['Admin', 'Superadmin'])) {
                abort(403, 'Anda tidak memiliki izin untuk mengunduh file ini.');
            }
    
            $paper = Paper::findOrFail($id);
    
            // --- WATERMARK INFO ---
            $currentDateTime = Carbon::now()->format('l, d F Y H:i:s');
            $userEmail = auth()->user()->email;
            $userIp = request()->ip();
            $watermarkText = "{$currentDateTime}\nDidownload oleh {$userEmail}\nIP: {$userIp}";
    
            // --- NORMALISASI PATH & VALIDASI FILE ---
            $rawPath = (string) $paper->full_paper;
            $normalizedRel = ltrim(str_replace(['f: ', 'F: '], '', $rawPath), '/');
            $absPath = Storage::disk('public')->path($normalizedRel);
    
            if (!file_exists($absPath)) abort(404, 'File tidak ditemukan.');
            if (!is_readable($absPath)) abort(500, 'File tidak dapat dibaca server.');
            $ext  = strtolower(pathinfo($absPath, PATHINFO_EXTENSION));
            $mime = @mime_content_type($absPath) ?: null;
            if ($ext !== 'pdf' || ($mime && stripos($mime, 'pdf') === false)) {
                abort(415, 'File bukan PDF / format tidak didukung.');
            }
    
            try {
                $pdf = new Fpdi();
    
                $pageCount = $pdf->setSourceFile($absPath);
    
                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $tplIdx = $pdf->importPage($pageNo);
                    $pdf->AddPage();
                    $pdf->useTemplate($tplIdx, 0, 0);
    
                    if (method_exists($pdf, 'SetAlpha')) $pdf->SetAlpha(0.1);
                    $pdf->SetFont('helvetica', 'B', 40);
                    $pdf->SetTextColor(255, 0, 0);
                    if (method_exists($pdf, 'StartTransform')) {
                        $pdf->StartTransform();
                        $pdf->Rotate(45, 150, 50);
                    }
                    $pdf->MultiCell(160, 180, $watermarkText, 0, 'C');
                    if (method_exists($pdf, 'StopTransform')) $pdf->StopTransform();
                    if (method_exists($pdf, 'SetAlpha')) $pdf->SetAlpha(1);
                }
    
                $outName = ($paper->innovation_title ?? 'paper') . '_watermarked.pdf';
    
                return response()->make($pdf->Output($outName, 'I'), 200, [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $outName . '"',
                    'X-Watermark'         => 'applied',
                ]);
    
            } catch (\Throwable $e) {
                $outName = ($paper->innovation_title ?? 'paper') . '.pdf';
                return response()->download($absPath, $outName, [
                    'Content-Type'  => 'application/pdf',
                    'X-Watermark'   => 'skipped',
                    'X-Reason'      => 'fpdi_parse_failed',
                ]);
            }
        }

        public function preview($id)
    {
        $paper = Paper::findOrFail($id);
        $fileUrl = route('evidence.pdf-stream-watermarked', ['id' => $paper->id]);
        return view('auth.admin.dokumentasi.evidence.preview', compact('paper', 'fileUrl'));
    }

        public function pdfStreamWatermarked($id)
    {
        $paper = Paper::findOrFail($id);
        $filePath = storage_path('app/public/' . str_replace('f: ', '', $paper->full_paper));

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }

        $currentDateTime = Carbon::now()->format('l, d F Y H:i:s');
        $userEmail = auth()->user()->email;
        $userIp = request()->ip();
        $watermarkText = "{$currentDateTime}\nDilihat oleh {$userEmail}\nIP: {$userIp}";

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($filePath);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $tplIdx = $pdf->importPage($pageNo);
            $pdf->AddPage();
            $pdf->useTemplate($tplIdx, 0, 0);

            // Tambahkan watermark
            $pdf->SetAlpha(0.1);
            $pdf->SetFont('helvetica', 'B', 40);
            $pdf->SetTextColor(255, 0, 0);
            $pdf->StartTransform();
            $pdf->Rotate(45, 150, 50);
            $pdf->MultiCell(160, 180, $watermarkText, 0, 'C');
            $pdf->StopTransform();
            $pdf->SetAlpha(1);
        }

        return response()->make($pdf->Output($paper->innovation_title . '_preview.pdf', 'I'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $paper->innovation_title . '_preview.pdf"',
        ]);
    }

}