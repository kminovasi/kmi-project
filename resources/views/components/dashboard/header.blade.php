@push('css')
    <link
        href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.css"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .ai-analyze-bar{
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            background: linear-gradient(90deg, #ef4444, #b91c1c, #ef4444);
            background-size: 200% 100%;
            animation: gradRun 8s linear infinite;

            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;     
            gap: 12px;
            width: 100%;
        }

        .ai-analyze-bar::after{
        content:"";
        position:absolute; inset:-40% -20%;
        background: linear-gradient(120deg,
                    transparent 0%,
                    rgba(255,255,255,.12) 45%,
                    rgba(255,255,255,.22) 50%,
                    rgba(255,255,255,.12) 55%,
                    transparent 100%);
        transform: translateX(-120%);
        animation: sweep 3.2s ease-in-out infinite;
        pointer-events:none;
        }

        .ai-analyze-left{
            display:flex; align-items:center; gap:10px;
            color:#fff; font-weight:600;
            flex: 1;                
            min-width: 0;           
        }

       .ai-title{
            opacity:.95;
            font-size:1.5rem;    
            font-weight:700;
            margin:0;
            line-height:1.2;
            color:#fff;
        }
        .ai-actions{
            display:flex; align-items:center; gap:8px;
            margin-left: auto;      
        }

        .ai-btn{
            background:#fff; color:#111; border:none; border-radius:20px; padding:8px 14px;
            font-weight:700; box-shadow:0 3px 10px rgba(0,0,0,.08);
            display: inline-flex; align-items: center; justify-content: center;
            white-space: nowrap;
        }
        @keyframes gradRun{
        0%   { background-position: 0% 50%; }
        100% { background-position: 100% 50%; }
        }
        @keyframes sweep{
        0%   { transform: translateX(-120%) rotate(0.001deg); }
        60%  { transform: translateX(0%)    rotate(0.001deg); }
        100% { transform: translateX(120%)  rotate(0.001deg); }
        }

        @media (prefers-reduced-motion: reduce){
        .ai-analyze-bar{ animation: none; }
        .ai-analyze-bar::after{ display:none; }
        }
        .ai-actions{
            display:flex;
            flex-direction: row;          
            align-items:center;
            gap:10px;                    
            flex-wrap: wrap;             
            margin-left:auto;
        }
        /* pill border ala Canva */
        .ai-help-pill{
            display:inline-flex; align-items:center; gap:.4rem;
            padding:.35rem .8rem;
            border-radius:999px;
            font-weight:700; font-size:.9rem;
            border:2px solid transparent;
            background:
            linear-gradient(#ffffff,#ffffff) padding-box,
            linear-gradient(120deg,#c084fc,#60a5fa 45%,#22d3ee) border-box;
            color:#111; text-decoration:none;
            box-shadow:0 4px 14px rgba(96,165,250,.18);
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .ai-help-pill:hover{ transform:translateY(-1px); box-shadow:0 6px 18px rgba(96,165,250,.28); }
        .ai-help-pill i{ font-size:.95rem; line-height:1; }
    </style>
@endpush

<header class="marginForDashboard page-header">
    {{-- Registrasi --}}
    <div class="ai-analyze-bar mb-3">
        <div class="ai-analyze-left">
            <span class="ai-title">Saatnya Bersinar, Wujudkan Ide Hebatmu di Sini</span>
        </div>

        <div class="ai-actions">
            <a class="ai-btn" target="_blank"
            href="{{ route('paper.register.team') }}">
            Klik disini untuk mendaftar
            </a>

            <a class="ai-help-pill" href="{{ route('ai.chat.index') }}" target="_blank" aria-label="Buka Si-Ino Chat AI">
                <i data-feather="zap"></i>
                Dapatkan bantuan AI
            </a>
        </div>
    </div>
                    
    <div class="container-xl px-4">
        <div class="row align-items-center justify-content-between">
            <div class="col-md-6 col-sm-6 col-xs-6">
                <div class="d-flex align-items-center">
                    <div class="d-flex">
                        <h2 class="my-3">
                            @if (Auth::user()->role == 'User')
                                Dashboard Innovator
                            @elseif(Auth::user()->role == 'Admin')
                                Dashboard Pengelola Inovasi
                            @elseif(Auth::user()->role == 'Superadmin')
                                Dashboard Super Admin
                            @elseif(Auth::user()->role == 'BOD')
                                Dashboard BOD
                            @elseif(Auth::user()->role == 'Juri')
                                Dashboard Juri
                            @endif
                        </h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-3 col-xs-6 justify-content-end">
                <div class="d-flex">
                    @php
                        $formattedDateTime = now()->isoFormat('dddd 路 D MMMM YYYY') . ' 路 ' . now()->format('H:i');
                    @endphp
                    <div class="page-header-subtitle mt-1 d-flex align-items-center">
                        <i class="bi bi-calendar-date me-2"></i>
                        <span>{{ $formattedDateTime }}</span>
                    </div>
                    {{-- <button type="button" class="btn-sm m-2 btn btn-primary btn-sm btn-filter" data-bs-toggle="modal"
                        data-bs-target="#yearFilterInnovator">
                        <i class="fas fa-filter me-1"></i>
                    </button> --}}
                </div>
            </div>
        </div>
    </div>
</header>
