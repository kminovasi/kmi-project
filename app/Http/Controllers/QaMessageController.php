<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Services\AiPaperAnalyzerGemini as Analyzer;

class QaMessageController extends Controller
{
    private int $ttlDays = 14;

    private function key(int $paperId): string
    {
        return "qa:paper:{$paperId}";
    }

    private function fmt(?string $iso): string
    {
        if (!$iso) return '';
        try {
            return Carbon::parse($iso)
                ->timezone(config('app.timezone'))
                ->format('d M Y H:i');
        } catch (\Throwable $e) {
            return (string) $iso;
        }
    }

    private function getPaperText(int $paperId, Analyzer $svc): string
    {
        $paper = DB::table('papers as p')
            ->leftJoin('teams as t', 't.id', '=', 'p.team_id')
            ->leftJoin('pvt_event_teams as pet', 'pet.team_id', '=', 't.id')
            ->leftJoin('events as e', 'e.id', '=', 'pet.event_id')
            ->where('p.id', $paperId)
            ->select(['p.id as paper_id','p.full_paper','p.file_review','pet.event_id'])
            ->orderByDesc('pet.id')
            ->first();

        if (!$paper) return '';

        return $svc->extractTextFromPaper([
            'full_paper'  => $paper->full_paper,
            'file_review' => $paper->file_review,
        ]);
    }

