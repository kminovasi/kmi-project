import {
    Chart,
    CategoryScale,
    LinearScale,
    BarController,
    BarElement,
    Tooltip,
} from "chart.js";
import ChartDataLabels from "chartjs-plugin-datalabels"; // Import plugin

Chart.register(
    CategoryScale,
    LinearScale,
    BarController,
    BarElement,
    Tooltip,
    ChartDataLabels,
);

// Array untuk menyimpan gambar logo
const logoImages = [];

// Fungsi untuk memuat semua gambar logo
const loadLogos = async (logos) => {
    try {
        await Promise.all(
            logos.map((url, index) => {
                return new Promise((resolve, reject) => {
                    const img = new Image();
                    img.src = url;
                    img.onload = () => {
                        logoImages[index] = img; // Simpan gambar yang telah dimuat
                        resolve();
                    };
                    img.onerror = reject; // Tangani kesalahan pemuatan gambar
                });
            }),
        );
    } catch (error) {
        console.error("Error loading logos:", error);
    }
};

// Plugin untuk menggambar logo
const imagePlugin = {
    id: "customImagePlugin",
    afterDraw: (chart) => {
        const { ctx, chartArea, scales } = chart;

        chart.data.labels.forEach((label, index) => {
            const x = scales.x.getPixelForTick(index);
            const y = chartArea.bottom; // Sesuaikan posisi y

            if (logoImages[index]) {
                const img = logoImages[index];
                const aspectRatio = img.width / img.height;
                const imgWidth = 30; // Lebar gambar
                const imgHeight = imgWidth / aspectRatio; // Tinggi gambar berdasarkan rasio aspek

                ctx.drawImage(img, x - imgWidth / 2, y, imgWidth, imgHeight); // Gambar logo
            }
        });
    },
};

document.addEventListener("DOMContentLoaded", async () => {
    const ctx = document.getElementById("total-team-chart").getContext("2d");

    await loadLogos(chartDataTotalTeam.logos);

    const chart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: chartDataTotalTeam.labels,
            datasets: chartDataTotalTeam.datasets,
        },
        options: {
            responsive: true,
            layout: {
                padding: {
                    bottom: 50,
                },
            },
            plugins: {
                legend: {
                    position: "top",
                },
                title: {
                    display: true,
                    text: "Total Tim Per Perusahaan",
                },
                datalabels: {
                    // Konfigurasi plugin Data Labels
                    display: true,
                    align: "end",
                    anchor: "end",
                    formatter: (value) => value.toLocaleString(), // Format angka (opsional)
                    font: {
                        weight: "bold",
                        size: 12,
                    },
                },
            },
            scales: {
                x: {
                    title: {
                        display: false,
                        text: "Perusahaan",
                    },
                    ticks: {
                        display: false,
                    },
                },
                y: {
                    title: {
                        display: true,
                        text: "Jumlah Tim",
                    },
                },
            },
        },
        plugins: [imagePlugin], // Daftarkan plugin untuk logo
    });
});
