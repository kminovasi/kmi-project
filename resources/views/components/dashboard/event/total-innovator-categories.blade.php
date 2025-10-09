<div class="card p-3 mt-3">
  <h5 class="text-center">Akumulasi Total Kategori yang Dipilih Inovator</h5>

  <div class="mt-3 row d-flex justify-content-center"
       id="cardContainer"
       data-endpoint="{{ $categoryRoute }}">
  </div>
</div>

<div class="modal fade" id="innovatorModal" tabindex="-1" aria-labelledby="innovatorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="innovatorModalLabel">Detail Innovator</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div id="innovatorSummary" class="mb-3 fw-bold"></div>

        <div class="table-responsive">
          <table class="table table-sm align-middle">
            <thead>
              <tr>
                <th style="width:56px;">No.</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Tim</th>
              </tr>
            </thead>
            <tbody id="innovatorTableBody"><!-- rows injected --></tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<script type="module">
  const chartDataTotalInnovatorCategories = @json($chartData);
  const event_name = @json($event_name);
  const eventId = @json($eventId ?? null);

  const categoryColors = ["bg-success","bg-secondary","bg-info","bg-warning","bg-primary","bg-danger"];

  document.addEventListener("DOMContentLoaded", () => {
    const cardContainer = document.getElementById('cardContainer');

    cardContainer.innerHTML = chartDataTotalInnovatorCategories.labels.map((label, index) => {
      const colorIndex = index % categoryColors.length;
      const categoryColor = categoryColors[colorIndex];
      const count = chartDataTotalInnovatorCategories.data[index] ?? 0;

      const safeLabel = String(label).replace(/"/g,'&quot;');

      return `
        <div class="col-12 col-md-3 mb-3">
          <div class="card mb-3 custom-card ${categoryColor} js-category-card" role="button" data-category="${safeLabel}">
            <div class="card-body">
              <h6 class="card-title">${label}</h6>
              <p class="card-text"><strong>${count}</strong></p>
            </div>
          </div>
        </div>
      `;
    }).join('');

    cardContainer.querySelectorAll('.js-category-card').forEach(card => {
      card.addEventListener('click', async () => {
        const category = card.getAttribute('data-category');
        await openInnovatorModal({ eventId, category });
      });
    });
  });

  function escapeHtml(s) {
    return String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
  }

  async function openInnovatorModal({ eventId, category }) {
    const modalEl = document.getElementById('innovatorModal');
    const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);

    const $title   = document.getElementById('innovatorModalLabel');
    const $summary = document.getElementById('innovatorSummary');
    const $tbody   = document.getElementById('innovatorTableBody');

    $title.textContent = `Kategori: ${category}`;
    $summary.textContent = 'Memuat data…';
    $tbody.innerHTML = '';
    bsModal.show();

    const baseUrl = document.getElementById('cardContainer').dataset.endpoint;
    if (!baseUrl) {
      $summary.innerHTML = `<span class="text-danger">Endpoint tidak ditemukan (data-endpoint kosong).</span>`;
      return;
    }

    const skeleton = Array.from({ length: 5 }).map(() => `
      <tr>
        <td colspan="4">
          <div class="placeholder-glow">
            <span class="placeholder col-2"></span>
            <span class="placeholder col-4"></span>
            <span class="placeholder col-3"></span>
            <span class="placeholder col-2"></span>
          </div>
        </td>
      </tr>`).join('');
    $tbody.innerHTML = skeleton;

    const url = `${baseUrl}?category=${encodeURIComponent(category)}`;
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 15000);

    try {
      const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, signal: controller.signal });

      let payload = null;
      try { payload = await res.json(); } catch {}

      if (!res.ok) {
        const detail = (payload && (payload.error || payload.message)) || `HTTP ${res.status}`;
        throw new Error(detail);
      }

      const total = Number(payload.total_innovators ?? 0);
      const internal = Number(payload.internal_count ?? 0);
      const outsource = Number(payload.outsource_count ?? 0);
      $summary.textContent = `Total Innovator: ${total} (Internal: ${internal}, Outsource: ${outsource})`;


      const list = Array.isArray(payload.innovators) ? payload.innovators : [];
      if (!list.length) {
        $tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted">Tidak ada data.</td></tr>`;
        return;
      }

      $tbody.innerHTML = list.map((row, i) => `
        <tr>
          <td>${i + 1}</td>
          <td>${escapeHtml(row.user_name ?? '-')}</td>
          <td>${escapeHtml(row.email ?? '-')}</td>
          <td>${escapeHtml(row.team_name ?? '-')}</td>
        </tr>
      `).join('');

    } catch (e) {
      const msg = e.name === 'AbortError' ? 'Permintaan timeout (15s). Coba lagi.' : (e.message || 'Gagal memuat data');
      $summary.innerHTML = `<span class="text-danger">Gagal memuat data: ${escapeHtml(msg)}</span>`;
      $tbody.innerHTML = '';
    } finally {
      clearTimeout(timeoutId);
    }
  }
</script>

<style>
  .custom-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 10px 15px rgba(0,0,0,.1);
    transition: all .3s ease-in-out;
    margin: 0 auto;
    height: 130px;
    cursor: pointer; 
  }
  .custom-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,0,0,.15);
  }
  .custom-card .card-body {
    padding: 10px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    text-align: center;
    height: 100%;
  }
  .custom-card .card-title { font-size: 1rem; font-weight: 700; color: #fff; margin-bottom: 10px; }
  .custom-card .card-text { font-size: .9rem; color: #fff; }
  .custom-card .card-text strong { color: #fff; font-size: 2.7rem; }

  .bg-info { background-color: #17a2b8; }
  .bg-success { background-color: #28a745; }
  .bg-warning { background-color: #ffc107; }
  .bg-primary { background-color: #007bff; }
  .bg-danger { background-color: #dc3545; }

  @media (max-width: 768px) {
    .custom-card { width: 100%; height: 150px; }
    h5 { font-size: 1.3rem; }
  }
</style>
