<div class="card p-3">
  <div class="card-body">
      <h5 class="card-title">Total Team per Perusahaan</h5>
      <div style="position:relative; width:100%; max-width:100%;">
        <canvas id="chartCanvasTotalTeamCompany" style="width:100%; height:420px;"></canvas>
      </div>
  </div>
</div>

<script type="module" src="{{ asset('build/assets/totalTeamCompanyChart-c2f0a3ca.js') }}"></script>

<script type="module">
document.addEventListener('DOMContentLoaded', () => {
  const data = @json($chartData);
  const canvas = document.getElementById('chartCanvasTotalTeamCompany');
  if (!canvas) return;

  const render = window.renderTotalTeamCompanyChart;
  if (typeof render === 'function') {
    render(data);
  }
});
</script>