    private function getDatabaseSnapshotForAnalyzer(int $paperId, Analyzer $svc, int $maxRowsPerTable = 100): string
    {
        $dbName = DB::getDatabaseName();

        $pivot = DB::table('papers as p')
            ->leftJoin('teams as t', 't.id', '=', 'p.team_id')
            ->leftJoin('pvt_event_teams as pet', 'pet.team_id', '=', 't.id')
            ->leftJoin('events as e', 'e.id', '=', 'pet.event_id')
            ->where('p.id', $paperId)
            ->select([
                'p.id as paper_id',
                't.id as team_id',
                'pet.event_id',
            ])
            ->orderByDesc('pet.id')
            ->first();

        $ctx = [
            'paper_id' => $pivot->paper_id ?? null,
            'team_id'  => $pivot->team_id  ?? null,
            'event_id' => $pivot->event_id ?? null,
        ];

        $tables = collect(DB::select("
            SELECT table_name
            FROM information_schema.tables
            WHERE table_schema = DATABASE() AND table_type='BASE TABLE'
            ORDER BY table_name
        "))->pluck('table_name');

        $exclude = [
            'migrations','password_resets','password_reset_tokens',
            'personal_access_tokens','sessions','failed_jobs','jobs',
            'cache','cache_locks',
        ];

        $tables = $tables->reject(fn($t) => in_array($t, $exclude));

        $lines = [];
        $lines[] = "=== DATABASE SNAPSHOT: {$dbName} ===";
        $lines[] = "CONTEXT: ".json_encode($ctx, JSON_UNESCAPED_UNICODE);

        foreach ($tables as $table) {
            $cols = collect(DB::select("
                SELECT column_name, data_type
                FROM information_schema.columns
                WHERE table_schema = DATABASE() AND table_name = ?
                ORDER BY ordinal_position
            ", [$table]))->map(fn($r) => "{$r->column_name} {$r->data_type}");

            $lines[] = "";
            $lines[] = "=== TABLE: {$table} ===";
            $lines[] = "COLUMNS: ".implode(', ', $cols->toArray());

            $colNames = $cols->map(fn($x) => explode(' ', $x)[0])->toArray();
            $query = DB::table($table);

            $didFilter = false;
            foreach (['paper_id','team_id','event_id'] as $k) {
                if ($ctx[$k] && in_array($k, $colNames, true)) {
                    $query->orWhere($k, $ctx[$k]);
                    $didFilter = true;
                }
            }

            $rows = $query->limit($maxRowsPerTable)->get();
            $lines[] = "FILTERED: ".($didFilter ? 'yes' : 'no')." | ROWS_SAMPLE_LIMIT: {$maxRowsPerTable}";
            foreach ($rows as $row) {
                $lines[] = "- ".json_encode($row, JSON_UNESCAPED_UNICODE);
            }
        }

        return implode("\n", $lines);
    }

    public function index(Request $request)
    {
        $paperId = (int) $request->query('paper_id');
        if (!$paperId) {
            return response()->json(['message' => 'paper_id required'], 400);
        }

        $list = Cache::get($this->key($paperId), []);
        $uid  = Auth::id();

        $out = collect($list)->map(function ($m) use ($uid) {
            $raw = $m['created_at'] ?? null;

            return [
                'id'              => $m['id'] ?? null,
                'body'            => $m['body'] ?? '',
                'author_name'     => $m['author_name'] ?? '—',
                'role'            => $m['role'] ?? null,
                'created_at'      => $this->fmt(is_string($raw) ? $raw : null),
                'created_at_iso'  => $raw,
                'mine'            => ($uid && ($m['user_id'] ?? null) == $uid),
            ];
        })->values();

        return response()->json($out);
    }

    public function store(Request $request, Analyzer $ai)
    {
        $data = $request->validate([
            'paper_id' => 'required|integer',
            'body'     => 'required|string|max:2000',
        ]);

        $paperId = (int) $data['paper_id'];
        $key     = $this->key($paperId);

        $isoNow = now()->toIso8601String();
        $msg = [
            'id'          => (string) Str::ulid(),
            'paper_id'    => $paperId,
            'user_id'     => Auth::id(),
            'author_name' => Auth::user()->name ?? 'User',
            'role'        => Auth::user()->role ?? null,
            'body'        => trim($data['body']),
            'created_at'  => $isoNow,
        ];

        $list   = Cache::get($key, []);
        $list[] = $msg;
        Cache::put($key, $list, now()->addDays($this->ttlDays));

        try {
            $paperText = $this->getPaperText($paperId, $ai);
            if ($paperText !== '') {
                $history  = array_slice($list, -5);
                $histText = collect($history)->map(fn($h) =>
                    (($h['role'] ?? '') === 'AI' ? 'AI' : ($h['author_name'] ?? 'User')) . ': ' . $h['body']
                )->implode("\n");

                $sys = "Kamu adalah asisten AI untuk juri lomba inovasi. Jawab ringkas (maks 6 kalimat), "
                     . "gunakan Bahasa Indonesia, dan rujuk pada isi makalah bila memungkinkan. "
                     . "Jika informasi tidak ada di makalah, katakan tidak ditemukan secara sopan.";

                $payload =
                    "Cuplikan makalah (mungkin terpotong):\n" .
                    mb_substr($paperText, 0, 12000) . "\n\n" .
                    "Riwayat percakapan:\n{$histText}\n\n" .
                    "Pertanyaan terbaru:\n" . $msg['body'];

                $answer = $this->askGemini($sys, $payload);

                if ($answer) {
                    $botIso = now()->toIso8601String();
                    $bot = [
                        'id'          => (string) Str::ulid(),
                        'paper_id'    => $paperId,
                        'user_id'     => null,
                        'author_name' => 'AI',
                        'role'        => 'AI',
                        'body'        => trim($answer),
                        'created_at'  => $botIso,
                    ];
                    $list[] = $bot;
                    Cache::put($key, $list, now()->addDays($this->ttlDays));
                }
            }
        } catch (\Throwable $e) {
            // diamkan error quick-reply
        }

        return response()->json([
            'ok'              => true,
            'id'              => $msg['id'],
            'created_at_iso'  => $isoNow,
            'created_at_fmt'  => $this->fmt($isoNow),
        ]);
    }

    private function askGemini(string $system, string $user): ?string
    {
        if (!env('GEMINI_API_KEY')) {
            return null;
        }

        $client = new Client([
            'base_uri' => rtrim(env('GEMINI_API_BASE','https://generativelanguage.googleapis.com'),'/').'/v1beta/',
            'timeout'  => 120,
        ]);

        $model = env('GEMINI_MODEL','gemini-2.5-flash');

        $resp = $client->post('models/'.$model.':generateContent', [
            'headers' => [
                'x-goog-api-key' => env('GEMINI_API_KEY'),
                'Content-Type'   => 'application/json',
            ],
            'json' => [
                'system_instruction' => ['parts' => [ ['text' => $system] ]],
                'contents' => [[ 'parts' => [ ['text' => $user] ] ]],
                'generationConfig' => [
                    'temperature'      => 0.3,
                    'maxOutputTokens'  => 512,
                    'responseMimeType' => 'text/plain',
                ],
            ],
        ]);

        $data = json_decode($resp->getBody()->getContents(), true);
        return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
    }

    private function askGeminiJson(string $system, string $user): ?array
    {
        if (!env('GEMINI_API_KEY')) {
            return null;
        }

        $client = new Client([
            'base_uri' => rtrim(env('GEMINI_API_BASE','https://generativelanguage.googleapis.com'),'/').'/v1beta/',
            'timeout'  => 120,
        ]);

        $model = env('GEMINI_MODEL','gemini-2.5-flash');

        try {
            $resp = $client->post('models/'.$model.':generateContent', [
                'headers' => [
                    'x-goog-api-key' => env('GEMINI_API_KEY'),
                    'Content-Type'   => 'application/json',
                ],
                'json' => [
                    'system_instruction' => ['parts' => [ ['text' => $system] ]],
                    'contents' => [[ 'parts' => [ ['text' => $user] ] ]],
                    'generationConfig' => [
                        'temperature'      => 0.2,
                        'maxOutputTokens'  => 1200,
                        'responseMimeType' => 'application/json',
                    ],
                ],
            ]);

            $bodyStr = (string) $resp->getBody();
            $data = json_decode($bodyStr, true);
            if (!is_array($data)) {
                return null;
            }

            $json = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if (is_string($json)) {
                $json = json_decode($json, true);
            } else {
                $json = $data;
            }
            return is_array($json) ? $json : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function findSimilarPapers(?string $title, int $excludePaperId = 0, int $limit = 5): array
    {
        if (!$title || mb_strlen($title) < 5) {
            return [];
        }

        $tokens = collect(preg_split('/\s+/u', mb_strtolower($title)))
            ->reject(fn($t) => mb_strlen($t) < 3)
            ->take(6)
            ->values()
            ->all();

        $q = DB::table('papers as p')
            ->leftJoin('teams as t', 't.id', '=', 'p.team_id')
            ->leftJoin('pvt_event_teams as pet', 'pet.team_id', '=', 't.id')
            ->leftJoin('events as e', 'e.id', '=', 'pet.event_id')
            ->whereNull('p.deleted_at')
            ->when($excludePaperId > 0, fn($qq) => $qq->where('p.id', '<>', $excludePaperId))
            ->when(DB::getSchemaBuilder()->hasColumn('events', 'company_code'), function ($qq) {
                $qq->whereIn('e.company_code', [2000, 7000]);
            })
            ->select([
                'p.id as paper_id',
                'p.title',
                'e.name as event_name',
                'e.year as event_year',
            ])
            ->distinct()
            ->limit($limit);

        foreach ($tokens as $tok) {
            $q->orWhere('p.title', 'LIKE', '%'.$tok.'%');
        }

        $q->orWhereRaw('SOUNDEX(p.title) = SOUNDEX(?)', [$title]);

        $res = $q->get()->map(function ($r) use ($title) {
            $a = collect(preg_split('/\s+/u', mb_strtolower($title)))->reject(fn($t)=>$t==='');
            $b = collect(preg_split('/\s+/u', mb_strtolower((string) $r->title)))->reject(fn($t)=>$t==='');
            $overlap = $a->intersect($b)->count();
            $score   = $a->count() ? round($overlap / max(1,$a->count()), 2) : 0.0;

            return [
                'paper_id'   => $r->paper_id,
                'title'      => $r->title,
                'event'      => trim(($r->event_name ?? '').' '.($r->event_year ?? '')),
                'sim_score'  => $score, // 0..1
            ];
        })->sortByDesc('sim_score')->values()->all();

        return $res;
    }

    public function assessCommon(Request $request, Analyzer $svc)
    {
        $rid = (string) Str::ulid();
        $request->attributes->set('rid', $rid);

        $data = $request->validate([
            'paper_id'           => 'required|integer',
            'structure_points'   => 'array',
            'structure_points.*' => 'string',
            'framework'          => 'string|in:DMAIC,PDCA,DesignThinking',
        ]);

        $paperId  = (int) $data['paper_id'];
        $points   = $data['structure_points'] ?? [
            'Judul', 'Abstrak', 'Latar Belakang', 'Rumusan Masalah',
            'Tujuan', 'Tinjauan Pustaka', 'Metodologi/Solusi',
            'Hasil & Pembahasan', 'Analisis Finansial/Benefit', 'Kesimpulan', 'Daftar Pustaka'
        ];
        $framework = $data['framework'] ?? 'DMAIC';

        $paperMeta  = DB::table('papers')->where('id', $paperId)->select(['id','title'])->first();
        $paperTitle = $paperMeta->title ?? null;

        $paperText = $this->getPaperText($paperId, $svc);
        if ($paperText === '') {
            return response()->json(['ok' => false, 'message' => 'Konten makalah tidak ditemukan/terbaca.'], 422);
        }

        $similar = $this->findSimilarPapers($paperTitle, $paperId, 5);

        $schema = [
            'financial_benefit' => [
                'present'     => 'boolean',
                'method'      => 'string',
                'assumptions' => 'string',
                'formula'     => 'string',
                'value'       => [
                    'min'      => 'number|null',
                    'max'      => 'number|null',
                    'currency' => 'string'
                ],
                'evidence'   => 'string',
                'confidence' => 'number'
            ],
            'structure' => [
                'points' => array_map(fn($p)=>['name'=>$p,'present'=>'boolean','evidence'=>'string','score'=> 'number'], $points),
                'total_score' => 'number',
                'missing'     => ['string']
            ],
            'prior_submission' => [
                'maybe_duplicate' => 'boolean',
                'reason'          => 'string',
                'confidence'      => 'number',
            ],
            'process_validation' => [
                'framework' => 'string',
                'steps'     => [
                    ['step'=>'string','present'=>'boolean','evidence'=>'string','gap'=>'string|null','score'=>'number']
                ],
                'overall_score' => 'number',
                'recommendation'=> 'string'
            ]
        ];

        $system = "Kamu adalah asisten penilai makalah inovasi. Jawab HANYA dalam JSON valid sesuai skema. " .
                  "Jika data tidak ada, isi null/false dengan penjelasan singkat. Nilai dalam Rupiah (IDR). " .
                  "Maksimalkan keterlacakan (tunjukkan bukti ringkas berupa kutipan 1-2 kalimat dari teks).";

        $user = "TEKS MAKALAH (potong jika panjang):\n" .
                mb_substr($paperText, 0, 15000) . "\n\n" .
                "KANDIDAT MIRIP (dari basis data internal SIG Group):\n" .
                collect($similar)->map(fn($s)=>"- {$s['title']} (sim=" . $s['sim_score'] . ", event={$s['event']})")->implode("\n") .
                "\n\n" .
                "FRAMEWORK YANG DIHARAPKAN: {$framework}\n" .
                "POIN STRUKTUR YANG WAJIB DICEK:\n- " . implode("\n- ", $points) . "\n\n" .
                "Skema JSON yang HARUS kamu keluarkan (jangan ada teks di luar JSON):\n" .
                json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $json  = $this->askGeminiJson($system, $user);
        if (!$json) {
            return response()->json(['ok' => false, 'message' => 'Gagal membuat penilaian terstruktur (JSON).'], 500);
        }

        $out = [
            'ok'                 => true,
            'paper_id'           => $paperId,
            'title'              => $paperTitle,
            'similar_candidates' => $similar,
            'assessment'         => $json,
        ];

        return response()->json($out);
    }

    public function askAi(Request $request, Analyzer $svc)
    {
        $data = $request->validate([
            'paper_id' => 'required|integer',
            'question' => 'required|string|max:2000',
        ]);

        $paperId  = (int) $data['paper_id'];
        $question = trim($data['question']);
        $qKey     = "qa:ans:{$paperId}:" . substr(hash('sha256', $question), 0, 16);
        $msgKey   = $this->key($paperId);

        try {
            if (Cache::has($qKey)) {
                $answer = Cache::get($qKey);
            } else {
                $paperText = $this->getPaperText($paperId, $svc);
                if ($paperText === '') {
                    return response()->json(['ok' => false, 'message' => 'Konten makalah tidak ditemukan/terbaca.'], 422);
                }

                $history = array_slice(Cache::get($msgKey, []), -8);
                $histForModel = array_map(function ($m) {
                    return [
                        'role' => (($m['user_id'] ?? 0) ? 'user' : 'model'),
                        'text' => (string) $m['body'],
                    ];
                }, $history);

                $answer = $svc->answerQuestion($paperText, $question, $histForModel);

                Cache::put($qKey, $answer, now()->addDays($this->ttlDays));
            }

            $isoNow = now()->toIso8601String();

            $list = Cache::get($msgKey, []);
            $list[] = [
                'id'          => (string) Str::ulid(),
                'paper_id'    => $paperId,
                'user_id'     => 0,
                'author_name' => 'AI',
                'role'        => 'AI',
                'body'        => $answer,
                'created_at'  => $isoNow,
            ];
            Cache::put($msgKey, $list, now()->addDays($this->ttlDays));

            return response()->json([
                'ok'             => true,
                'answer'         => $answer,
                'created_at_iso' => $isoNow,
                'created_at_fmt' => $this->fmt($isoNow),
            ]);

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $status = $e->getResponse() ? $e->getResponse()->getStatusCode() : 0;
            $short  = $status ? "Gemini HTTP {$status}" : 'Gagal menghubungi Gemini';
            return response()->json(['ok' => false, 'message' => $short], 500);

        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
