<?php

namespace App\Services;

use Illuminate\Support\Str;

class AiGuard
{
    public const HOST_ALLOWLIST  = ['generativelanguage.googleapis.com'];
    public const MODEL_ALLOWLIST = [
        'gemini-2.0-flash', 'gemini-2.0-pro', 'gemini-2.5-flash', 'gemini-2.5-pro'
    ];

    public const MAX_QUESTION_CHARS = 1500;
    public const MAX_CONTEXT_CHARS  = 18000;

    private const PII_PATTERNS = [
        '/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i',
        '/\b(\+?62|0)\d{8,15}\b/',
        '/\b\d{16}\b/',
        '/(api[_-]?key|secret|token|password)\s*[:=]\s*[\'"][A-Za-z0-9_\-\.]{8,}[\'"]/i',
        '/(sk-[A-Za-z0-9]{20,})/i',
    ];


    private function cfg(string $key, $default = null) {
        try { return config($key, $default); } catch (\Throwable $e) { return $default; }
    }

    private function envList(string $envKey): array
    {
        $raw = env($envKey, '');
        if ($raw === '' || $raw === null) return [];
        return array_values(array_filter(array_map('trim', explode(',', $raw)), fn($v)=>$v!==''));
    }

    private function envRegexList(string $envKey): array
    {
        $raw = env($envKey, '');
        if ($raw === '' || $raw === null) return [];
        $json = json_decode($raw, true);
        if (is_array($json)) {
            return array_values(array_filter(array_map('strval', $json)));
        }
        return array_values(array_filter(array_map('trim', explode(';;;', $raw)), fn($v)=>$v!==''));
    }

    public function sanitizeUserInput(string $text): string
    {
        $t = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', ' ', $text);
        $t = trim(Str::limit($t, self::MAX_QUESTION_CHARS, ' …'));
        return $this->redactPII($t);
    }

    public function sanitizeContext(string $ctx): string
    {
        $ctx = trim(Str::limit($ctx, self::MAX_CONTEXT_CHARS, ' …'));
        return $this->redactPII($ctx);
    }

    public function redactPII(string $text): string
    {
        foreach (self::PII_PATTERNS as $re) {
            $text = preg_replace($re, '[REDACTED]', $text);
        }
        return $text;
    }

    public function hardRedactInfra(string $text): string
    {
        $text = preg_replace('/\b[a-z][a-z0-9_]{2,}\.[a-z][a-z0-9_]{2,}\b/i', '[REDACTED.DB]', $text);
        $text = preg_replace('/`[a-z][a-z0-9_]{2,}`/i', '[REDACTED.ID]', $text);
        $text = preg_replace('/"[^"]+"\."[^"]+"/u', '[REDACTED.DB]', $text);
        $text = preg_replace('#/(var|etc|srv|opt|home)/[^\s]+#i', '[REDACTED.PATH]', $text);
        $text = preg_replace('/\b(127\.0\.0\.1|10\.\d+\.\d+\.\d+|192\.168\.\d+\.\d+)\b/', '[REDACTED.IP]', $text);
        $text = preg_replace('/https?:\/\/(localhost|127\.0\.0\.1|10\.\d+\.\d+\.\d+|192\.168\.\d+\.\d+)/i', '[BLOCKED-URL]', $text);
        return $text;
    }

    public function sanitizeModelOutput(string $out): string
    {
        $out = $this->hardRedactInfra($out);
        $out = $this->normalizePlainText($out);
        return trim($out);
    }

    public function validateBaseUri(string $base): string
    {
        $u = parse_url($base);
        $host = $u['host'] ?? '';
        if (!in_array($host, self::HOST_ALLOWLIST, true)) {
            throw new \RuntimeException('AI base URI tidak diizinkan');
        }
        return rtrim($base, '/').'/';
    }

    public function validateModel(?string $model): string
    {
        $m = $model ?: env('GEMINI_MODEL', 'gemini-2.5-flash');
        if (!in_array($m, self::MODEL_ALLOWLIST, true)) {
            throw new \RuntimeException('Model tidak diizinkan');
        }
        return $m;
    }

