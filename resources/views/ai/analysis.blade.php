@extends('layouts.app')
@section('title', 'Analisis Makalah (AI)')

@push('css')
<style>
  body { background:#f6f8fb; }

  :root{
    --sig-red-1:#ef4444; 
    --sig-red-2:#dc2626; 
    --sig-red-3:#b91c1c; 
  }

  .section-banner{
    background: linear-gradient(90deg,var(--sig-red-1) 0%, var(--sig-red-2) 55%, var(--sig-red-3) 100%);
    border: 0;
    border-radius: 20px;
    padding: .9rem 1.1rem;
    display:flex; align-items:center; justify-content:space-between;
    color:#fff;
    box-shadow:
      0 10px 26px rgba(220,38,38,.28),      
      inset 0 1px 0 rgba(255,255,255,.22);  
  }
  .section-banner .title{
    font-weight:700; letter-spacing:.2px;
    display:flex; align-items:center; gap:.6rem;
    color:#fff;
  }
  .section-banner .sparkle{
    width:22px; height:22px; display:inline-flex; align-items:center; justify-content:center;
    border-radius:999px; background:rgba(255,255,255,.22);
    font-size:13px; line-height:1;
  }
  .section-banner .chip{
    background:#fff; color:#111827;
    font-weight:600; padding:.45rem .9rem;
    border-radius:14px;
    box-shadow:0 6px 16px rgba(0,0,0,.12);
    white-space:nowrap;
  }

  .panel-framed{
    background:#fff; border:2px solid #4c1d95;
    border-radius:12px; padding:1rem; min-height:320px;
    box-shadow:0 6px 20px rgba(17,24,39,.08);
  }

  .ai-card{ background:#fff; border:1px solid #e5e7eb; border-radius:14px;
    box-shadow:0 8px 30px rgba(17,24,39,.06); }

  /* Panel Q&A */
  .qa-sticky{ position:sticky; top:1rem; }
  .qa-box{ height:60vh; overflow:auto; background:#f1f5f9; border-radius:.75rem; }
  .qa-msg{ background:#fff; border-radius:.75rem; padding:.5rem .75rem; box-shadow:0 1px 2px rgba(0,0,0,.05); white-space:pre-line; line-height:1.6; }
  .qa-me{ background:#e7f1ff; }
  .qa-meta{ font-size:.75rem; color:#6c757d; }

  /* typing bubble */
  .typing{ background:#fff; border-radius:.75rem; padding:.5rem .75rem; display:inline-flex; align-items:center; gap:6px; box-shadow:0 1px 2px rgba(0,0,0,.05);}
  .typing .dot{ width:6px; height:6px; border-radius:50%; background:#9aa2af; opacity:.6; animation: blink 1.2s infinite;}
  .typing .dot:nth-child(2){ animation-delay:.2s;} .typing .dot:nth-child(3){ animation-delay:.4s;}
  @keyframes blink{ 0%{opacity:.2} 20%{opacity:1} 100%{opacity:.2} }
</style>
@endpush


@section('content')
<header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
  <div class="container-xl px-4">
    <div class="page-header-content">
      <div class="row align-items-center justify-content-between pt-3">
        <div class="col-auto mb-3">
          <h1 class="page-header-title">
            <div class="page-header-icon"><i data-feather="bar-chart-2"></i></div>
            Analisis Makalah (AI)
          </h1>
          <div class="page-submeta">
           {{ $meta['innovationTitle'] ?? '—' }}
            • {{ $meta['eventTitle'] ?? '—' }} 
            • Kategori {{ $meta['category'] }}
          </div>
        </div>
        <div class="col-12 col-xl-auto mb-3">
        </div>
      </div>
    </div>
  </div>
</header>

@php
  $today = \Carbon\Carbon::now()->isoFormat('D MMMM YYYY');
@endphp

@php
  $stage     = request('stage') ?? ($meta['stage'] ?? null);
  $stageNorm = is_string($stage) ? strtolower(str_replace(['-',' '], '_', $stage)) : null;

  // Semua stage yang pakai tampilan khusus (2 tabel / ringkas)
  $specialStages = ['presentation', 'caucus'];

  // true = pakai tampilan khusus
  $qaOnly = in_array($stageNorm, $specialStages, true) || request()->boolean('qaOnly');
@endphp

{{-- <div class="row g-3 align-items-start">
<div class="col-md-7 col-xl-8">
  <div class="ai-card mb-4 p-3">
    <div class="section-banner mb-3">
      <div class="title">Tabel Hasil Penilaian AI</div>
      <div class="meta">
        <i data-feather="calendar"></i>
        <span>{{ $today }}</span>
      </div>
    </div>
    <div class="panel-framed">
      {!! $html !!}
    </div>
  </div>
</div> --}}


    {{-- <div class="col-md-5 col-xl-4">
      <div class="card qa-sticky shadow-sm">
        <div class="section-banner" style="border-left-color:#8b5cf6;background:#ede9fe;">
          <div class="title" style="color:#4c1d95;">Asisten Juri</div>
        </div>

        <div class="card-body">
          <div id="qaList" class="qa-box p-3">
            <div class="text-muted small">Memuat percakapan…</div>
          </div>
        </div>

        <div class="card-footer">
          <form id="qaForm" class="d-flex gap-2" onsubmit="return false;">
            <input type="hidden" id="qaPaperId" value="{{ (int)$meta['paperId'] }}">
            <input type="text" id="qaMessage" class="form-control" placeholder="Tulis pertanyaan/komentar…" required>
            <button class="btn btn-primary" id="qaSendBtn" type="submit">Kirim</button>
          </form>
          <div class="form-text mt-1">Pesan terlihat oleh juri & admin pada paper ini.</div>
        </div>
      </div>
    </div>
  </div>
</div>
</div> --}}

<div class="row g-3 align-items-start">

  @if(!$qaOnly)
  <div class="col-md-7 col-xl-8">
    <div class="ai-card mb-4 p-3">
      <div class="section-banner mb-3">
        <div class="title">Tabel Hasil Penilaian AI</div>
        <div class="meta">
          <i data-feather="calendar"></i>
          <span>{{ $today }}</span>
        </div>
      </div>
      <div class="panel-framed">
        {!! $html !!}
      </div>
    </div>
  </div>
  @endif

  <div class="{{ $qaOnly ? 'col-12' : 'col-md-5 col-xl-4' }}">
    <div class="card qa-sticky shadow-sm">
      <div class="section-banner" style="border-left-color:#8b5cf6;background:#ede9fe;">
        <div class="title" style="color:#4c1d95;">Asisten Juri</div>
      </div>

      <div class="card-body">
        <div id="qaList" class="qa-box p-3">
          <div class="text-muted small">Memuat percakapan…</div>
        </div>
      </div>

      <div class="card-footer">
        <form id="qaForm" class="d-flex gap-2" onsubmit="return false;">
          <input type="hidden" id="qaPaperId" value="{{ (int)$meta['paperId'] }}">
          <input type="text" id="qaMessage" class="form-control" placeholder="Tulis pertanyaan/komentar…" required>
          <button class="btn btn-primary" id="qaSendBtn" type="submit">Kirim</button>
        </form>
        <div class="form-text mt-1">Pesan terlihat oleh juri & admin pada paper ini.</div>
      </div>
    </div>
  </div>

</div>

@endsection

@push('js')
<script>
if (window.feather && typeof feather.replace === 'function') feather.replace();

(function(){
  const paperId = document.getElementById('qaPaperId')?.value;
  const listEl  = document.getElementById('qaList');
  const formEl  = document.getElementById('qaForm');
  const inputEl = document.getElementById('qaMessage');
  const btnSend = document.getElementById('qaSendBtn');
  const btnRef  = document.getElementById('qaRefreshBtn');
  const CSRF    = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  const API_LIST = '{{ url('/qa/messages') }}' + '?paper_id=' + encodeURIComponent(paperId);
  const API_SEND = '{{ url('/qa/messages') }}';
  const API_ASK  = '{{ route('qa.messages.ask') }}';

  let awaitingAi = false; 

  function escapeHtml(s){ return (s||'').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }
  function nowHHMM(){
    const d = new Date(); return String(d.getHours()).padStart(2,'0')+':'+String(d.getMinutes()).padStart(2,'0');
  }

  function bubbleMine(text) {
    return `
      <div class="mb-2 text-end">
        <div class="qa-meta mb-1">Anda • ${nowHHMM()}</div>
        <div class="qa-msg qa-me">${escapeHtml(text)}</div>
      </div>`;
  }

  function fmtAI(text){
  return escapeHtml(text)
    .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
  }

  function bubbleAi(text) {
    return `
      <div class="mb-2">
      <div class="qa-meta mb-1">AI • ${nowHHMM()}</div>
      <div class="qa-msg">${fmtAI(text)}</div>
    </div>`;
  }

  function bubbleTyping(id) {
    return `
      <div class="mb-2" id="${id}">
        <div class="qa-meta mb-1">AI • sedang mengetik…</div>
        <div class="typing"><span class="dot"></span><span class="dot"></span><span class="dot"></span></div>
      </div>`;
  }

  function scrollToBottom(){ listEl.scrollTop = listEl.scrollHeight; }

  async function load() {
    try {
      const res = await fetch(API_LIST, { headers: { 'Accept':'application/json' }, credentials: 'same-origin' });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const data = await res.json();
      listEl.innerHTML = (Array.isArray(data) && data.length)
        ? data.map(m => (m.mine ? bubbleMine(m.body) : bubbleAi(m.body))).join('')
        : `<div class="text-muted small">Belum ada percakapan. Mulai dengan pertanyaan pertama.</div>`;
      scrollToBottom();
    } catch (e) {
      listEl.innerHTML = `<div class="text-danger small">Gagal memuat percakapan: ${escapeHtml(e.message)}</div>`;
    }
  }

  async function sendAndAsk(msg) {
    if (!msg) return;
    awaitingAi = true;
    btnSend.disabled = true;

    listEl.insertAdjacentHTML('beforeend', bubbleMine(msg));
    const pendingId = 'ai-typing-' + Math.random().toString(36).slice(2);
    listEl.insertAdjacentHTML('beforeend', bubbleTyping(pendingId));
    scrollToBottom();

    fetch(API_SEND, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Accept':'application/json', 'Content-Type':'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify({ paper_id: paperId, body: msg })
    }).catch(()=>{}); 

    try {
      const res = await fetch(API_ASK, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept':'application/json', 'Content-Type':'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ paper_id: paperId, question: msg })
      });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const data = await res.json();
      const ans  = (data && data.answer) ? data.answer : 'Maaf, tidak ada jawaban.';

      const holder = document.getElementById(pendingId);
      if (holder) holder.outerHTML = bubbleAi(ans);

    } catch (e) {
      const holder = document.getElementById(pendingId);
      if (holder) holder.outerHTML = `<div class="text-danger small">Gagal mengambil jawaban AI: ${escapeHtml(e.message)}</div>`;
    } finally {
      awaitingAi = false;
      btnSend.disabled = false;
      inputEl.focus();
      scrollToBottom();
    }
  }

  formEl?.addEventListener('submit', (ev) => {
    ev.preventDefault();
    const v = (inputEl.value || '').trim();
    if (!v) return;
    inputEl.value = '';
    sendAndAsk(v);
  });

  inputEl?.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      formEl.dispatchEvent(new Event('submit'));
    }
  });

  btnRef?.addEventListener('click', () => { if (!awaitingAi) load(); });

  load();
  setInterval(() => { if (!awaitingAi) load(); }, 15000);
})();
</script>
@endpush
