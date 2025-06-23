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
import autocolors from "chartjs-plugin-autocolors";

// Import chartjs-plugin-datalabels untuk menampilkan angka di dalam chart
import ChartDataLabels from "chartjs-plugin-datalabels";

Chart.register(
    CategoryScale,
    LinearScale,
    BarController,
    BarElement,
    Tooltip,
    Legend,
    Title,
    autocolors
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
            })
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

        // Menggambar logo di bawah setiap label
        chart.data.labels.forEach((label, index) => {
            const x = chartArea.left + 10;
            const y = scales.y.getPixelForValue(index) - 15; // Posisi Y, tambahkan jarak di bawah label

            if (logoImages[index]) {
                const img = logoImages[index];
                const aspectRatio = img.width / img.height;
                const imgWidth = 50; // Lebar gambar
                const imgHeight = imgWidth / aspectRatio; // Tinggi gambar berdasarkan rasio aspek

                ctx.drawImage(img, x - imgWidth / 7, y, imgWidth, imgHeight); // Gambar logo
            }
        });
    },
};

export async function renderTotalBenefitChart(companies) {
    const ctx = document.getElementById("totalBenefitChart").getContext("2d");

    const labels = companies.map((item) => item.company_name);
    const data = companies.map((item) => item.total_benefit);
    const logos = companies.map((item) => item.logo);

    // Memuat logo sebelum membuat grafik
    await loadLogos(logos);

    // Render Chart.js Bar Chart
    new Chart(ctx, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Total Financial Benefit",
                    data: data,
                    borderWidth: 1,
                    maxBarThickness: 40
                },
            ],
        },
        options: {
            indexAxis: "y", // Horizontal Bar
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const value = context.raw.toLocaleString("id-ID");
                            return `Benefit: Rp ${value}`;
                        },
                    },
                },
                autocolors: {
                    mode: 'data'
                },
                // Plugin untuk menampilkan angka di tengah batang chart
                datalabels: {
                    display: true,
                    anchor: "center", // Posisi angka di tengah batang
                    align: "center", // Penyesuaian posisi angka
                    formatter: (value) =>
                        formatRupiah(value), // Format angka
                    font: {
                        weight: "bold", // Tebalkan font
                        size: 14, // Ukuran font
                    },
                    color: "#000", // Warna hitam untuk angka
                },
            },
            responsive: true,
            scales: {
                x: {
                    beginAtZero: true,
                    title: {
                        display: false,
                        text: "Perusahaan",
                    },
                    ticks: {
                        display: false, // Sembunyikan label pada sumbu X
                    },
                },
                y: {
                    ticks: {
                        display: false, // Sembunyikan label pada sumbu Y
                        autoSkip: false, // Pastikan semua label ditampilkan
                    },
                },
            },
        },
        plugins: [imagePlugin, ChartDataLabels], // Daftarkan plugin untuk menggambar logo
    });
}


const formatRupiah = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);
};

window.renderTotalBenefitChart = renderTotalBenefitChart;