    public function safetySettings(): array
    {
        $cat = [
            'HARM_CATEGORY_DANGEROUS_CONTENT',
            'HARM_CATEGORY_HATE_SPEECH',
            'HARM_CATEGORY_HARASSMENT',
            'HARM_CATEGORY_SEXUALLY_EXPLICIT',
        ];
        $threshold = env('SIINO_SAFETY_THRESHOLD', 'BLOCK_MEDIUM_AND_ABOVE');

        return array_map(fn($c) => [
            'category'  => $c,
            'threshold' => $threshold,
        ], $cat);
    }

    public function safetySystemText(): string
    {
        return implode("\n", [
            "Kamu asisten portal inovasi internal.",
            "JANGAN ungkap struktur basis data, nama tabel/kolom, query SQL, path/server internal, alamat IP privat, kredensial, atau detail arsitektur.",
            "Jika diminta informasi tersebut, tolak sopan dan tawarkan alternatif yang aman (rujuk dokumentasi internal/administrator).",
            "Jangan gunakan format Markdown (tidak ada **, __, *, #, [],()). Tulis teks polos dengan bullet '- ' bila perlu.",
            "Patuhi kebijakan: jangan minta/menyimpan kredensial, PII sensitif, atau info rahasia.",
            "Jawab berdasarkan konteks yang diberikan. Jika tidak ada, jawab singkat 'Maaf, saya tidak bisa memahami input tersebut. Bisa dijelaskan maksudnya?'",
            "Gunakan Bahasa Indonesia yang ringkas dan netral.",
        ]);
    }

    public function isIntrospection(string $q): bool
    {
        $enabled = filter_var(env('SIINO_INTROSPECTION_ENABLED', true), FILTER_VALIDATE_BOOLEAN);
        if (!$enabled) return false;

        $text   = mb_strtolower($q);
        $score  = 0;
        $thres  = (int) env('SIINO_INTROSPECTION_THRESHOLD', 1);

        $regexes = $this->envRegexList('SIINO_INTROSPECTION_REGEXES');
        $regexes = array_merge($regexes, (array) $this->cfg('ai_guard.regexes', []));
        foreach ($regexes as $re) {
            try {
                if ($re !== '' && @preg_match($re, '') !== false && preg_match($re, $text)) {
                    $score++;
                }
            } catch (\Throwable $e) { /* abaikan regex buruk */ }
        }

        $kwList = array_merge(
            $this->envList('SIINO_INTROSPECTION_KEYWORDS'),
            (array) $this->cfg('ai_guard.extra_keywords', [])
        );
        foreach ($kwList as $kw) {
            $kw = trim(mb_strtolower($kw));
            if ($kw !== '' && Str::contains($text, $kw)) $score++;
        }

        if ($score === 0 && empty($regexes) && empty($kwList)) return false;

        return $score >= $thres;
    }

    private function normalizePlainText(string $t): string
    {
        $t = str_replace(["\r\n", "\r"], "\n", $t);
        $t = preg_replace('/^\s{0,3}#{1,6}\s*/m', '', $t);
        $t = preg_replace('/\*\*(.+?)\*\*/s', '$1', $t);
        $t = preg_replace('/__(.+?)__/s', '$1', $t);
        $t = preg_replace('/(?<!^)\*(?!\s)([^*\n]+)\*/m', '$1', $t);
        $t = preg_replace('/_(?!\s)([^_\n]+)_/m', '$1', $t);
        $t = preg_replace('/`([^`]+)`/', '$1', $t);
        $t = preg_replace('/\[(.*?)\]\((.*?)\)/', '$1', $t);
        $t = preg_replace('/^\s*\*\s+/m', '- ', $t);
        $t = preg_replace('/\s+([,.!?;:])/', '$1', $t);
        $t = preg_replace("/\n{3,}/", "\n\n", $t);
        $t = preg_replace('/[ \t]+$/m', '', $t);

        return $t;
    }

    public function refusalForIntrospection(): string
    {
        return "Demi keamanan, saya tidak dapat mengungkap struktur basis data, nama tabel/kolom, query SQL, atau detail arsitektur sistem. " .
               "Silakan gunakan dokumentasi internal berizin atau hubungi admin untuk akses.";
    }
}
