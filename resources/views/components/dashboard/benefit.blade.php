<link rel="stylesheet" href="{{ asset('build/assets/app-6037ed8c.css') }}">

<style>
    h4 {
        font-size: 1rem;
    }

    .bi-exclamation-circle {
        font-size: 2.2rem;
    }
</style>

<div class="card-header bg-gradient-primary d-flex justify-content-between align-items-center p-3 rounded">
    <h4 class="text-white">Akumulasi Total Benefit (Real & Potensial)</h4>
    <div class="d-flex gap-2 align-items-center">
    {{-- Dropdown Start Year --}}
    <div class="dropdown">
        <button class="btn btn-sm btn-white dropdown-toggle" type="button" id="startYearDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            Start Year
        </button>
        <ul class="dropdown-menu" aria-labelledby="startYearDropdown">
            @foreach ($availableYears as $availableYear)
                <li>
                    <a class="dropdown-item start-year-option" href="#" data-startyear="{{ $availableYear }}">
                        {{ $availableYear }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Dropdown End Year --}}
    <div class="dropdown">
        <button class="btn btn-sm btn-white dropdown-toggle" type="button" id="endYearDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            End Year
        </button>
        <ul class="dropdown-menu" aria-labelledby="endYearDropdown">
            @foreach ($availableYears as $availableYear)
                <li>
                    <a class="dropdown-item end-year-option" href="#" data-endyear="{{ $availableYear }}">
                        {{ $availableYear }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Tombol apply filter --}}
    <button class="btn btn-sm btn-white" id="applyYearFilter">Terapkan</button>
</div>

</div>

<div>
    <canvas id="benefitChart"></canvas>
</div>

<div id="chartDataAkumulasiBenefit" data-labels='@json($charts['labels'] ?? [])' data-data='@json($charts['data'] ?? [])' data-logos='@json($charts['logos'] ?? [])'></div>

{{-- Modal End Year Lebih Kecil Dari Start Year  --}}
<div class="modal animated--zoom-in" id="invalidYearModal" tabindex="-1" aria-labelledby="invalidYearLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body text-center">
        <div class="mb-3">
            <i class="bi bi-exclamation-circle font-xl text-danger"></i>
        </div>
        <p class="text-capitalize text-md">
            Tahun akhir tidak boleh lebih kecil dari tahun awal!!
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

{{-- Modal Belum Memilih End Year dan Start Year  --}}
<div class="modal animated--zoom-in" id="noYearValue" tabindex="-1" aria-labelledby="noYearValueLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body text-center">
        <div class="mb-3">
            <i class="bi bi-exclamation-circle font-xl text-danger"></i>
        </div>
        <p class="text-capitalize text-md">
            Silahkan Memilih Tahun Awal dan Tahun Akhir Dulu!!
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
