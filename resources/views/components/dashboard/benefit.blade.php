@vite(['resources/css/app.css'])

<div class="card-header bg-gradient-primary d-flex justify-content-between align-items-center p-3 rounded">
    <h4 class="text-white">Akumulasi Total Benefit (Real & Potensial)</h4>
    <div class="dropdown">
        <button class="btn btn-pink dropdown-toggle d-flex align-items-center" type="button" id="yearDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-calendar me-2"></i> <!-- Added margin-end for spacing -->
            {{ $year }}
        </button>
        <ul class="dropdown-menu" aria-labelledby="yearDropdown">
            @foreach ($availableYears as $availableYear)
                <li>
                    <a class="dropdown-item" href="{{ route('dashboard', ['year' => $availableYear]) }}">
                        {{ $availableYear }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>

<div>
    <canvas id="benefitChart"></canvas>
</div>

<!-- Siapkan data dalam elemen data-attributes -->
<div id="chartDataAkumulasiBenefit" data-labels='@json($charts['labels'] ?? [])' data-data='@json($charts['data'] ?? [])'
    data-logos='@json($charts['logos'] ?? [])'></div>

