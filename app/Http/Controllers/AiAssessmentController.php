<?php

namespace App\Http\Controllers;

use App\Services\AiPaperAnalyzerGemini as Analyzer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AiAssessmentController extends Controller
{

    public function view(Request $request, int $paperId, Analyzer $svc)
    {
        $paper = DB::table('papers as p')
            ->leftJoin('teams as t', 't.id', '=', 'p.team_id')
            ->leftJoin('pvt_event_teams as pet', 'pet.team_id', '=', 't.id')
            ->leftJoin('events as e', 'e.id', '=', 'pet.event_id')
            ->where('p.id', $paperId)
            ->select([
                'p.id as paper_id','p.innovation_title','p.full_paper','p.file_review',
                't.id as team_id',                           
                'pet.id as event_team_id','pet.event_id',   
                'e.event_name','e.year',
            ])
            ->orderByDesc('pet.id')
            ->first();

        if (!$paper) abort(404, 'Paper tidak ditemukan');

        $innovationTitle = (string) ($paper->innovation_title ?? '');
        $eventTitle = null;
        if (!empty($paper->event_name) && !empty($paper->year)) {
            $eventTitle = $paper->event_name.' Tahun '.$paper->year; 
        }
        $teamId      = (int)($paper->team_id ?? 0);
        $eventTeamId = (int)($paper->event_team_id ?? 0);
        $eventId  = (int)($request->query('event_id') ?: ($paper->event_id ?? 0));
        $category = (string)$request->query('category', 'BI/II');
        
        // Rubric penilaian
        $rubric = $svc->getRubricForEvent($eventId, $category, $teamId, $eventTeamId);
        if (!$rubric && $category) {
            $rubric = $svc->getRubricForEvent($eventId, null, $teamId, $eventTeamId);
        }
        if (!$rubric) {
            return view('ai.analysis', [
                'meta' => [
                    'paperId'=>$paperId,
                    'innovationTitle' => $innovationTitle, 
                    'eventId'=>$eventId,
                    'eventTitle'=> $eventTitle, 
                    'category'=>$category,
                    'cached'=>false,'total'=>0,
                ],
                'html' => "<div class='alert alert-warning mb-0'>Rubric/point belum di-assign untuk event/kategori ini.</div>",
            ]);
        }

        $text = $svc->extractTextFromPaper([
            'full_paper'=>$paper->full_paper,
            'file_review'=>$paper->file_review,
        ]);
        if ($text === '') {
            return view('ai.analysis', [
                'meta' => [
                    'paperId'=>$paperId,
                    'innovationTitle' => $innovationTitle,
                    'eventId'=>$eventId,
                    'eventTitle'=> $eventTitle, 
                    'category'=>$category,
                    'cached'=>false,'total'=>0,
                ],
                'html' => "<div class='alert alert-danger mb-0'>Gagal membaca konten makalah.</div>",
            ]);
        }

        $hash = hash('sha256', $text.'|'.json_encode($rubric));
        $key  = "gemini:paper:$paperId:$hash";

        if (Cache::has($key)) {
            $scored = Cache::get($key);
            $cached = true;
        } else {
            $scored = $svc->scoreWithGemini($text, $rubric, env('GEMINI_MODEL'));
            Cache::put($key, $scored, now()->addDays(14));
            $cached = false;
        }

        $rubricById = collect($rubric)->keyBy('assessment_point_id');
        $decorated = collect($scored['per_point'])->map(function ($r) use ($rubricById) {
            $meta = $rubricById->get($r['assessment_point_id'], []);
            return [
                'assessment_point_id' => (int)($r['assessment_point_id'] ?? 0),
                'point'               => (string)($meta['point']  ?? ''),
                'detail'              => (string)($meta['detail'] ?? ''),
                'score_ai'            => (int)($r['score_ai'] ?? 0),
                'score_max'           => (int)($r['score_max'] ?? 0),
                'justification'       => (string)($r['justification'] ?? ''),
            ];
        })->values()->toArray();

        $html = $this->renderHtml($scored['summary'], (int)$scored['total'], $decorated);

        return view('ai.analysis', [
            'meta' => [
                'paperId'=>$paperId,
                'innovationTitle' => $innovationTitle,
                'eventId'=>$eventId,
                'eventTitle'=> $eventTitle, 
                'category'=>$category,
                'cached'=>$cached,
                'total'=>(int)$scored['total'],
            ],
            'html' => $html,
        ]);
    }

    public function analyze(Request $request, int $paperId, Analyzer $svc)
    {
        $paper = DB::table('papers as p')
            ->leftJoin('teams as t', 't.id', '=', 'p.team_id')
            ->leftJoin('pvt_event_teams as pet', 'pet.team_id', '=', 't.id')
            ->leftJoin('events as e', 'e.id', '=', 'pet.event_id')
            ->where('p.id', $paperId)
            ->select([
                'p.id as paper_id','p.innovation_title','p.full_paper','p.file_review',
                't.id as team_id',                           
                'pet.id as event_team_id','pet.event_id',   
                'e.event_name','e.year',
            ])
            ->orderByDesc('pet.id')
            ->first();

        if (!$paper) {
            return response()->json(['ok'=>false,'message'=>'Paper tidak ditemukan'], 404);
        }

        $innovationTitle = (string) ($paper->innovation_title ?? '');
        $eventIdReq = (int) $request->input('event_id');
        $teamId      = (int)($paper->team_id ?? 0);
        $eventTeamId = (int)($paper->event_team_id ?? 0);
        $eventId    = $eventIdReq ?: (int) ($paper->event_id ?? 0);
        $category   = (string) $request->get('category', 'BI/II');

        // Rubric Penilaian
        $rubric = $svc->getRubricForEvent($eventId, $category, $teamId, $eventTeamId);
        if (!$rubric && $category) {
            $rubric = $svc->getRubricForEvent($eventId, null, $teamId, $eventTeamId);
        }
        if (!$rubric) {
            Log::warning('[AI] rubric tetap kosong', ['event_id'=>$eventId, 'category'=>$category]);
            return response()->json(['ok'=>false,'message'=>'Rubric/point belum di-assign.'], 422);
        }

        // Ekstrak paper
        $text = $svc->extractTextFromPaper([
            'full_paper' => $paper->full_paper,
            'file_review'=> $paper->file_review,
        ]);
        if ($text === '') {
            Log::warning('[AI] extractTextFromPaper kosong', ['paper_id'=>$paperId]);
            return response()->json(['ok'=>false,'message'=>'Gagal membaca konten makalah.'], 422);
        }

        //Cache
        $hash = hash('sha256', $eventTeamId.'|'.$teamId.'|'.$text.'|'.json_encode($rubric));
        $key  = "gemini:paper:$paperId:$hash";


        if (Cache::has($key)) {
            $scored = Cache::get($key);
        } else {
            try {
                $scored = $svc->scoreWithGemini($text, $rubric, env('GEMINI_MODEL'));
                Cache::put($key, $scored, now()->addDays(14));
            } catch (\Throwable $e) {
                return response()->json(['ok'=>false,'message'=>'Gagal memanggil Gemini API.'], 500);
            }
        }

        $rubricById = collect($rubric)->keyBy('assessment_point_id'); 
        $decorated = collect($scored['per_point'])->map(function ($r) use ($rubricById) {
            $meta = $rubricById->get($r['assessment_point_id'], []);
            return [
                'assessment_point_id' => (int)($r['assessment_point_id'] ?? 0),
                'point'               => (string)($meta['point']  ?? ''),
                'detail'              => (string)($meta['detail'] ?? ''),
                'score_ai'            => (int)($r['score_ai'] ?? 0),
                'score_max'           => (int)($r['score_max'] ?? 0),
                'justification'       => (string)($r['justification'] ?? ''),
            ];
        })->values()->toArray();

        $html = $this->renderHtml($scored['summary'], (int)$scored['total'], $decorated);

        return response()->json([
            'ok'     => true,
            'cached' => Cache::has($key),
            'html'   => $html,
            'scores' => $decorated, 
        ]);
    }

    private function renderHtml(string $summary, int $total, array $perPoint): string
    {
        $rows = collect($perPoint)->map(function ($r) {
            $id   = (int)($r['assessment_point_id'] ?? 0);
            $nm   = e($r['point']  ?? '');
            $dt   = e($r['detail'] ?? '');
            $dtShort = $dt !== '' ? e(mb_strimwidth($dt, 0, 180, '…', 'UTF-8')) : '';
            $sc   = (int)($r['score_ai']  ?? 0);
            $mx   = (int)($r['score_max'] ?? 0);
            $al   = e($r['justification'] ?? '');
            return "
                <tr>
                  <td class='text-nowrap align-top'>{$id}</td>
                  <td class='align-top'>
                    <div class='fw-semibold'>{$nm}</div>"
                    . ($dtShort ? "<div class='small text-muted'>{$dtShort}</div>" : "") .
                  "</td>
                  <td class='align-top text-center'>{$sc} / {$mx}</td>
                  <td class='small text-muted align-top'>{$al}</td>
                </tr>";
        })->implode('');

        return "
          <div class='mb-3'>
            <div class='fw-bold mb-1'>Ringkasan</div>
            <div>".e($summary)."</div>
          </div>
          <div class='mb-2 fw-bold'>Total Skor AI: {$total}</div>
          <div class='table-responsive'>
            <table class='table table-sm align-middle'>
              <thead>
                <tr>
                  <th class='text-nowrap'>No.</th>
                  <th>Poin</th>
                  <th class='text-center'>Skor</th>
                  <th>Alasan</th>
                </tr>
              </thead>
              <tbody>{$rows}</tbody>
            </table>
          </div>";
    }
}
