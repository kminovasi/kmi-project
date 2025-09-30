@extends('layouts.app')
@section('title','Si-Ino Assistant')

@push('css')
<style>
  :root{ --header-h:64px; --composer-h:72px; }
  .siino-shell{ display:grid; grid-template-rows:var(--header-h) 1fr var(--composer-h); min-height:calc(100vh - 160px); border:2px solid var(--bs-danger); border-radius:.75rem; overflow:hidden; background:#fff; }
  .siino-chat{ overflow:auto; background:#f8f9fa; }
  .bubble{ max-width:72%; background:#fff; border:1px solid #e9ecef; border-radius:.75rem; }
  .bubble.me{ background:#e7f1ff; border-color:#d6e7ff; }
  .msg-meta{ font-size:.8rem; color:#6c757d; }

  /* Stepper (khusus wizard) */
  .stepper{ display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
  .step{ display:flex; align-items:center; gap:8px; padding:6px 10px; border-radius:999px; border:1px solid #e9ecef; background:#fff; font-size:.85rem; color:#6c757d; }
  .step.active{ border-color:#dc3545; color:#dc3545; background:#fff5f6; }
  .step.done{ background:#e9f7ef; border-color:#b6e2c6; color:#2a7a3b; }

  /* === Typing indicator (AI berpikir) === */
  .bubble{ display:inline-block; }
  .typing { display:inline-flex; gap:6px; align-items:center; min-height:12px; }
  .typing .dot {
    width:6px; height:6px; border-radius:50%;
    background:#adb5bd; opacity:.25;
    animation: typingBlink 1s infinite ease-in-out;
  }
  .typing .dot:nth-child(2){ animation-delay:.15s; }
  .typing .dot:nth-child(3){ animation-delay:.30s; }
  @keyframes typingBlink {
    0%,100% { opacity:.25; transform:translateY(0); }
    50%     { opacity:1;   transform:translateY(-2px); }
  }
</style>
@endpush

@section('content')
<div class="container-xxl px-3 px-lg-4">
  <div class="mx-auto" style="max-width:900px;">
    <div class="siino-shell shadow">
      <div class="bg-danger text-white d-flex align-items-center justify-content-between px-3" style="height:var(--header-h);">
        <div class="d-flex align-items-center gap-2">
          <span class="d-inline-flex align-items-center justify-content-center bg-white text-danger rounded-circle" style="width:28px;height:28px;">
            <i data-feather="zap"></i>
          </span>
          <h5 class="mb-0 fw-semibold">
            @if($channel==='wizard') Buat Ide Bersama AI
            @elseif($channel==='finder') Temukan Masalah di Unit Saya
            @else Lewati — Saya Sudah Punya Ide
            @endif
          </h5>
        </div>
        {{-- back ke HOME (index) --}}
        <a href="{{ route('ai.chat.index') }}" class="btn btn-light btn-sm" style="border-radius:10px; color:#dc3545; border-color:#ffd6d6;">← Kembali</a>
      </div>

      <div id="chatList" class="siino-chat p-3">

        {{-- STEPper hanya tampil untuk wizard --}}
        @if($channel==='wizard')
        <div id="stepperWrap" class="mb-3">
          <div id="stepper" class="stepper"></div>
        </div>
        @endif

        <div class="skel" style="height:56px;width:60%;background:#e9ecef;border-radius:.75rem;"></div>
        <div class="skel mt-3" style="height:56px;width:58%;background:#e9ecef;border-radius:.75rem;margin-left:auto;"></div>
      </div>

      <div class="border-top bg-white" style="height:var(--composer-h);">
        <form id="composerForm" class="h-100 d-flex align-items-center gap-2 px-3" autocomplete="off" novalidate>
          @csrf
          <input id="messageInput" name="message" type="text" class="form-control border-0 bg-light" placeholder="Tulis pesan..." autocomplete="off">
          <button type="button" id="btnSend" class="btn btn-danger">Kirim</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div id="siinoApp"
     data-session-id="{{ $sessionId }}"
     data-channel="{{ $channel }}"
     data-fetch-url="{{ route('ai.chat.fetch', [], false) . '?c=' . $channel }}"
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

  const fetchUrl = appEl.dataset.fetchUrl;
  const sendUrl  = appEl.dataset.sendUrl;
  const channel  = appEl.dataset.channel;
  const csrf     = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
  const stripTag = (s) => (s || '').replace(/^\s*\[(?:WIZARD|FINDER):[^\]]+\]\s*/i, '').trim();
  const isNextCmd = (t) => /^[\s\/]*(lanjut|next|berikutnya|continue)\s*$/i.test((t||'').trim());

  const stepperWrap = $("stepperWrap");
  const stepperEl   = $("stepper");

  // ===== State untuk wizard & finder =====
  const state = {
    step: 0,
    steps: [
      { key:'tema',   title:'Menentukan tema & judul',      prompt:'Sebut unit/divisi dan tujuan utamamu (hemat biaya, keselamatan, kualitas, waktu). Aku beri beberapa opsi tema & judul—pilih salah satu.' },
      { key:'akar',   title:'Menganalisis penyebab',        prompt:'Ceritakan gejala & konteks masalah (kapan/di mana/siapa terdampak). Aku bantu susun problem statement + 5 Whys singkat.' },
      { key:'solusi', title:'Menentukan solusi',            prompt:'Prefer solusi teknologi/proses/pelatihan? Ada batas biaya/waktu? Aku beri 2–3 opsi solusi + quick-win.' },
      { key:'rencana',title:'Merencanakan perbaikan',       prompt:'Sebut anggota tim kecil & target waktu. Aku susun rencana aksi 4–6 langkah (RACI + due date).' },
      { key:'draf',   title:'Menyusun & melaksanakan',      prompt:'Klik “Generate Draf” setelah memberi poin kunci. Aku gabungkan jadi draf makalah yang bisa kamu edit.' },
      { key:'evaluasi',title:'Mengevaluasi solusi',         prompt:'Masukkan angka before/after (contoh: downtime/bulan, biaya/ton). Aku buat ringkasan dampak & pelajaran.' },
      { key:'standar', title:'Membuat standar baru',        prompt:'Siapa owner SOP dan siklus auditnya? Aku turunkan SOP singkat dan rencana sustain.' },
    ],
    finder: { unit:null },
    typingId: null
  };

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

  /* === Typing bubble === */
  function addTyping(){
    removeTyping();
    const row = document.createElement('div');
    state.typingId = 'typing-' + Date.now();
    row.id = state.typingId;
    row.className = 'd-flex align-items-end gap-2 mb-3 justify-content-start';
    row.innerHTML =
      '<div><div class="bubble bot rounded-3 shadow-sm p-2">'+
        '<div class="typing"><span class="dot"></span><span class="dot"></span><span class="dot"></span></div>'+
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

  // ===== Stepper rendering (only wizard) =====
  function renderStepper(){
    if (channel !== 'wizard') return;
    stepperEl.innerHTML = '';
    state.steps.forEach((s, idx) => {
      const el = document.createElement('div');
      el.className = 'step' + (idx < state.step ? ' done' : (idx === state.step ? ' active' : ''));
      el.innerHTML =
        `<span class="badge rounded-pill ${
          idx < state.step ? 'text-bg-success' : (idx === state.step ? 'text-bg-danger' : 'text-bg-secondary')
        }">${idx+1}</span>${s.title}`;
      stepperEl.appendChild(el);
    });
    stepperWrap.style.display = 'block';
  }

  function currentWizardKey(){ return state.steps[state.step]?.key || 'tema'; }
  function nextWizardStep(){
    if (state.step < state.steps.length - 1){
      state.step++;
      renderStepper();
      addBubble({
        role:'assistant',
        content:`${state.steps[state.step].title}\n\n${state.steps[state.step].prompt}`,
        created_at:new Date().toISOString()
      });
    } else {
      addBubble({role:'assistant', content:'Keren! Semua tahap selesai. Kamu bisa Generate Draf dari data yang sudah terkumpul.', created_at:new Date().toISOString()});
    }
  }

  async function loadHistory() {
    try {
      const res = await fetch(fetchUrl, { headers: { 'Accept':'application/json' } });
      const data = await res.json();
      document.querySelectorAll('.skel').forEach(el => el.remove());
      (data.messages || []).forEach(m => addBubble(m));

      if ((data.messages || []).length === 0) {
        if (channel === 'wizard') {
          renderStepper();
          addBubble({role:'assistant', content:`${state.steps[0].title}\n\n${state.steps[0].prompt}`, created_at:new Date().toISOString()});
        }
        if (channel === 'finder') addBubble({role:'assistant', content:'Sebutkan unit/divisimu terlebih dahulu. (Contoh: Pabrik Tuban – Produksi)', created_at:new Date().toISOString()});
        if (channel === 'direct') addBubble({role:'assistant', content:'Silakan jelaskan ide yang sudah kamu punya atau langsung ajukan pertanyaan.', created_at:new Date().toISOString()});
      } else {
        if (channel === 'wizard') renderStepper();
      }
    } catch (e) { console.error(e); }
  }

  async function sendMessage(text) {
    let payload = text;

    if (channel === 'wizard') {
      payload = `[WIZARD:${currentWizardKey()}] ${text}`;
    } else if (channel === 'finder') {
      if (!state.finder.unit) {
        state.finder.unit = text.trim();
        payload = `[FINDER:unit] ${state.finder.unit}`;
      } else {
        payload = `[FINDER:refine:${state.finder.unit}] ${text}`;
      }
    }

    // tampilkan animasi mengetik saat menunggu respons
    addTyping();

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
        body: JSON.stringify({ message: payload, c: channel })
      });
    } catch (err) {
      removeTyping();
      addBubble({role:'assistant', content:'Koneksi gagal.', created_at:new Date().toISOString()});
      return;
    }

    removeTyping();

    if (!res.ok) {
      addBubble({role:'assistant', content:`Gagal (${res.status}).`, created_at:new Date().toISOString()});
      return;
    }
    const data = await res.json();
    addBubble({role:'assistant', content: data?.assistant?.content || 'Tidak ada jawaban.', created_at:new Date().toISOString()});

    if (channel === 'wizard') {
      nextWizardStep();
    } else if (channel === 'finder' && !state.finder.unit) {
      addBubble({role:'assistant', content:'Terima kasih. Jika perlu, sebutkan lokasi/sub-unit atau masalah spesifik. Jika tidak ada, tulis “lanjutkan”.', created_at:new Date().toISOString()});
    }
  }

  formEl.addEventListener('submit', (e) => {
    e.preventDefault(); e.stopPropagation();
    const raw = (inputEl.value || '').trim();
    if (!raw) return;
    inputEl.value = '';
    addBubble({role:'user', content: raw, created_at:new Date().toISOString()});
    if (channel === 'wizard' && isNextCmd(raw)) {
    // user minta pindah langkah → langsung maju, TIDAK call backend
    nextWizardStep();
    return;
  }
    sendMessage(raw);
  });
  btnSend.addEventListener('click', (e) => { e.preventDefault(); formEl.dispatchEvent(new Event('submit', { cancelable:true, bubbles:true })); });
  inputEl.addEventListener('keydown', (e) => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); formEl.dispatchEvent(new Event('submit', { cancelable:true, bubbles:true })); } });

  loadHistory();
})();
</script>
@endpush
