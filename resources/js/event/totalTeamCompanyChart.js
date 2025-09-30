import Chart from "chart.js/auto";
import autocolors from "chartjs-plugin-autocolors";
import ChartDataLabels from "chartjs-plugin-datalabels";

let __ttcChart = null;

function normalize(data) {
  if (data && Array.isArray(data.labels) && Array.isArray(data.data)) {
    return { labels: data.labels, data: data.data.map(v => Number(v) || 0) };
  }
  if (Array.isArray(data) && data.length && typeof data[0] === "object") {
    const labels = data.map(it => String(it.company_name ?? it.company ?? "Unknown"));
    const values = data.map(it => Number(it.total_teams ?? it.value ?? 0) || 0);
    return { labels, data: values };
  }
  if (data && typeof data === "object") {
    const entries = Object.entries(data).map(([k, v]) => [String(k || "Lainnya"), Number(v) || 0]);
    entries.sort((a, b) => b[1] - a[1]);
    return { labels: entries.map(e => e[0]), data: entries.map(e => e[1]) };
  }
  return { labels: [], data: [] };
}

export function renderTotalTeamCompanyChart(chartDataTotalTeamCompany) {
  const { labels, data } = normalize(chartDataTotalTeamCompany);
  const canvasId = "chartCanvasTotalTeamCompany";
  const canvas = document.getElementById(canvasId);
  if (!canvas || !labels.length) return;
  const ctx = canvas.getContext("2d");

  const N = labels.length;
  const horizontal = N > 10; 
  canvas.style.height = horizontal ? Math.max(360, N * 30) + "px" : "420px";

  const barThickness = horizontal
    ? Math.max(10, Math.min(20, Math.floor((canvas.clientHeight || 400) / (N * 1.6))))
    : 24;

  const categoryPercentage = horizontal ? 0.7 : 0.45;
  const barPercentage      = horizontal ? 0.8 : 0.7;

  const trim = (s, max) => (s.length > max ? s.slice(0, max - 1) + "…" : s);

  if (__ttcChart?.destroy) __ttcChart.destroy();

  __ttcChart = new Chart(ctx, {
    type: "bar",
    data: {
      labels: labels.map(String),
      datasets: [{
        label: "Jumlah Tim",
        data,
        barThickness,
        maxBarThickness: barThickness,
        categoryPercentage,
        barPercentage,
        borderWidth: 1,
      }],
    },
    options: {
      indexAxis: horizontal ? "y" : "x",
      responsive: true,
      maintainAspectRatio: false,
      animation: { duration: 300 },
      plugins: {
        autocolors: { mode: "data" },
        legend: { display: false },
        tooltip: {
          callbacks: {
            title: (items) => (items[0] ? String(items[0].label) : ""),
            label: (ctx) => `Jumlah Tim: ${ctx.raw}`,
          },
        },
        datalabels: {
          anchor: horizontal ? "end" : "center",
          align:  horizontal ? "right" : "center",
          formatter: (v) => v,
          font: { size: 12, weight: "bold" },
          color: "#000",
          clamp: true,
        },
      },
      scales: {
        [horizontal ? "x" : "y"]: {
          beginAtZero: true,
          ticks: { color: "#000", precision: 0 },
          title: { display: true, text: "Jumlah Tim", color: "#000", font: { weight: "bold" } },
          grid: { drawBorder: false },
        },
        [horizontal ? "y" : "x"]: {
          ticks: {
            color: "#000",
            maxRotation: 0,
            autoSkip: !horizontal, 
            callback: (val, idx) => trim(labels[idx] ?? "", horizontal ? 32 : 18),
          },
          title: { display: true, text: "Perusahaan", color: "#000", font: { weight: "bold" } },
          grid: { drawBorder: false },
        },
      },
    },
    plugins: [autocolors, ChartDataLabels],
  });
}

if (typeof window !== "undefined") {
  window.renderTotalTeamCompanyChart = renderTotalTeamCompanyChart;
  console.log("[TotalTeamCompanyChart] ready v3");
}
