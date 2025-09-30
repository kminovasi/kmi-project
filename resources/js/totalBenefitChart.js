import {
  Chart,
  CategoryScale,
  LinearScale,
  BarController,
  BarElement,
  Tooltip,
  Legend,
  LineElement,
  LineController,
  PointElement,
} from "chart.js";
import ChartDataLabels from "chartjs-plugin-datalabels";

// Rupiah
const formatRupiah = (value) =>
  new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    minimumFractionDigits: 0,
  }).format(value);

Chart.register(
  CategoryScale,
  LinearScale,
  BarController,
  BarElement,
  Tooltip,
  Legend,
  LineElement,
  LineController,
  PointElement,
  ChartDataLabels
);

// ===== (CHANGED) Loader logo -> return array logo lokal =====
const loadLogos = async (urls) => {
  const images = new Array(urls.length);
  await Promise.all(
    urls.map((url, index) => {
      return new Promise((resolve, reject) => {
        const img = new Image();
        img.crossOrigin = "anonymous";
        img.src = url;
        img.onload = () => {
          images[index] = img;
          resolve();
        };
        img.onerror = reject;
      });
    })
  );
  return images;
};

// ===== (CHANGED) Plugin factory: tiap chart pakai images sendiri =====
const makeImagePlugin = (images, { offset = 50, imgWidth = 30, axis = "y" } = {}) => ({
  id: "customImagePlugin_" + Math.random().toString(36).slice(2),
  afterDraw: (chart) => {
    const { ctx, chartArea, scales } = chart;
    const scale = axis === "y" ? scales.y : scales.x;
    if (!scale || !images) return;

    chart.data.labels.forEach((_, index) => {
      // kategori -> getPixelForTick
      const pos = scale.getPixelForTick(index);
      const x = chartArea.left - offset;

      const img = images[index];
      if (!img) return;

      const aspect = img.width / img.height;
      const h = imgWidth / aspect;

      ctx.drawImage(img, x, pos - h / 2, imgWidth, h);
    });
  },
});

// ===== Chart 1: Total Benefit (HORIZONTAL bila Superadmin) =====
document.addEventListener("DOMContentLoaded", async () => {
  const el = document.getElementById("total-benefit-chart");
  if (!el || !window.chartDataTotalBenefit) return;

  const data = window.chartDataTotalBenefit;
  const chartType = data.isSuperadmin ? "bar" : "line";

  // Sorting berdasarkan dataset[0]
  const sorted = data.labels
    .map((label, index) => ({
      label,
      logo: data.logos[index],
      values: data.datasets.map((ds) => ds.data[index] ?? 0),
    }))
    .sort((a, b) => (b.values[0] || 0) - (a.values[0] || 0));

  data.labels = sorted.map((s) => s.label);
  data.logos = sorted.map((s) => s.logo);
  data.datasets.forEach((ds, di) => {
    ds.data = sorted.map((s) => s.values[di] ?? 0);
  });

  // (CHANGED) muat logo lokal utk chart ini
  const images = await loadLogos(data.logos);
  const imagePlugin = makeImagePlugin(images, { offset: 50, imgWidth: 30, axis: "y" });

  new Chart(el.getContext("2d"), {
    type: chartType,
    data: {
      labels: data.labels,
      datasets: data.datasets,
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      indexAxis: "y",
      layout: {
        padding: { left: 50 },
      },
      plugins: {
        legend: { position: "top" },
        title: { display: true, text: "Total Benefit Finansial (Tahun Ini)" },
        datalabels: {
          display: true,
          color: "black",
          align: "end",
          anchor: "center",
          formatter: (value) => formatRupiah(value),
          font: { weight: "bold", size: 12 },
        },
        tooltip: {
          callbacks: { label: (ti) => formatRupiah(ti.raw) },
        },
      },
      scales: {
        y: {
          ticks: { display: data.isSuperadmin ? false : true },
        },
        x: {
          title: { display: true, text: "Benefit Finansial" },
          beginAtZero: true,
          ticks: {
            callback: (val) => formatRupiah(val),
          },
        },
      },
    },
    plugins: [imagePlugin],
  });
});

// ===== Chart 2: Total Potential Benefit =====
document.addEventListener("DOMContentLoaded", async () => {
  const el = document.getElementById("total-potential-benefit-chart");
  if (!el || !window.chartDataTotalPotentialBenefit) return;

  const data = window.chartDataTotalPotentialBenefit;
  const chartType = data.isSuperadmin ? "bar" : "line";

  // Sorting berdasarkan TOTAL semua tahun
  const sorted = data.labels
    .map((label, index) => ({
      label,
      logo: data.logos[index],
      values: data.datasets.map((ds) => ds.data[index] ?? 0),
    }))
    .sort(
      (a, b) =>
        b.values.reduce((s, v) => s + v, 0) -
        a.values.reduce((s, v) => s + v, 0)
    );

  data.labels = sorted.map((s) => s.label);
  data.logos = sorted.map((s) => s.logo);
  data.datasets.forEach((ds, di) => {
    ds.data = sorted.map((s) => s.values[di] ?? 0);
  });

  // (CHANGED) muat logo lokal utk chart ini
  const images = await loadLogos(data.logos);
  const imagePlugin = makeImagePlugin(images, {
    offset: 50,
    imgWidth: 30,
    axis: data.isSuperadmin ? "y" : "x",
  });

  new Chart(el.getContext("2d"), {
    type: chartType,
    data: {
      labels: data.labels,
      datasets: data.datasets,
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      indexAxis: data.isSuperadmin ? "y" : "x",
      layout: {
        padding: { left: 50 },
      },
      plugins: {
        legend: { position: "top" },
        title: { display: true, text: "Total Potential Benefit (Tahun Ini)" },
        datalabels: {
          display: true,
          color: "black",
          align: "end",
          anchor: "center",
          formatter: (value) => formatRupiah(value),
          font: { weight: "bold", size: 12 },
        },
        tooltip: {
          callbacks: { label: (ti) => formatRupiah(ti.raw) },
        },
      },
      scales: {
        y: {
          ticks: { display: data.isSuperadmin ? false : true },
        },
        x: {
          title: { display: true, text: "Potential Benefit" },
          beginAtZero: true,
          ticks: {
            callback: (val) => formatRupiah(val),
          },
        },
      },
    },
    plugins: [imagePlugin],
  });
});
