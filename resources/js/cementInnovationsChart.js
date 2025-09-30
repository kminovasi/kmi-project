import {
  Chart,
  CategoryScale,
  LinearScale,
  BarElement,
  BarController,
  Tooltip,
  Legend,
} from "chart.js";
import ChartDataLabels from "chartjs-plugin-datalabels";

Chart.register(
  CategoryScale,
  LinearScale,
  BarElement,
  BarController,
  Tooltip,
  Legend,
  ChartDataLabels
);

document.addEventListener("DOMContentLoaded", () => {
  const ctx = document
    .getElementById("cement-innovation-chart")
    ?.getContext("2d");
  const chartData = window.cementInnovationChartData;

  if (!ctx || !chartData) {
    console.error("Canvas atau data cement group tidak ditemukan.");
    return;
  }

  const { labels, implemented, idea_box, logos } = chartData;
  const logoImages = [];

  // preload logo
  Promise.all(
    logos.map((url, i) => {
      return new Promise((resolve) => {
        const img = new Image();
        img.crossOrigin = "anonymous";
        img.onload = () => {
          logoImages[i] = img;
          resolve();
        };
        img.onerror = () => {
          console.warn(`Gagal load logo cement ke-${i}`, url);
          resolve();
        };
        img.src = url;
      });
    })
  ).then(() => {
    // plugin gambar: turunkan logo & samakan tinggi
    const imagePlugin = {
      id: "customImagePlugin",
      afterDraw(chart) {
        const { ctx, chartArea, scales } = chart;
        const xScale = scales.x;

        const OFFSET_Y = 10;     // + turun dari sumbu-X (px). Ubah jika perlu
        const FIXED_HEIGHT = 28; // semua logo tinggi 28px (seragam)

        chart.data.labels.forEach((_, i) => {
          const img = logoImages[i];
          if (!img) return;

          const x = xScale.getPixelForValue(i);
          const yTop = chartArea.bottom + OFFSET_Y;

          const ar = (img.width || 1) / (img.height || 1);
          const imgHeight = FIXED_HEIGHT;
          const imgWidth = imgHeight * ar;

          ctx.save();
          ctx.imageSmoothingQuality = "high";
          ctx.drawImage(img, x - imgWidth / 2, yTop, imgWidth, imgHeight);
          ctx.restore();
        });
      },
    };

    new Chart(ctx, {
      type: "bar",
      data: {
        labels,
        datasets: [
          {
            label: "Implemented",
            data: implemented,
            backgroundColor: "#67a9cf",
          },
          {
            label: "Idea Box",
            data: idea_box,
            backgroundColor: "#ef8a62",
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: {
          // ruang untuk logo di bawah (FIXED_HEIGHT + OFFSET_Y ≈ 38)
          padding: { bottom: 50 },
        },
        plugins: {
          legend: { position: "top" },
          tooltip: {
            callbacks: {
              title: (items) => labels[items[0].dataIndex],
            },
          },
          datalabels: {
            anchor: "center",
            align: "center",
            font: { size: 14, weight: "bold" },
            color: "#000",
          },
        },
        scales: {
          x: {
            ticks: { display: false }, // sembunyikan label x (diganti logo)
            grid: { display: false },
          },
          y: {
            beginAtZero: true,
            title: { display: true, text: "Jumlah Inovasi" },
          },
        },
      },
      plugins: [ChartDataLabels, imagePlugin],
    });
  });
});
