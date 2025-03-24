import {
    Chart,
    CategoryScale,
    LinearScale,
    BarController,
    BarElement,
    Tooltip,
    Legend,
    Title,
} from "chart.js";
import ChartDataLabels from "chartjs-plugin-datalabels"; // Import plugin

Chart.register(
    CategoryScale,
    LinearScale,
    BarController,
    BarElement,
    Tooltip,
    Legend,
    Title,
    ChartDataLabels,
);

// Array untuk menyimpan gambar logo
let logoImages = [];

// Fungsi untuk memuat semua gambar logo
const loadLogos = async (logos) => {
    logoImages = []; // Kosongkan array sebelum memuat ulang

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

// Plugin untuk menggambar logo di bawah label
const imagePlugin = {
    id: "customImagePlugin",
    afterDraw: (chart) => {
        const { ctx, chartArea, scales } = chart;

        chart.data.labels.forEach((label, index) => {
            const x = scales.x.getPixelForTick(index); // Posisi X label
            const y = chartArea.bottom + 10; // Posisi Y, tambahkan jarak di bawah chart

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
    const ctx = document
        .getElementById("total-innovator-chart")
        .getContext("2d");

    // **Pastikan data tidak kosong**
    if (!chartData.datasets || chartData.datasets.length === 0) {
        console.error("Dataset kosong!");
        return;
    }

    // **Ambil data dari tahun terbaru**
    const latestYearIndex = chartData.datasets.length - 1; // Tahun terbaru ada di index terakhir
    const latestYearData = chartData.datasets[latestYearIndex].data;

    if (!latestYearData || latestYearData.length === 0) {
        console.error("Data tahun terbaru kosong!");
        return;
    }

    // **Gabungkan data untuk sorting**
    let combinedData = chartData.labels.map((label, index) => ({
        label,
        logo: chartData.logos[index],
        value: latestYearData[index], // Ambil jumlah inovator dari tahun terbaru
        datasetValues: chartData.datasets.map((dataset) => dataset.data[index]), // Simpan seluruh nilai dataset per perusahaan
    }));

    // **Urutkan berdasarkan jumlah inovator terbesar**
    combinedData.sort((a, b) => b.value - a.value);

    // **Buat ulang chartData berdasarkan urutan baru**
    chartData.labels = combinedData.map((item) => item.label);
    chartData.logos = combinedData.map((item) => item.logo);

    // **Update data di semua dataset dengan urutan baru**
    chartData.datasets.forEach((dataset, datasetIndex) => {
        dataset.data = combinedData.map(
            (item) => item.datasetValues[datasetIndex],
        );
    });

    console.log("Data setelah sorting:", chartData);

    // **Muat ulang logo berdasarkan urutan baru**
    await loadLogos(chartData.logos);

    // **Buat chart setelah sorting**
    const chart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: chartData.labels,
            datasets: chartData.datasets,
        },
        options: {
            responsive: true,
            layout: {
                padding: { bottom: 50 },
            },
            plugins: {
                legend: {
                    display: true,
                    position: "top",
                    labels: {
                        font: { size: 12 },
                        boxWidth: 20,
                        padding: 15,
                    },
                },
                title: {
                    display: true,
                    text: "Jumlah Keterlibatan Inovator per Perusahaan",
                },
                datalabels: {
                    display: true,
                    align: "end",
                    anchor: "end",
                    formatter: (value) => value.toLocaleString(),
                    font: { weight: "bold", size: 12 },
                },
            },
            scales: {
                x: {
                    title: { display: false, text: "Perusahaan" },
                    ticks: { display: false }, // Sembunyikan teks label agar hanya logo yang tampil
                },
                y: {
                    title: { display: true, text: "Jumlah Inovator" },
                },
            },
        },
        plugins: [imagePlugin], // Daftarkan plugin untuk menggambar logo
    });
});
