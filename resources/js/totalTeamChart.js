import {
  Chart, CategoryScale, LinearScale, BarController, BarElement, Tooltip,
} from "chart.js";
import ChartDataLabels from "chartjs-plugin-datalabels";

Chart.register(CategoryScale, LinearScale, BarController, BarElement, Tooltip, ChartDataLabels);

// ====== Logo loader
const logoImages = [];
const loadLogos = async (logos = []) => {
  try {
    await Promise.all(
      logos.map((url, index) => new Promise((resolve, reject) => {
        const img = new Image();
        img.src = url;
        img.onload = () => { logoImages[index] = img; resolve(); };
        img.onerror = reject;
      })),
    );
  } catch (err) {
    console.error("[TotalTeamChart] Error loading logos:", err);
  }
};

// ====== Plugin gambar logo
const imagePlugin = {
  id: "customImagePlugin",
  afterDraw: (chart) => {
    const { ctx, chartArea, scales } = chart;
    if (!chart?.data?.labels?.length) return;

    chart.data.labels.forEach((_, index) => {
      const x = scales.x.getPixelForTick(index);
      const y = chartArea.bottom;
      const img = logoImages[index];
      if (!img) return;

      const aspectRatio = img.width / img.height || 1;
      const imgWidth = 30;
      const imgHeight = imgWidth / aspectRatio;
      ctx.drawImage(img, x - imgWidth / 2, y, imgWidth, imgHeight);
    });
  },
};

document.addEventListener("DOMContentLoaded", async () => {
  const canvas = document.getElementById("total-team-chart");
  if (!canvas) {
    console.error("[TotalTeamChart] Canvas #total-team-chart tidak ditemukan");
    return;
  }
  const ctx = canvas.getContext("2d");

  // Ambil data global
  const chartData = window.chartDataTotalTeam || {};
  const labels = chartData.labels || [];
  const datasets = Array.isArray(chartData.datasets) ? chartData.datasets : [];
  const companyIds = chartData.company_ids || []; // <— gunakan ini untuk onClick
  const logos = chartData.logos || [];

  // Validasi panjang data
  const bad = datasets
    .map((ds, i) => ({ i, len: ds?.data?.length ?? -1 }))
    .filter(r => r.len !== labels.length);
  if (bad.length) {
    console.warn("[TotalTeamChart] Panjang data tidak sejajar dengan labels:", bad);
  }

  // Pastikan nilai numeric untuk datalabels
  const safeDatasets = datasets.map(ds => ({
    ...ds,
    data: (ds.data || []).map(v => typeof v === "number" ? v : Number(v || 0)),
  }));

  await loadLogos(logos);

  const chart = new Chart(ctx, {
    type: "bar",
    data: {
      labels,
      datasets: safeDatasets,
    },
    options: {
      responsive: true,
      layout: { padding: { bottom: 50 } },
      plugins: {
        legend: { position: "top" },
        title: { display: true, text: "Total Tim Per Perusahaan" },
        datalabels: {
          display: true,
          align: "end",
          anchor: "end",
          formatter: (value) => {
            const n = (typeof value === "number" ? value : Number(value || 0));
            return n.toLocaleString("id-ID");
          },
          font: { weight: "bold", size: 12 },
        },
      },
      scales: {
        x: { title: { display: false }, ticks: { display: false } },
        y: { title: { display: true, text: "Jumlah Tim" } },
      },
      onClick: (_evt, elements) => {
        if (!elements?.length) return;
        const index = elements[0].index;
        const companyId = companyIds?.[index]; // <— ambil dari array sejajar
        console.log("[TotalTeamChart] onClick companyId:", companyId, "index:", index);
        if (companyId != null && companyId !== "") {
          window.location.href = `/detail-company-chart/${companyId}`;
        }
      },
      onHover: function (event, chartElement) {
        event.native.target.style.cursor = chartElement.length ? "pointer" : "default";
      },
    },
    plugins: [imagePlugin],
  });
});
