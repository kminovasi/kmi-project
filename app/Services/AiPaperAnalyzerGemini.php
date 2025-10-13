<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser as PdfParser;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Services\AiGuard;
use Log;
use GuzzleHttp\Exception\RequestException;
use Throwable;


class AiPaperAnalyzerGemini
{
    private AiGuard $guard; 

    public function __construct(AiGuard $guard)  
    {
        $this->guard = $guard;
    }

    public function getRubricForEvent(int $eventId, string $category = null, int $teamId = 0, int $eventTeamId = 0): array {
        $tbl  = 'pvt_assessment_events';
        $cols = \Illuminate\Support\Facades\Schema::getColumnListing($tbl);

        $q = \DB::table("$tbl as pae")->where('pae.event_id', $eventId);

        if ($eventTeamId > 0 && in_array('event_team_id', $cols)) {
            $q->where('pae.event_team_id', $eventTeamId);
        } elseif ($teamId > 0 && in_array('team_id', $cols)) {
            $q->where('pae.team_id', $teamId);
        } elseif ($eventTeamId > 0 && \Illuminate\Support\Facades\Schema::hasTable('pvt_assessment_event_teams')) {
            $pcols = \Illuminate\Support\Facades\Schema::getColumnListing('pvt_assessment_event_teams');
            if (in_array('event_team_id',$pcols) && in_array('assessment_event_id',$pcols)) {
                $q->join('pvt_assessment_event_teams as paet','paet.assessment_event_id','=','pae.id')
                  ->where('paet.event_team_id', $eventTeamId);
            }
        }

        if (in_array('stage', $cols)) {
            $q->whereIn('pae.stage', ['on desk','On Desk','ondesk','on_desk']);
        }
        if (in_array('status_point', $cols)) {
            $q->whereIn('pae.status_point', ['active','Active','aktif','Aktif',1,'1',true]);
        } elseif (in_array('status', $cols)) {
            $q->whereIn('pae.status', ['active','Active','aktif','Aktif',1,'1',true]);
        }
        if (in_array('assign', $cols)) {
            $q->whereIn('pae.assign', [1,'1',true]);
        } elseif (in_array('is_assigned', $cols)) {
            $q->whereIn('pae.is_assigned', [1,'1',true]);
        }

        if ($category) {
            if (in_array('pdca', $cols))          $q->where('pae.pdca', $category);
            elseif (in_array('category',$cols))   $q->where('pae.category', $category);
        }

        $rows = $q->select('pae.id','pae.point','pae.detail_point','pae.score_max')
                  ->orderBy('pae.id')
                  ->get();

        return $rows->map(fn($r) => [
            'assessment_point_id' => (int)$r->id,
            'point'               => (string)$r->point,
            'detail'              => (string)$r->detail_point,
            'score_max'           => (int)$r->score_max,
        ])->toArray();
    }

    public function extractTextFromPaper(array $paper): string
    {
        $candidates = array_values(array_filter([
            $paper['full_paper']  ?? null,
            $paper['file_review'] ?? null,
        ]));

        foreach ($candidates as $rel) {
            $paths = [];
            try { $paths[] = Storage::path($rel); } catch (\Throwable $e) {}
            try { $paths[] = Storage::disk('public')->path($rel); } catch (\Throwable $e) {}
            if (is_file($rel)) $paths[] = $rel;

            $abs = collect($paths)->first(fn($p)=>is_file($p)) ?: null;
            if (!$abs) continue;

            try {
                $ext  = strtolower(pathinfo($abs, PATHINFO_EXTENSION));

                if ($ext === 'pdf') {
                    $pdf = (new PdfParser())->parseFile($abs);
                    $txt = trim($pdf->getText());
                    if ($txt !== '') return $txt;
                } else {
                    $txt = trim((string)file_get_contents($abs));
                    if ($txt !== '') return $txt;
                }
            } catch (\Throwable $e) {
            }
        }

        return '';
    }

