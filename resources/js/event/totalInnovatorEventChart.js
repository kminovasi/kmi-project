import { Chart, registerables } from "chart.js";
import ChartDataLabels from "chartjs-plugin-datalabels";
import autocolors from "chartjs-plugin-autocolors";

Chart.register(...registerables, ChartDataLabels);

function toLabelsData(input) {
  if (input && Array.isArray(input.labels) && Array.isArray(input.data)) {
    return { labels: input.labels, data: input.data };
  }

  // Bentuk map -> ubah ke {labels, data}
  const entries = Object.entries(input || {}).map(([k, v]) => [
    (typeof k === "string" && k.trim()) || "Lainnya",
    Number(v) || 0,
  ]);

  // Hapus key kosong/invalid yang duplikat “Lainnya”
  const merged = {};
  for (const [k, v] of entries) merged[k] = (merged[k] || 0) + v;

  // Urutkan desc 
  const sorted = Object.entries(merged).sort((a, b) => b[1] - a[1]);

  return {
    labels: sorted.map(([k]) => k),
    data: sorted.map(([, v]) => v),
  };
}

// mapping label judul
function formatOrganizationUnit(unit) {
  const m = {
    directorate_name: "Direktorat",
    group_function_name: "Group Head",
    department_name: "Departemen",
    unit_name: "Unit",
    section_name: "Seksi",
    sub_section_of: "Sub Seksi",
  };
  return m[unit] || (unit || "Direktorat");
}

export function initializeTotalInnovatorEventChart(chartData, canvasId, organizationUnit) {
  console.group("[TotalInnovatorEventChart] Renderer");
  console.log("raw chartData:", chartData);
  const { labels, data } = toLabelsData(chartData);
  console.log("normalized.labels.length:", labels.length);
  console.log("normalized.data.length:", data.length);
  console.log("top5 preview:", labels.slice(0, 5).map((l, i) => [l, data[i]]));

  const canvas = document.getElementById(canvasId);
  if (!canvas) {
    console.error(`Canvas #${canvasId} tidak ditemukan`);
    console.groupEnd();
    return;
  }
  const ctx = canvas.getContext("2d");
  if (!ctx) {
    console.error(`Context 2D tidak tersedia untuk #${canvasId}`);
    console.groupEnd();
    return;
  }

  if (labels.length === 0) {
    console.warn("Tidak ada data untuk ditampilkan.");
    console.groupEnd();
    return;
  }

  console.time("chart_render");
  new Chart(ctx, {
    plugins: [autocolors, ChartDataLabels],
    type: "bar",
    data: {
      labels,
      datasets: [
        {
          label: "Total Inovator",
          data,
          borderWidth: 1,
          maxBarThickness: 40,
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: "top", labels: { color: "#000" } },
        autocolors: { mode: "data" },
        title: {
          display: true,
          text: `Total Inovator Berdasarkan ${formatOrganizationUnit(organizationUnit)}`,
          font: { size: 16, weight: "bold" },
        },
        datalabels: {
          display: true,
          anchor: "center",
          align: "center",
          formatter: (v) => v,
          font: { weight: "bold", size: 20 },
          color: "#000",
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { stepSize: 2, color: "#000" },
          title: { display: true, text: "Jumlah Inovator", font: { size: 14, weight: "bold" }, color: "#000" },
        },
        x: {
          ticks: { color: "#000" },
          title: { display: true, text: "Organisasi", font: { size: 14, weight: "bold" }, color: "#000" },
        },
      },
    },
  });
  console.timeEnd("chart_render");
  console.groupEnd();
}

if (typeof window !== "undefined") {
  window.initializeTotalInnovatorEventChart = initializeTotalInnovatorEventChart;
}
