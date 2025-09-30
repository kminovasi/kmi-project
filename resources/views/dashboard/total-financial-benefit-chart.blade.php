@extends('layouts.app')
@section('title', 'Total Benefit Chart | Dashboard')

@section('content')
    <div class="container mt-3">
        @if ($isSuperadmin)
            <div class="card">
                <div class="card-header" style="background-color: #eb4a3a">
                    <h5 class="text-white">Total Finansial Benefit per Perusahaan </h5>
                </div>
                <div class="card-body">
                    <canvas id="total-benefit-chart" style="height: 35rem;"></canvas>
                </div>
            </div>
            <x-dashboard.potential-benefit-total-chart />
            <x-dashboard.financial-benefit-chart-companies />
        @else
            <x-dashboard.financial-benefit-total-chart :is-superadmin="auth()->user()->role === 'Superadmin'" :user-company-code="auth()->user()->company_code" />
            <x-dashboard.potential-benefit-total :is-superadmin="auth()->user()->role === 'Superadmin'" :user-company-code="auth()->user()->company_code" />
        @endif
    </div>
@endsection

{{-- Bentuk payload yang dipakai totalBenefitChart-*.js --}}
<script type="module">
(() => {
  const raw = @json($chartDataTotalBenefit ?? []);
  const isSuperadmin = {{ $isSuperadmin ? 'true' : 'false' }};

  // 4 tahun terakhir (ikuti controller)
  const now = new Date().getFullYear();
  const years = [now - 3, now - 2, now - 1, now];

  // Kalau controller sudah kirim dalam bentuk final (punya datasets), pakai apa adanya.
  // Kalau belum, anggap raw = [{ company_name, financials:{year:value}, (optional) logo }]
  let payload;
  if (raw && Array.isArray(raw.datasets) && Array.isArray(raw.labels)) {
    payload = { ...raw, isSuperadmin };
  } else {
    const labels = Array.isArray(raw) ? raw.map(r => r.company_name) : [];
    const logos  = Array.isArray(raw) ? raw.map(r => r.logo ?? '') : [];
    const datasets = years.map(y => ({
      label: String(y),
      data: Array.isArray(raw) ? raw.map(r => Number((r.financials ?? {})[y] ?? 0)) : [],
      borderWidth: 1,
    }));
    payload = { labels, logos, datasets, isSuperadmin };
  }

  // Expose ke script chart
  window.chartDataTotalBenefit = payload;
})();
</script>

<script src="{{ asset('/build/assets/totalBenefitChart-556469cb.js') }}" type="module"></script>
