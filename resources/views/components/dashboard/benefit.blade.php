@vite(['resources/css/app.css'])

<style>
    h4 {
        font-size: 0.9rem;
    }
</style>

<div class="card-header bg-gradient-primary d-flex justify-content-between align-items-center p-3 rounded">
    <h4 class="text-white">Akumulasi Total Benefit (Real & Potensial)</h4>
    <div class="d-flex gap-2 align-items-center">
    {{-- Dropdown Start Year --}}
    <div class="dropdown">
        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="startYearDropdown" data-bs-toggle="dropdown" aria-expanded="false">
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
        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="endYearDropdown" data-bs-toggle="dropdown" aria-expanded="false">
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
    <button class="btn btn-sm btn-success" id="applyYearFilter">Terapkan</button>
</div>

</div>

<div>
    <canvas id="benefitChart"></canvas>
</div>

<div id="chartDataAkumulasiBenefit" data-labels='@json($charts['labels'] ?? [])' data-data='@json($charts['data'] ?? [])' data-logos='@json($charts['logos'] ?? [])'></div>

