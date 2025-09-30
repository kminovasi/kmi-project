@extends('layouts.app')
@section('title','Si-Ino Assistant')

@push('css')
<style>
  :root{ --header-h:64px; --composer-h:72px; }

  /* Header bar */
  .siino-header{
    height:var(--header-h);
    background:#dc3545;
    color:#fff;
  }
  .siino-logo{
    width:34px; height:34px;
    display:inline-flex; align-items:center; justify-content:center;
    border-radius:50%;
    background:#fff; color:#dc3545;
    box-shadow:0 1px 0 rgba(0,0,0,.04);
  }

  /* Shell & Chat */
  .shell{ border:2px solid #dc3545; border-radius:.75rem; overflow:hidden; background:#fff; }
  .chat-area{ background:#f8f9fa; }
  .bubble{ max-width:72%; background:#fff; border:1px solid #e9ecef; border-radius:.75rem; }
  .bubble.me{ background:#e7f1ff; border-color:#d6e7ff; }
  .msg-meta{ font-size:.8rem; color:#6c757d; }
  .skel{ height:56px; width:60%; background:#e9ecef; border-radius:.75rem; }
  .skel.r{ margin-left:auto; width:58%; }

  /* Hero CTA */
  .hero-cta{
    background:linear-gradient(135deg,#ffe8e8 0%, #fff 60%);
    border:1px solid #ffd6d6;
    border-radius:16px;
    padding:24px;
  }
  .hero-cta h2{ font-weight:800; letter-spacing:.2px; margin:0 0 6px; }
  .hero-cta p{ color:#495057; margin:0 0 14px; }
  .cta-row{ display:flex; flex-wrap:wrap; gap:12px; align-items:center; }
  .btn-cta{ padding:.7rem 1.1rem; border-radius:12px; font-weight:600; }
  .btn-danger-cta{
    background:#dc3545; border:1px solid #dc3545; color:#fff;
    box-shadow:0 1px 0 rgba(0,0,0,.04), 0 4px 14px rgba(220,53,69,.18);
  }
  .btn-danger-cta:hover{ background:#c92a39; border-color:#c92a39; }
  .btn-outline-danger-cta{ background:#fff; color:#dc3545; border:1.5px solid #ff9099; }
  .btn-outline-danger-cta:hover{ background:#fff5f6; color:#c92a39; border-color:#ff6b78; }
  .btn-outline-purple-cta{ background:#fff; color:#7b2cbf; border:1.5px solid #e0c3ff; }
  .btn-outline-purple-cta:hover{ background:#faf5ff; color:#6a21ab; border-color:#caa0ff; }
  .hero-links a{ text-decoration:none; color:#dc3545; margin-right:18px; }
  .hero-links a:hover{ text-decoration:underline; }

  /* typing indicator */
  .bubble{ display:inline-block; }
  .typing { display:inline-flex; gap:6px; align-items:center; min-height:12px; }
  .typing .dot{
    width:6px; height:6px; border-radius:50%;
    background:#adb5bd; opacity:.25;
    animation: typingBlink 1s infinite ease-in-out;
  }
  .typing .dot:nth-child(2){ animation-delay:.15s; }
  .typing .dot:nth-child(3){ animation-delay:.30s; }
  @keyframes typingBlink{
    0%,100% { opacity:.25; transform:translateY(0); }
    50%     { opacity:1;   transform:translateY(-2px); }
  }

  @media (max-width:576px){
    .hero-cta{ padding:18px; }
    .btn-cta{ width:100%; justify-content:center; }
    .bubble{ max-width:85%; }
  }
  
</style>
@endpush

@section('content')
<div class="container-xxl py-3">
  <div class="mx-auto" style="max-width:900px;">
    <div class="shell shadow">

      {{-- Header merah Si-Ino Assistant --}}
      <div class="siino-header d-flex align-items-center gap-2 px-3">
        <span class="siino-logo"><i data-feather="zap"></i></span>
        <h5 class="mb-0 fw-semibold">Si-Ino Assistant</h5>
      </div>

      {{-- HERO CTA --}}
      <div class="p-3 p-lg-4">
        <div class="hero-cta">
          <h2 class="mb-2">Mulai dari Nol, Akhiri dengan Dampak.</h2>
          <p>Belum punya ide? Aku bantu kamu menemukan masalah nyata dan menjadikannya inovasi siap presentasi.</p>

          <div class="cta-row mb-2">
            <a class="btn btn-cta btn-danger-cta" href="{{ route('ai.chat.wizard') }}">Buat Ide Bersama AI</a>
            <a class="btn btn-cta btn-outline-danger-cta" href="{{ route('ai.chat.finder') }}">Temukan Masalah di Unit Saya</a>
            <a class="btn btn-cta btn-outline-purple-cta" href="{{ route('ai.chat.direct') }}">Lewati — saya sudah punya ide</a>
          </div>

          <div class="hero-links small">
            <a href="{{ route('evidence.index') }}" target="_blank" rel="noopener">Lihat contoh ide yang jadi juara →</a>
          </div>
        </div>
      </div>

      {{-- Q&A CHAT di halaman utama --}}
      <div id="chatList" class="chat-area px-3 pb-3" style="min-height:280px;">
        <div class="skel"></div>
        <div class="skel r mt-3"></div>
      </div>

      {{-- Composer --}}
      <div class="border-top bg-white" style="height:var(--composer-h);">
        <form id="composerForm" class="h-100 d-flex align-items-center gap-2 px-3" autocomplete="off" novalidate>
          @csrf
          <input id="messageInput" name="message" type="text"
                 class="form-control border-0 bg-light"
                 placeholder="Ask Anything" autocomplete="off">
          <button type="button" id="btnSend" class="btn btn-danger">Kirim</button>
        </form>
      </div>
      {{-- /Composer --}}
    </div>
  </div>
</div>

{{-- data endpoint --}}
<div id="siinoApp"
     data-session-id="{{ $sessionId ?? session('siino.session_id') }}"
     data-fetch-url="{{ route('ai.chat.fetch') }}"
     data-send-url="{{ route('ai.chat.send') }}">
</div>
@endsection

@push('js')
<script>
(() => {
  const $ = (id) => document.getElementById(id);

  const appEl   = $("siinoApp");
  const chatEl  = $("chatList");
  const formEl  = $("composerForm");
  const inputEl = $("messageInput");
  const btnSend = $("btnSend");

  const fetchUrl = appEl?.dataset.fetchUrl;
  const sendUrl  = appEl?.dataset.sendUrl;
  const csrf     = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

  const state = { typingId: null };

  const scrollBottom = () => { chatEl.scrollTop = chatEl.scrollHeight; };

  function addBubble({ role, content, created_at }) {
    const row = document.createElement('div');
    row.className = 'd-flex align-items-end gap-2 mb-3 ' + (role === 'user' ? 'justify-content-end' : 'justify-content-start');

    const wrap = document.createElement('div');
    const bubble = document.createElement('div');
    bubble.className = 'bubble ' + (role === 'user' ? 'me' : 'bot') + ' rounded-3 shadow-sm p-2';
    bubble.innerText = (content || '').trim();

    const meta = document.createElement('div');
    meta.className = 'msg-meta mt-1';
    meta.textContent = created_at ? new Date(created_at).toLocaleString() : '';

    wrap.appendChild(bubble); wrap.appendChild(meta);
    row.appendChild(wrap);
    chatEl.appendChild(row);
    scrollBottom();
  }

  // === Typing indicator ===
  function addTyping(){
    removeTyping();
    const row = document.createElement('div');
    state.typingId = 'typing-' + Date.now();
    row.id = state.typingId;
    row.className = 'd-flex align-items-end gap-2 mb-3 justify-content-start';
    row.innerHTML =
      '<div><div class="bubble bot rounded-3 shadow-sm p-2">' +
        '<div class="typing"><span class="dot"></span><span class="dot"></span><span class="dot"></span></div>' +
      '</div></div>';
    chatEl.appendChild(row);
    scrollBottom();
  }
  function removeTyping(){
    if (!state.typingId) return;
    const row = document.getElementById(state.typingId);
    row?.remove();
    state.typingId = null;
  }

  // === Load history (tanpa sapaan default) ===
  async function loadHistory() {
    try {
      const res = await fetch(fetchUrl, { headers: { 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' } });
      const data = await res.json();
      document.querySelectorAll('.skel').forEach(el => el.remove());

      const msgs = data.messages || [];
      if (msgs.length) msgs.forEach(m => addBubble(m));
      // kalau kosong: tidak menambahkan apa pun
    } catch (e) {
      console.error('[index] loadHistory error', e);
      document.querySelectorAll('.skel').forEach(el => el.remove());
    }
  }

  // === Send ===
  async function sendMessage(text) {
    addTyping(); // tampilkan animasi selama AI memproses

    let res;
    try {
      res = await fetch(sendUrl, {
        method: 'POST',
        headers: {
          'Content-Type':'application/json',
          'Accept':'application/json',
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With':'XMLHttpRequest'
        },
        body: JSON.stringify({ message: text })
      });
    } catch (err) {
      removeTyping();
      addBubble({role:'assistant', content:'Koneksi gagal.', created_at:new Date().toISOString()});
      return;
    }

    removeTyping();

    if (!res.ok) {
      const body = await res.text().catch(()=>'(no-body)');
      console.error('[index] send failed', res.status, body);
      addBubble({role:'assistant', content:`Gagal (${res.status}).`, created_at:new Date().toISOString()});
      return;
    }

    const data = await res.json();
    addBubble({
      role:'assistant',
      content: data?.assistant?.content || 'Tidak ada jawaban.',
      created_at:new Date().toISOString()
    });
  }

  // === Events ===
  formEl.addEventListener('submit', (e) => {
    e.preventDefault(); e.stopPropagation();
    const raw = (inputEl.value || '').trim();
    if (!raw) return;
    inputEl.value = '';
    addBubble({role:'user', content: raw, created_at:new Date().toISOString()});
    sendMessage(raw);
  });
  btnSend.addEventListener('click', (e) => {
    e.preventDefault();
    formEl.dispatchEvent(new Event('submit', { cancelable:true, bubbles:true }));
  });
  inputEl.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      formEl.dispatchEvent(new Event('submit', { cancelable:true, bubbles:true }));
    }
  });

  loadHistory();
})();
</script>
@endpush

