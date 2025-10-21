<div class="row mb-3">
    @vite(['resources/css/dashboard.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    @push('css')
        <style>
            .bg-event {
                background: #D84040;
            }

            .bg-innovations {
                background: #D84040;
            }

            .bg-purple {
                background: linear-gradient(45deg, #6f42c1, #5e35b1);
            }


            .icon-circle {
                min-height: 3.5rem;
                min-width: 3.5rem;
                height: 3.5rem;
                width: 3.5rem;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: rgba(255, 255, 255, 0.1);
            }

            .bg-white-25 {
                background-color: rgba(255, 255, 255, 0.25);
            }

            .card-footer {
                background-color: rgba(0, 0, 0, 0.1);
                border-top: 1px solid rgba(255, 255, 255, 0.15);
                padding: 0.75rem 1.25rem;
            }

            .text-lg {
                font-size: 1.5rem;
            }

            /* Responsive adjustments */
            @media (max-width: 768px) {
                .icon-circle {
                    min-height: 3rem;
                    min-width: 3rem;
                    height: 3rem;
                    width: 3rem;
                }

                .text-lg {
                    font-size: 1.25rem;
                }
            }

            .bg-gradient-primary {
                background: linear-gradient(45deg, #4e73df, #224abe);
            }

            .bg-gradient-green {
                background: #D84040;
            }


            .bg-white-25 {
                background-color: rgba(255, 255, 255, 0.25);
            }


            .text-lg {
                font-size: 1.5rem;
            }

            .modal-header {
                border-bottom: none;
            }

            .modal-footer {
                border-top: none;
            }

            .list-group-item {
                background-color: transparent;
            }

            .card-icon i{
                font-size:22px; line-height:1; color:#fff;   
                opacity:.95;
            }

            .metric-card .card-body{
                position: relative;
                padding-right: 5.25rem;   
                padding-bottom: 3.75rem;  
            }
            .metric-card .icon-circle{
                position: absolute;
                right: 16px;
                bottom: 16px;             
                width: 56px; height: 56px; border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
                background-color: rgba(255,255,255,.22);
                backdrop-filter: saturate(140%) blur(1px);
            }
            .metric-card .icon-circle i{
                display: inline-flex;
                font-size: 28px; line-height: 1; color: #fff; opacity: .95;
            }

            .bg-innovations, .bg-event, .bg-gradient-green { background:#D84040; }

            /* key-value garis rata kiri–kanan, anti patah */
            .kv-line{display:flex;justify-content:space-between;align-items:center;gap:.75rem}
            .kv-label,.kv-value{white-space:nowrap}
            .kv-value{font-weight:700}
            .text-nowrap{white-space:nowrap}

            /* Responsive */
            @media (max-width:768px){
                .metric-card .card-body{ padding-right: 4.25rem; padding-bottom: 3.25rem; }
                .metric-card .icon-circle{ right: 12px; bottom: 12px; width: 48px; height: 48px; }
                .metric-card .icon-circle i{ font-size: 22px; }
            }
        </style>
    @endpush

    {{-- Total Innovation --}}
    <div class="col-12 col-md-4 col-lg-4 col-xl-4 mb-4"> 
        <div class="card bg-innovations text-white h-100 shadow-lg metric-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <div class="small mb-1" style="font-weight: 700; font-size: 1rem; color: #ffffff;"
                            data-bs-toggle="modal" data-bs-target="#exampleModal">Inovasi Implemented</div>
                        <div class="text-lg fw-bold d-flex align-items-center">
                            <!-- Menampilkan total jumlah inovasi berdasarkan kategori -->
                            {{ $totalImplementedInnovations }}
                        </div>
                    </div>
                    <div class="icon-circle bg-white-25 flex-shrink-0">
                        <i class="fa-solid fa-rocket fa-xl text-white"
                            style="font-size: 30px; font-weight: bolder; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between small">
                <a class="text-white stretched-link" href="#" data-bs-toggle="modal"
                    data-bs-target="#exampleModal">
                    Lihat Detail
                </a>
                <div class="text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>

    <!-- Detail Implemented Innovation -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header  text-white">
                    <h5 class="modal-title fw-bold d-flex align-items-center" id="exampleModalLabel">
                        <i data-feather="zap" class="me-2"></i> <span class="fw-bold">Detail Inovasi</span>
                    </h5>
                    <button type="button" class="btn-close" style="color: black;" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body bg-light">
                    <div class="row">
                        @foreach ($implemented as $item)
                            @php
                                $colors = [
                                    'text-success',
                                    'text-warning',
                                    'text-info',
                                    'text-primary',
                                    'text-secondary',
                                ];
                                $icons = ['zap', 'layers', 'box', 'shield', 'star'];
                                $color = $colors[$loop->index % count($colors)];
                                $icon = $icons[$loop->index % count($icons)];
                            @endphp
                            <div class="col-md-6 mb-4">
                                <a href="{{ route('dashboard.listPaper', [
                                    'category' => $item['category_name'],
                                    'status' => 'implemented'
                                ]) }}"
                                class="text-decoration-none list-paper-link">
                                    <div class="card shadow-sm border-0 rounded">
                                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                            <i data-feather="{{ $icon }}" class="me-2 {{ $color }}"></i>
                                            <h5 class="m-0 fw-bold {{ $color }}">{{ $item["category_name"] }}</h5>
                                            <span class="badge bg-primary rounded-pill fs-5 fw-bold">
                                                {{ $item["count"] ?? 0 }}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer bg-gradient-light">
                    <button type="button" class="btn btn-outline-primary fw-bold" data-bs-dismiss="modal">
                        <i data-feather="x-circle" class="me-1"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Inovasi Metodologi --}}
    <div class="col-12 col-md-4 col-lg-4 col-xl-4 mb-4">
    <div class="card bg-innovations text-white h-100 shadow-lg metric-card">
        <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div class="me-3">
            <div class="small mb-1" style="font-weight:700;font-size:1rem;color:#fff"
                data-bs-toggle="modal" data-bs-target="#metodologiModal">
                Inovasi Metodologi
            </div>
            <div class="text-lg fw-bold d-flex align-items-center">
                {{ collect($metodologi)->sum('count') }}   
            </div>
            </div>
            <div class="icon-circle bg-white-25 flex-shrink-0">
            <i class="fa-solid fa-flask fa-xl text-white"
                style="font-size:30px;font-weight:bolder;text-shadow:2px 2px 4px rgba(0,0,0,.3);"></i>
            </div>
        </div>
        </div>
        <div class="card-footer d-flex align-items-center justify-content-between small">
        <a class="text-white stretched-link" href="#" data-bs-toggle="modal" data-bs-target="#metodologiModal">
            Lihat Detail
        </a>
        <div class="text-white"><i class="fas fa-angle-right"></i></div>
        </div>
    </div>
    </div>

    <!-- Detail Methodology Innovation -->
    <div class="modal fade" id="metodologiModal" tabindex="-1" aria-labelledby="metodologiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header  text-white">
                    <h5 class="modal-title fw-bold d-flex align-items-center" id="metodologiModalLabel">
                        <i data-feather="zap" class="me-2"></i> <span class="fw-bold">Detail Inovasi</span>
                    </h5>
                    <button type="button" class="btn-close" style="color: black;" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body bg-light">
                    <div class="row">
                        @foreach ($metodologi as $item)
                            @php
                                $colors = [
                                    'text-success',
                                    'text-warning',
                                    'text-info',
                                    'text-primary',
                                    'text-secondary',
                                ];
                                $icons = ['zap', 'layers', 'box', 'shield', 'star'];
                                $color = $colors[$loop->index % count($colors)];
                                $icon = $icons[$loop->index % count($icons)];
                            @endphp
                            <div class="col-md-6 mb-4">
                                <a href="{{ route('dashboard.listPaperMetodologi', ['metodologi_id' => $item['id']]) }}"
                                class="text-decoration-none list-paper-link">
                                    <div class="card shadow-sm border-0 rounded">
                                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                            <i data-feather="{{ $icon }}" class="me-2 {{ $color }}"></i>
                                            <h5 class="m-0 fw-bold {{ $color }}">{{ $item["category_name"] }}</h5>
                                            <span class="badge bg-primary rounded-pill fs-5 fw-bold">
                                                {{ $item["count"] ?? 0 }}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer bg-gradient-light">
                    <button type="button" class="btn btn-outline-primary fw-bold" data-bs-dismiss="modal">
                        <i data-feather="x-circle" class="me-1"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Idea Box --}}
    <div class="col-12 col-md-4 col-lg-4 col-xl-4 mb-4">
        <div class="card bg-innovations text-white h-100 shadow-lg metric-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3 flex-grow-1">
                        <div class="small mb-1" style="font-weight: 700; font-size: 1rem; color: #ffffff;">Inovasi IDEA BOX
                        </div>
                        <div class="text-lg fw-bold d-flex align-items-center">
                            {{ $totalIdeaBoxInnovations }}
                        </div>
                    </div>
                    <div class="icon-circle bg-white-25 flex-shrink-0">
                        <i class="fa-solid fa-lightbulb fa-xl text-white"
                            style="font-size: 30px; font-weight: bolder; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between small">
                <a class="text-white stretched-link" href="#" data-bs-toggle="modal"
                    data-bs-target="#ideaBoxModal">
                    Lihat Detail
                </a>
                <div class="text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
    
    <!-- Detail Idea box -->
    <div class="modal fade" id="ideaBoxModal" tabindex="-1" aria-labelledby="ideaBoxModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header text-white">
                    <h5 class="modal-title fw-bold d-flex align-items-center" id="ideaBoxModalLabel">
                        <i class="fa-solid fa-lightbulb me-2"></i> <span class="fw-bold">Detail Idea Box</span>
                    </h5>
                    <button type="button" class="btn-close" style="color: black;" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body bg-light">
                    <div class="row">
                        @foreach ($ideaBox as $item)
                            @php
                                $colors = [
                                    'text-success',
                                    'text-warning',
                                    'text-info',
                                    'text-primary',
                                    'text-secondary',
                                ];
                                $icons = ['zap', 'layers', 'box', 'shield', 'star'];
                                $color = $colors[$loop->index % count($colors)];
                                $icon = $icons[$loop->index % count($icons)];
                            @endphp
                            <div class="col-md-6 mb-4">
                                <a href="{{ route('dashboard.listPaper', [
                                    'category' => $item['category_name'],
                                    'status' => 'idea box'
                                ]) }}"
                                class="text-decoration-none list-paper-link">
                                    <div class="card shadow-sm border-0 rounded">
                                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                            <i data-feather="{{ $icon }}" class="me-2 {{ $color }}"></i>
                                            <h5 class="m-0 fw-bold {{ $color }}">{{ $item["category_name"] }}</h5>
                                            <span class="badge bg-primary rounded-pill fs-5 fw-bold">
                                                {{ $item["count"] ?? 0 }}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="modal-footer bg-gradient-light">
                    <button type="button" class="btn btn-outline-primary fw-bold" data-bs-dismiss="modal">
                        <i class="fa-solid fa-x-circle me-1"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Event Active --}}
    @if ($isSuperadmin || $isAdmin)
        <div class="col-12 mb-4">
            <div class="card bg-event text-white h-100 metric-card">

                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3 flex-grow-1">
                            <div class="small mb-1" style="font-weight: 700; font-size: 1rem; color: #ffffff;">Data Statistik Event</div>
                            <div class="text-lg fw-bold d-flex align-items-center">
                                {{ $totalActiveEvents }}
                                <small class="ms-2">(Event)</small>
                            </div>
                        </div>
                        <div class="icon-circle bg-white-25 flex-shrink-0">
                            <i class="fas fa-calendar-alt fa-xl"
                                style="font-size: 40px; font-weight: bolder; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3); color: #ffffff;"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <a class="text-white stretched-link" href="{{ route('dashboard-event.list') }}">
                        Lihat Daftar Statistik Event
                    </a>
                    <div class="text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    @endif

    <div class="col-12 mb-4"></div>

   <div class="col-12 mb-4">
  <div class="card bg-gradient-green text-white h-100 metric-card">

            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <!-- Informasi Teks -->
                    <div class="me-3 flex-grow-1 d-flex flex-column gap-y-2">
                        <div class="small mb-1" style="font-weight: 700; font-size: 1.5rem; color: #ffffff;">
                            Akumulasi Total Inovator
                        </div>
                        <div class="text-lg fw-bold d-flex align-items-center" id="totalInnovators">
                            {{ $totalInnovators }}
                            <small class="ms-2">(Orang)</small>
                        </div>
                        <!-- Persentase laki-laki dan perempuan -->
                        <div class="mt-3">
                            <span style="font-weight: 600;">Total Inovator:</span>
                            <div class="mt-3 d-flex justify-content-between">
                                <span>Laki-laki:</span>
                                <span class="fw-bold" id="totalInnovatorsMale">
                                    {{ $totalInnovatorsMale }} Orang
                                </span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Perempuan:</span>
                                <span class="fw-bold" id="totalInnovatorsFemale">
                                    {{ $totalInnovatorsFemale }} Orang
                                </span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Outsoure:</span>
                                <span class="fw-bold" id="totalInnovatorsOutsource">
                                    {{ $totalInnovatoresOutsource }} Orang
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- Chart -->
                    <div class="chart-container" style="width: 230px; height: 230px; background-color: transparent;">
                        <canvas id="innovatorChart" style="background-color: transparent;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 mb-4"></div>

    <div class="col-lg-3 col-xl-4 mb-4 mx-auto">
        <div class="card bg-gradient-green text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3 flex-grow-1">
                        <div class="small mb-1" style="font-weight: 700; font-size: 1rem; color: #ffffff;">
                            Inovator Laki-laki
                        </div>
                        <div class="text-lg fw-bold d-flex align-items-center">
                            {{ $totalInnovatorsMale }}
                            <small class="ms-2">(Orang)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-xl-4 mb-4 mx-auto">
        <div class="card bg-gradient-green text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3 flex-grow-1">
                        <div class="small mb-1" style="font-weight: 700; font-size: 1rem; color: #ffffff;">
                            Inovator Perempuan</div>
                        <div class="text-lg fw-bold d-flex align-items-center">
                            {{ $totalInnovatorsFemale }}
                            <small class="ms-2">(Orang)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-xl-4 mb-4 mx-auto">
        <div class="card bg-gradient-green text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3 flex-grow-1">
                        <div class="small mb-1" style="font-weight: 700; font-size: 1rem; color: #ffffff;">
                            Inovator Outsource</div>
                        <div class="text-lg fw-bold d-flex align-items-center">
                            {{ $totalInnovatoresOutsource }}
                            <small class="ms-2">(Orang)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