    public function scoreWithGemini(string $paperText, array $rubric, string $model = null): array
    {
        $model   = $model ?: env('GEMINI_MODEL','gemini-2.5-flash');
        $excerpt = mb_substr($paperText, 0, 18000);

        $responseSchema = [
            'type' => 'object',
            'properties' => [
                'summary' => ['type' => 'string'],
                'per_point' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'assessment_point_id' => ['type'=>'integer'],
                            'score_ai'            => ['type'=>'integer'],
                            'score_max'           => ['type'=>'integer'],
                            'justification'       => ['type'=>'string'],
                        ],
                        'required' => ['assessment_point_id','score_ai','score_max'],
                        'propertyOrdering' => ['assessment_point_id','score_ai','score_max','justification']
                    ]
                ],
            ],
            'required' => ['summary','per_point'],
            'propertyOrdering' => ['summary','per_point']
        ];

        $systemText = "Anda adalah juri inovasi. Nilai makalah berdasarkan rubric.
        - Beri score_ai integer 0..score_max untuk tiap assessment_point_id
        - Justification <=2 kalimat, Bahasa Indonesia
        - Wajib keluarkan JSON sesuai schema (tanpa field tambahan).";

        $userText = json_encode([
            'paper_excerpt' => $excerpt,
            'rubric'        => $rubric,
            'instruksi'     => 'Kembalikan JSON sesuai schema.',
        ], JSON_UNESCAPED_UNICODE);

        $client = new Client([
            'base_uri' => rtrim(env('GEMINI_API_BASE','https://generativelanguage.googleapis.com'),'/').'/v1beta/',
            'timeout'  => 120,
        ]);

        $resp = $client->post('models/'.$model.':generateContent', [
            'headers' => [
                'x-goog-api-key' => env('GEMINI_API_KEY'),
                'Content-Type'   => 'application/json',
            ],
            'json' => [
                'system_instruction' => [
                    'parts' => [ ['text' => $systemText] ],
                ],
                'contents' => [[
                    'parts' => [ ['text' => $userText] ],
                ]],
                'generationConfig' => [
                    'temperature'        => 0.2,
                    'responseMimeType'   => 'application/json',
                    'responseSchema'     => $responseSchema,
                ],
            ],
        ]);

        $data = json_decode($resp->getBody()->getContents(), true);
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
        $parsed = json_decode($text, true) ?: ['summary'=>'','per_point'=>[]];

        $maxMap  = collect($rubric)->keyBy('assessment_point_id');
        $points  = collect($parsed['per_point'] ?? [])->map(function($r) use ($maxMap){
            $id  = (int)($r['assessment_point_id'] ?? 0);
            $max = (int)($maxMap[$id]['score_max'] ?? 0);
            $ai  = max(0, min((int)($r['score_ai'] ?? 0), $max));
            return [
                'assessment_point_id' => $id,
                'score_ai'            => $ai,
                'score_max'           => $max,
                'justification'       => (string)($r['justification'] ?? ''),
            ];
        })->values()->toArray();

        return [
            'model'     => $model,
            'summary'   => (string)($parsed['summary'] ?? ''),
            'per_point' => $points,
            'total'     => array_sum(array_column($points, 'score_ai')),
            'raw'       => $text,
        ];
    }

    public function answerQuestion(string $paperText, string $question, array $history = [], string $model = null): string
    {
        $model = $this->guard->validateModel($model ?: env('GEMINI_MODEL','gemini-2.5-flash'));
        $excerpt = $this->guard->sanitizeContext($paperText);


        $parts = [];
        foreach ($history as $turn) {
            $role = $turn['role'] ?? 'user';
            $parts[] = ['text' => ($role === 'user' ? "User: " : "AI: ") . (string)$turn['text']];
        }
        $parts[] = ['text' =>
            "Konteks makalah (ringkas):\n" . $excerpt .
            "\n\nInstruksi:\n- Jawab spesifik dari isi makalah.\n- Bila tidak ada di makalah, tulis singkat 'Maaf, saya tidak bisa memahami input tersebut. Bisa dijelaskan maksudnya?.'"
            . " dan lanjutkan dengan bagian 'Saran:' yang berisi rekomendasi langkah praktis.\n"
            . "Pertanyaan:\n" . $question
        ];

        $client = new \GuzzleHttp\Client([
            'base_uri' => rtrim(env('GEMINI_API_BASE','https://generativelanguage.googleapis.com'),'/').'/v1beta/',
            'timeout'  => 120,
        ]);

        try {
            $resp = $client->post('models/'.$model.':generateContent', [
                'headers' => [
                    'x-goog-api-key' => env('GEMINI_API_KEY'),
                    'Content-Type'   => 'application/json',
                ],
                'json' => [
                    'contents' => [[ 'parts' => $parts ]],
                    'generationConfig' => [
                        'temperature'     => 0.3,
                        'topP'            => 0.9,
                        'maxOutputTokens' => 1500,
                    ],
                ],
            ]);

            $data   = json_decode($resp->getBody()->getContents(), true);
            $answer = trim($data['candidates'][0]['content']['parts'][0]['text'] ?? '');

            $needSuggest =
                ($answer === '') ||
                (mb_strlen($answer) < 15) ||
                (bool) preg_match('/\b(tidak ditemukan|tidak ada|data tidak tersedia)\b/i', $answer);

            if ($needSuggest) {
                $suggest = $this->suggestNextStepsAi($question, $excerpt, $history);
                return $answer !== ''
                    ? ($answer . "\n\n" . $suggest)
                    : $suggest;
            }

            return $answer;
        } catch (\Throwable $e) {
            return $this->suggestNextStepsAi($question, $excerpt, $history);
        }
    }

    private function suggestNextStepsAi(string $question, string $paperExcerpt, array $history = []): string
    {
        $model = env('GEMINI_MODEL', 'gemini-2.5-flash');
        $client = new \GuzzleHttp\Client([
            'base_uri' => rtrim(env('GEMINI_API_BASE','https://generativelanguage.googleapis.com'),'/').'/v1beta/',
            'timeout'  => 60,
        ]);

        $sys = "Anda adalah asisten yang menyarankan langkah berikutnya agar pengguna mendapatkan jawaban."
            . " Keluarkan hanya bagian 'Saran:' berisi 3–6 poin bullet pendek, sangat spesifik dan dapat ditindaklanjuti."
            . " Gunakan Bahasa Indonesia. Jangan bertele-tele, jangan minta maaf, jangan menjelaskan alasan model.";

        $lastTurns = array_slice($history, -3);
        $historyTxt = implode("\n", array_map(function($h){
            $r = ($h['role'] ?? 'user') === 'user' ? 'User' : 'AI';
            return $r.': '.mb_substr((string)($h['text'] ?? ''), 0, 240);
        }, $lastTurns));

        $user = "Pertanyaan: {$question}\n\n"
            . "Cuplikan makalah (mungkin terpotong):\n"
            . mb_substr($paperExcerpt, 0, 6000) . "\n\n"
            . "Riwayat singkat:\n{$historyTxt}\n\n"
            . "Output yang diminta (format wajib):\n"
            . "Saran:\n- <butir 1>\n- <butir 2>\n- <butir 3>\n- <butir 4?>\n- <butir 5?>\n- <butir 6?>";

        try {
            $resp = $client->post('models/'.$model.':generateContent', [
                'headers' => [
                    'x-goog-api-key' => env('GEMINI_API_KEY'),
                    'Content-Type'   => 'application/json',
                ],
                'json' => [
                    'system_instruction' => ['parts' => [ ['text' => $sys] ]],
                    'contents' => [[ 'parts' => [ ['text' => $user] ] ]],
                    'generationConfig' => [
                        'temperature'     => 0.2,
                        'maxOutputTokens' => 500,
                        'responseMimeType'=> 'text/plain',
                    ],
                ],
            ]);

            $data = json_decode($resp->getBody()->getContents(), true);
            $text = trim($data['candidates'][0]['content']['parts'][0]['text'] ?? '');

            if ($text === '' || stripos($text, 'Saran:') === false) {
                $text = "Saran:\n- Perjelas tujuan spesifik pertanyaan.\n- Tambahkan detail konteks (periode, metrik, unit kerja).\n- Sertakan data/tautan pendukung dari makalah atau lampiran.\n- Jika ada, aktifkan pengambilan data pendukung (mis. opsi with_db) untuk memperkaya konteks.\n- Pecah pertanyaan besar menjadi 2–3 sub-pertanyaan terfokus.";
            }

            return $text;
        } catch (\Throwable $e) {
            return "Saran:\n"
                . "- Perjelas tujuan spesifik dan hasil yang diinginkan.\n"
                . "- Tambahkan konteks/parameter penting (periode, metrik, asumsi, unit kerja).\n"
                . "- Sertakan data/artefak pendukung dari makalah (kutipan, tabel, lampiran).\n"
                . "- Pecah pertanyaan kompleks jadi sub-pertanyaan yang lebih sempit.";
        }
    }

    public function answerGeneralInnovator(string $question, array $opts = []): array
    {
        $t0       = microtime(true);
        $reqId    = (string) Str::uuid();
        $session  = session('siino.session_id');
        $model    = $opts['model'] ?? env('GEMINI_MODEL', 'gemini-2.5-flash');
        $apiKey   = env('GEMINI_API_KEY');
        $baseUri  = rtrim(env('GEMINI_API_BASE', 'https://generativelanguage.googleapis.com'), '/') . '/v1beta/';
        $cleanQuestion = $this->guard->sanitizeUserInput($question);
        [$contextText, $citations] = $this->safeCompileContext($opts);
        $cleanContext = $this->guard->sanitizeContext((string) $contextText);

        Log::channel('ai_guard')->info('ai.general.start', [
            'req'         => $reqId,
            'session'     => $session,
            'model'       => $model,
            'has_api_key' => (bool) $apiKey,
            'base'        => $baseUri,
            'q_len'       => mb_strlen($cleanQuestion),
            'ctx_len'     => mb_strlen($cleanContext),
            'opts'        => array_diff_key($opts, ['__question' => true]),
        ]);

        // Jika tidak ada API key → pesan jelas + log
        if (!$apiKey) {
            Log::channel('ai_guard')->warning('ai.general.no_api_key', [
                'req' => $reqId, 'session' => $session,
            ]);
            $msg = "Layanan AI belum aktif (API key kosong).";
            if ($cleanContext !== '') {
                $msg .= "\n\nRingkasan internal (singkat):\n" . Str::limit($cleanContext, 800);
            }
            return [
                'answer'    => $msg,
                'followups' => $this->followups(),
                'citations' => $citations,
                'raw'       => [],
                'mode'      => 'no-key',
            ];
        }

        $client = new \GuzzleHttp\Client(['base_uri' => $baseUri, 'timeout' => 60]);

        $systemCtx = implode(' ', [
            "Anda asisten Portal Inovasi SIG.",
            "Gunakan data dari konteks internal jika relevan.",
            "Jika konteks tidak menjawab, jawab umum terbaik.",
            "Jawab ringkas (1–3 paragraf) + bullet seperlunya. Bahasa Indonesia."
        ]);

        try {
            // 1) Coba dengan konteks internal
            $answer1 = $this->callGeminiOnce($client, $model, $apiKey, $systemCtx, $cleanQuestion, $cleanContext);

            // Log respons pertama
            Log::channel('ai_guard')->info('ai.general.first_try', [
                'req'        => $reqId,
                'session'    => $session,
                'ans_len'    => mb_strlen($answer1),
                'too_short'  => (mb_strlen(trim($answer1)) < 20),
            ]);

            $final = $answer1;

            // 2) Jika kosong/terlalu pendek → coba tanpa konteks (prompt general)
            if (trim($final) === '' || mb_strlen($final) < 20) {
                $systemGeneral = "Anda asisten yang menjawab umum terbaik. Bahasa Indonesia, ringkas.";
                $answer2 = $this->callGeminiOnce($client, $model, $apiKey, $systemGeneral, $cleanQuestion, '');
                Log::channel('ai_guard')->info('ai.general.second_try', [
                    'req'       => $reqId,
                    'session'   => $session,
                    'ans_len'   => mb_strlen($answer2),
                    'from_empty'=> (trim($final) === ''),
                ]);
                $final = $answer2;
            }

            // 3) Jika tetap tidak layak tampil → fallback dengan pesan jelas + potongan konteks
            if (trim($final) === '' || mb_strlen($final) < 10) {
                Log::channel('ai_guard')->warning('ai.general.fallback_too_short', [
                    'req'      => $reqId,
                    'session'  => $session,
                    'ans_len'  => mb_strlen($final),
                ]);
                $fallback = "Saya belum dapat menghasilkan jawaban dari model saat ini.";
                if ($cleanContext !== '') {
                    $fallback .= "\n\nBerikut ringkasan internal (singkat):\n" . Str::limit($cleanContext, 800);
                }
                $final = $fallback;
                $mode  = 'fallback-short';
            } else {
                $mode  = 'model';
            }

            // Log: done
            Log::channel('ai_guard')->info('ai.general.done', [
                'req'      => $reqId,
                'session'  => $session,
                'mode'     => $mode,
                'duration' => round((microtime(true) - $t0) * 1000) . 'ms',
                'ans_len'  => mb_strlen($final),
            ]);

            return [
                'answer'    => $final,
                'followups' => $this->followups(),
                'citations' => $citations,
                'raw'       => [],
                'mode'      => $mode,
            ];

        } catch (RequestException $e) {
            $status = $e->getResponse()?->getStatusCode();
            $body   = $e->getResponse()?->getBody()?->getContents();
            // Potong body agar log aman
            $bodySh = $body ? Str::limit($this->guard->redactPII($body), 900) : null;

            Log::channel('ai_guard')->warning('ai.general.http_error', [
                'req'      => $reqId,
                'session'  => $session,
                'status'   => $status,
                'error'    => $e->getMessage(),
                'body'     => $bodySh,
                'duration' => round((microtime(true) - $t0) * 1000) . 'ms',
            ]);

            $fallback = "Terjadi kendala memanggil layanan AI.";
            if ($cleanContext !== '') {
                $fallback .= "\n\nRingkasan internal (singkat):\n" . Str::limit($cleanContext, 800);
            }

            return [
                'answer'    => $fallback,
                'followups' => $this->followups(),
                'citations' => $citations,
                'raw'       => ['status' => $status],
                'mode'      => 'http-error',
            ];

        } catch (Throwable $e) {
            Log::channel('ai_guard')->warning('ai.general.throwable', [
                'req'      => $reqId,
                'session'  => $session,
                'error'    => $e->getMessage(),
                'duration' => round((microtime(true) - $t0) * 1000) . 'ms',
            ]);

            $fallback = "Terjadi kendala memanggil layanan AI.";
            if ($cleanContext !== '') {
                $fallback .= "\n\nRingkasan internal (singkat):\n" . Str::limit($cleanContext, 800);
            }

            return [
                'answer'    => $fallback,
                'followups' => $this->followups(),
                'citations' => $citations,
                'raw'       => [],
                'mode'      => 'exception',
            ];
        }
    }

    private function callGeminiOnce(\GuzzleHttp\Client $client, string $model, string $apiKey, string $system, string $question, string $context): string
    {
        $question = (new AiGuard())->sanitizeUserInput($question);
        $context  = (new AiGuard())->sanitizeContext($context);

        $resp = $client->post('models/'.$model.':generateContent', [
            'headers' => ['x-goog-api-key'=>$apiKey,'Content-Type'=>'application/json'],
            'json' => [
                'system_instruction' => ['parts' => [['text' => (new AiGuard())->safetySystemText()."\n".$system]]],
                'contents' => [[
                    'role'  => 'user',
                    'parts' => [[ 'text' => json_encode([
                        'question' => $question,
                        'context'  => $context,
                        'note'     => 'Jawab ringkas dan langsung ke inti. Berikan langkah/ide praktis bila cocok.'
                    ], JSON_UNESCAPED_UNICODE)]],
                ]],
                'generationConfig' => [
                    'temperature'=>0.3,'topP'=>0.9,
                    'maxOutputTokens'=>(int) env('SIINO_AI_MAX_TOKENS',1500),
                    'responseMimeType'=>'text/plain',
                ],
                'safetySettings' => (new AiGuard())->safetySettings(),
            ],
        ]);

        $data   = json_decode($resp->getBody()->getContents(), true);
        $answer = trim($data['candidates'][0]['content']['parts'][0]['text'] ?? '');
        return (new AiGuard())->sanitizeModelOutput($answer);
    }


    private function safeCompileContext(array $opts): array
    {
        try {
            return $this->compileGeneralContext($opts);
        } catch (\Throwable $e) {
            return ['', []];
        }
    }

    private function compileGeneralContext(array $opts): array
    {
        $eventId   = $opts['event_id']   ?? null;
        $companyId = $opts['company_id'] ?? null;

        $chunks = [];
        $cit    = [];

        // 1) Timeline
        if ($eventId && \Schema::hasTable('timeline')) {
            $q = \DB::table('timeline')->where('event_id',$eventId);
            if (\Schema::hasColumn('timeline','order_no'))      $q->orderBy('order_no');
            elseif (\Schema::hasColumn('timeline','order'))     $q->orderBy('order');
            else                                                $q->orderBy('id');

            $rows = $q->get(['id','title','start_at','end_at','notes']);
            if ($rows->count()) {
                $t = "Timeline (event_id={$eventId}):\n";
                foreach ($rows as $r) {
                    $range = trim(($r->start_at ?: '').($r->end_at ? " s.d. {$r->end_at}" : ''));
                    $t .= "- {$r->title}".($range ? " ({$range})" : '').($r->notes ? " — {$r->notes}" : '')."\n";
                    $cit[] = ['type'=>'timeline','id'=>$r->id];
                }
                $chunks[] = trim($t);
            }
        }

        // 2) Rubrik
        if (\Schema::hasTable('template_assessment_points')) {
            $tb = 'template_assessment_points';
            $q  = \DB::table($tb)->when($eventId, fn($qq)=>$qq->where('event_id',$eventId));

            if (\Schema::hasColumn($tb,'weight')) $q->orderByDesc('weight'); else $q->orderByDesc('id');

            $maybe = ['id','point','weight','description','desc','criteria','detail','notes'];
            $selects = [];
            foreach ($maybe as $c) {
                if (\Schema::hasColumn($tb, $c)) $selects[] = $c;
            }
            if (!in_array('id', $selects))     $selects[] = 'id';
            if (!in_array('point', $selects))  $selects[] = 'point';

            $rub = $q->limit(10)->get($selects);

            if ($rub->count()) {
                $t = "Kriteria penilaian teratas:\n";
                foreach ($rub as $r) {
                    $desc = $r->description
                        ?? ($r->desc    ?? null)
                        ?? ($r->criteria?? null)
                        ?? ($r->detail  ?? null)
                        ?? ($r->notes   ?? null);

                    $w = property_exists($r,'weight') && $r->weight !== null ? " (bobot {$r->weight})" : '';
                    $t .= "- {$r->point}{$w}".($desc ? ": ".\Illuminate\Support\Str::limit($desc,120) : "")."\n";
                }
                $cit[]     = ['type'=>'rubric','event_id'=>$eventId];
                $chunks[]  = trim($t);
            }
        }

        // 3) Tema
        if (\Schema::hasTable('themes')) {
            $th = \DB::table('themes')
                ->when($eventId, fn($qq)=>$qq->where('event_id',$eventId))
                ->when(\Schema::hasColumn('themes','order_no'), fn($qq)=>$qq->orderBy('order_no'), fn($qq)=>$qq->orderBy('id'))
                ->limit(8)->pluck('theme_name');
            if ($th->count()) { $chunks[] = "Tema: ".$th->implode(', ')."."; $cit[] = ['type'=>'themes','event_id'=>$eventId]; }
        }

        // 4) Summary executives
        if (\Schema::hasTable('summary_executives')) {
            $tb = 'summary_executives';
            $q = \DB::table($tb.' as se');

            if (\Schema::hasTable('pvt_event_teams')) {
                $q->leftJoin('pvt_event_teams as pet', 'pet.id', '=', 'se.pvt_event_teams_id');
                if ($eventId && \Schema::hasColumn('pvt_event_teams','event_id')) {
                    $q->where('pet.event_id', $eventId);
                }
            }
            if (\Schema::hasTable('teams')) {
                if (\Schema::hasColumn('pvt_event_teams','team_id')) {
                    $q->leftJoin('teams as t', 't.id', '=', 'pet.team_id');
                }
                if ($companyId && \Schema::hasColumn('teams','company_id')) {
                    $q->where('t.company_id', $companyId);
                }
            }

            $selects = ['se.id', 'se.problem_background', 'se.innovation_idea', 'se.benefit', 'se.file_ppt'];
            if (\Schema::hasColumn('pvt_event_teams','event_id')) $selects[] = 'pet.event_id as event_id';
            if (\Schema::hasColumn('teams','name'))               $selects[] = 't.name as team_name';

            $rows = $q->orderByDesc('se.id')->limit(3)->get($selects);

            if ($rows->count()) {
                $t = "Ringkasan eksekutif terbaru:";
                foreach ($rows as $r) {
                    $meta = [];
                    if (property_exists($r,'team_name') && $r->team_name) $meta[] = "Tim: {$r->team_name}";
                    if (property_exists($r,'event_id') && $r->event_id)   $meta[] = "Event: {$r->event_id}";
                    $metaStr = $meta ? ' — '.implode(' • ', $meta) : '';

                    $pb  = \Illuminate\Support\Str::limit((string)($r->problem_background ?? ''), 120);
                    $ide = \Illuminate\Support\Str::limit((string)($r->innovation_idea   ?? ''), 120);
                    $ben = \Illuminate\Support\Str::limit((string)($r->benefit           ?? ''), 120);

                    $t .= "\n- ID {$r->id}{$metaStr}"
                       .  ($pb  ? "\n  • Problem: {$pb}"  : '')
                       .  ($ide ? "\n  • Ide: {$ide}"      : '')
                       .  ($ben ? "\n  • Benefit: {$ben}"  : '')
                       .  ((string)($r->file_ppt ?? '') !== '' ? "\n  • PPT: {$r->file_ppt}" : '');
                }
                $chunks[] = $t;
                $cit[]    = ['type'=>'summary_executives_list','count'=>$rows->count()];
            }
        }

        // 5) Paper stat
        if (\Schema::hasTable('papers')) {
            $p = \DB::table('papers')
                ->when($eventId, fn($qq)=>$qq->where('event_id',$eventId))
                ->when($companyId, fn($qq)=>$qq->where('company_id',$companyId))
                ->selectRaw('COUNT(*) total, SUM(CASE WHEN status="accepted" THEN 1 ELSE 0 END) accepted')
                ->first();
            if ($p && $p->total !== null) {
                $chunks[] = "Progres paper: {$p->total} dokumen, diterima {$p->accepted}.";
                $cit[] = ['type'=>'papers_stat','event_id'=>$eventId,'company_id'=>$companyId];
            }
        }

        // 6) Team stat
        if (\Schema::hasTable('pvt_event_teams') && \Schema::hasTable('teams')) {
            $t = \DB::table('pvt_event_teams as pet')
                ->join('teams as t','t.id','=','pet.team_id')
                ->when($eventId, fn($qq)=>$qq->where('pet.event_id',$eventId))
                ->when($companyId, fn($qq)=>$qq->where('t.company_id',$companyId))
                ->selectRaw('COUNT(*) total, SUM(CASE WHEN pet.status="finalist" THEN 1 ELSE 0 END) finalist')
                ->first();
            if ($t && $t->total !== null) {
                $chunks[] = "Tim terdaftar: {$t->total} tim (finalis: {$t->finalist}).";
                $cit[] = ['type'=>'teams_stat','event_id'=>$eventId,'company_id'=>$companyId];
            }
        }

        // 7) Pencarian topik dari pertanyaan
        $keywords = $this->extractKeywords($opts['__question'] ?? null);
        if (!empty($keywords)) {
            [$hitsTxt, $hitsCite] = $this->searchInternalInnovations($keywords, $eventId, $companyId, false);
            if ($hitsTxt !== '') { $chunks[] = $hitsTxt; $cit = array_merge($cit, $hitsCite); }
        }

        $context = trim(implode("\n\n", array_filter($chunks)));
        return [$context, $cit];
    }

    private function extractKeywords(?string $question): array
    {
        if (!$question) return [];
        $q = mb_strtolower($question);

        $q = preg_replace('/[^a-z0-9\s\-\/]/u', ' ', $q);
        $q = preg_replace('/\s+/', ' ', $q);

        $stop = ['apa','yang','di','ke','dari','tentang','sudah','udah','ada','belum','bagaimana','gimana','kapan','dimana','apakah','dan','atau','itu','ini','terkait'];
        $tokens = array_values(array_filter(explode(' ', $q), fn($t)=>$t !== '' && !in_array($t, $stop)));

        $phrases = [];
        $text = ' '.implode(' ', $tokens).' ';
        $knownPhrases = ['data center','data-center','dc','gresik','tuban','rembang','sorowako','sidoarjo','cloud','server','network','jaringan','cooling'];
        foreach ($knownPhrases as $p) {
            if (str_contains($text, ' '.strtolower($p).' ')) $phrases[] = strtolower($p);
        }

        return array_values(array_unique(array_merge($phrases, $tokens)));
    }

    private function searchInternalInnovations(array $keywords, ?int $eventId, ?int $companyId, bool $withCountLog = false): array
    {
        if (!\Schema::hasTable('papers')) return $withCountLog ? ['', [], 0] : ['', []];

        $q = \DB::table('papers as p');

        $hasTeamsJoin = \Schema::hasTable('teams') && \Schema::hasColumn('papers','team_id');
        if ($hasTeamsJoin) {
            $q->leftJoin('teams as t','t.id','=','p.team_id');
        }

        if ($eventId && \Schema::hasColumn('papers','event_id')) {
            $q->where('p.event_id', $eventId);
        }
        if ($companyId) {
            if (\Schema::hasColumn('papers','company_id')) {
                $q->where('p.company_id', $companyId);
            } elseif ($hasTeamsJoin && \Schema::hasColumn('teams','company_id')) {
                $q->where('t.company_id', $companyId);
            }
        }

        $cols = ['p.innovation_title','p.abstract','p.inovasi_lokasi','t.name'];
        $availableCols = array_values(array_filter($cols, function($c){
            [$alias,$col] = explode('.', $c, 2);
            $table = $alias === 'p' ? 'papers' : ($alias === 't' ? 'teams' : '');
            return $table && \Schema::hasColumn($table, $col);
        }));
        if (empty($availableCols)) return $withCountLog ? ['', [], 0] : ['', []];

        $q->where(function($qq) use ($availableCols, $keywords){
            foreach ($keywords as $kw) {
                $kw = trim($kw);
                if ($kw === '') continue;
                $qq->orWhere(function($qq2) use ($availableCols, $kw){
                    foreach ($availableCols as $c) {
                        $qq2->orWhereRaw("$c LIKE ?", ['%'.$kw.'%']);
                    }
                });
            }
        });

        $selects = ['p.id'];
        foreach (['innovation_title','abstract','inovasi_lokasi','status','event_id','team_id'] as $c) {
            if (\Schema::hasColumn('papers',$c)) $selects[] = "p.$c";
        }
        if ($hasTeamsJoin && \Schema::hasColumn('teams','name'))       $selects[] = 't.name as team_name';
        if ($hasTeamsJoin && \Schema::hasColumn('teams','company_id')) $selects[] = 't.company_id as team_company_id';

        $rows = $q->orderByDesc('p.id')->limit(5)->get($selects);

        $lines = [];
        $cites = [];
        foreach ($rows as $r) {
            $ev   = property_exists($r,'event_id') ? $r->event_id : null;
            $loc  = $r->inovasi_lokasi ?? null;
            $team = $r->team_name ?? null;

            $meta = [];
            if ($team) $meta[] = "Tim: {$team}";
            if ($ev)   $meta[] = "Event: {$ev}";
            if ($loc)  $meta[] = "Lokasi: {$loc}";
            $metaStr = $meta ? ' — '.implode(' • ', $meta) : '';

            $title = $r->innovation_title ?? '(tanpa judul)';
            $lines[] = "- {$title}{$metaStr}";
            $cites[] = ['type'=>'paper','id'=>$r->id];
        }

        $txt = $rows->isEmpty() ? '' : "Temuan terkait kata kunci:\n".implode("\n", $lines);

        return $withCountLog ? [$txt, $cites, $rows->count()] : [$txt, $cites];
    }

    private function jawabLokal(string $q, string $ctx): string
    {
        if (Str::contains(Str::lower($q), ['deadline','batas','kapan','tutup','submit'])) {
            $firstLine = strtok($ctx, "\n");
            return $firstLine."\n(Lihat rincian timeline di atas.)";
        }
        return "Ringkasan internal:\n".$ctx;
    }

    private function followups(): array
    {
        return [
            'Ini terkait event/tahun berapa?',
            'Perlu tautan template proposal / panduan unggah?',
            'Butuh info batas waktu pendaftaran atau final presentation?'
        ];
    }
}
