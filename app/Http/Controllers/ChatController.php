<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Services\AiPaperAnalyzerGemini as Analyzer;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        return view('ai.chat.index'); 
    }

    public function wizard(Request $request) { return $this->chatPage('wizard'); }
    public function finder(Request $request) { return $this->chatPage('finder'); }
    public function direct(Request $request) { return $this->chatPage('direct'); }

    private function chatPage(string $channel)
    {
        $sessionId = session()->get('siino.session_id') ?? (function () {
            $sid = (string) Str::uuid();
            session()->put('siino.session_id', $sid);
            return $sid;
        })();

        $key = "siino.messages.$channel";
        if (!session()->has($key)) session()->put($key, []);

        return view('ai.chat.page', [
            'sessionId' => $sessionId,
            'channel'   => $channel,
        ]);
    }

    public function fetch(Request $request)
    {
        $channel  = $request->query('c', 'direct');
        $messages = session("siino.messages.$channel", []);
        return response()->json(['messages' => $messages]);
    }

    public function send(Request $request, Analyzer $ai)
    {
        $data = $request->validate([
            'message' => 'required|string|max:4000',
            'c'       => 'nullable|string|in:wizard,finder,direct',
        ]);

        $channel  = $data['c'] ?? 'direct';
        $key      = "siino.messages.$channel";
        $messages = session($key, []);

        $userMsg = [
            'id' => 'u_'.Str::random(10), 'role'=>'user',
            'content'=>$data['message'], 'status'=>'ok',
            'created_at'=>Carbon::now()->toISOString(),
        ];
        $messages[] = $userMsg;

        try {
            $incoming = match ($channel) {
                'wizard' => "[WIZARD] {$data['message']}",
                'finder' => "[FINDER] {$data['message']}",
                default  => $data['message'],
            };

            $res = $ai->answerGeneralInnovator($incoming, ['limit_faq'=>6]);
            $answerText = (string)($res['answer'] ?? 'Maaf, belum ada jawaban yang bisa ditampilkan.');

            $assistant = [
                'id'=>'a_'.Str::random(10),'role'=>'assistant',
                'content'=>$answerText,'status'=>'ok',
                'created_at'=>Carbon::now()->toISOString(),
            ];
            $messages[] = $assistant;

            session()->put($key, $messages);

            return response()->json([
                'user'      => ['id'=>$userMsg['id'],'content'=>$userMsg['content']],
                'assistant' => ['id'=>$assistant['id'],'content'=>$assistant['content']],
                'meta'      => ['followups'=>$res['followups'] ?? [], 'citations'=>$res['citations'] ?? []],
            ], 201);
        } catch (\Throwable $e) {
            $assistant = [
                'id'=>'a_'.Str::random(10),'role'=>'assistant',
                'content'=>'Maaf, terjadi gangguan. Coba ulang beberapa saat lagi.',
                'status'=>'error','created_at'=>Carbon::now()->toISOString(),
            ];
            $messages[] = $assistant;
            session()->put($key, $messages);

            return response()->json([
                'user'      => ['id'=>$userMsg['id'],'content'=>$userMsg['content']],
                'assistant' => ['id'=>$assistant['id'],'content'=>$assistant['content']],
            ], 500);
        }
    }

    public function upload(Request $request)
    {
        $request->validate(['files.*'=>'required|file|max:5120']);
        $out = [];
        foreach ($request->file('files', []) as $file) {
            $path = $file->store('chat', 'public');
            $out[] = [
                'url'=>asset('storage/'.$path),
                'name'=>$file->getClientOriginalName(),
                'mime'=>$file->getClientMimeType(),
                'size'=>$file->getSize(),
            ];
        }
        return response()->json(['attachments'=>$out]);
    }
}