{{-- <div class="col-12 mb-4">
    <div class="card bg-gradient-green text-white h-100 metric-card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="me-3 flex-grow-1">
                <div class="small mb-1" style="font-weight:700;font-size:1rem;color:#fff">Inovator per Usia</div>
                <div class="text-lg fw-bold d-flex align-items-center">
                    {{ $ageTotal ?? 0 }} <small class="ms-2">(Orang)</small>
                </div>
                </div>
                <div class="chart-container" style="width:420px;height:260px;background:transparent;">
                </div>
            </div>
            </div>
        </div>
    </div>
</div> --}}

<div class="card border-0 shadow-lg mt-4">
  <div class="card-header bg-gradient-primary">
    <h5 class="card-title text-white">Statistik Inovator Berdasarkan Usia</h5>
  </div>
  <div class="card-body">
@php
  $yy = $years ?? [];
  $columnTotals = array_fill_keys($yy, 0);
  $grandTotal = 0;
@endphp

    <div class="table-responsive">
      <table class="table table-bordered table-striped table-hover align-middle">
        <thead class="table-primary text-center">
          <tr>
            <th>Kelompok Usia</th>
           @foreach (($years ?? []) as $year)
              <th>{{ $year }}</th>
            @endforeach
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
         @foreach (($ageGroups ?? []) as $group)
            @php $rowTotal = 0; @endphp
            <tr class="text-center">
              <td class="text-start fw-bold">{{ $group }}</td>
              @foreach ($years as $year)
                @php
                  $value = $ageData[$group][$year] ?? 0;
                  $rowTotal += $value;
                  $columnTotals[$year] += $value;
                @endphp
                <td>{{ $value }}</td>
              @endforeach
              <td class="fw-bold bg-light">{{ $rowTotal }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot class="table-light text-center">
          <tr>
            <th class="text-start">Total per Tahun</th>
            @foreach ($years as $year)
              @php $grandTotal += $columnTotals[$year]; @endphp
              <th>{{ $columnTotals[$year] }}</th>
            @endforeach
            <th class="table-primary text-white">{{ $grandTotal }}</th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script>
    function renderInnovatorChart(total, male, female, outsource) {
        const ctx = document.getElementById('innovatorChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{
                    data: [
                        total > 0 ? Math.round((male / total) * 100 * 100) / 100 : 0,
                        total > 0 ? Math.round((female / total) * 100 * 100) / 100 : 0,
                        total > 0 ? Math.round((outsource / total) * 100 * 100) / 100 : 0
                    ],
                    backgroundColor: ['#fff', '#c0c0c0', '#71888e'],
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            color: 'red',
                            font: {
                                size: 15
                            }
                        }
                    },
                    datalabels: {
                        color: 'red',
                        font: {
                            size: 20,
                            weight: 'bold'
                        },
                        formatter: (value) => `${value}%`, // Menampilkan persentase di dalam chart
                        anchor: 'bottom',
                        align: 'bottom'
                    },
                    tooltip: {
                        enabled: false
                        // Nonaktifkan tooltip jika tidak dibutuhkan
                    }
                },
                maintainAspectRatio: false,
                responsive: true
            },
            plugins: [ChartDataLabels]
        });
    }
    document.addEventListener('DOMContentLoaded', function () {
        const total = parseInt(document.getElementById('totalInnovators').textContent.replace(/\D/g, ''));
        const male = parseInt(document.getElementById('totalInnovatorsMale').textContent.replace(/\D/g, ''));
        const female = parseInt(document.getElementById('totalInnovatorsFemale').textContent.replace(/\D/g, ''));
        const outsource = parseInt(document.getElementById('totalInnovatorsOutsource').textContent.replace(/\D/g, ''));
        renderInnovatorChart(total, male, female, outsource);
    });

    (function(){
        const labels = @json($ageLabels ?? []);
        const data    = @json($ageCounts ?? []);
        const total   = @json($ageTotal ?? 0);

        const ctx = document.getElementById('ageChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: { labels, datasets: [{
                label: 'Orang',
                data,
                backgroundColor: ['#fff','#c0c0c0','#71888e','#9aa7ad','#d6dfe2','#bcbcbc'],
                borderColor: '#fff',
                borderWidth: 1,
                borderRadius: 6,
                maxBarThickness: 44
            }]},
            options: {
                maintainAspectRatio:false,
                plugins:{
                    legend:{display:false},
                    tooltip:{callbacks:{label:(c)=>{
                        const v=c.raw||0; const p= total? Math.round(v/total*1000)/10 : 0;
                        return ` ${v} orang (${p}%)`;
                    }}},
                    datalabels:{
                        anchor:'end',align:'end',offset:4,color:'#fff',
                        font:{size:12,weight:'bold'},
                        formatter:(v)=> total&&v? `${v} (${Math.round(v/total*1000)/10}%)` : ''
                    }
                },
                scales:{
                    x:{grid:{display:false},ticks:{color:'#fff'}},
                    y:{beginAtZero:true,grid:{color:'rgba(255,255,255,.15)'},ticks:{color:'#fff'}}
                }
            },
            plugins:[ChartDataLabels]
        });
    })();
</script>
