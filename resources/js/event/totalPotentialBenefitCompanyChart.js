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

Chart.register(
    CategoryScale,
    LinearScale,
    BarController,
    BarElement,
    Tooltip,
    Legend,
    Title
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
            const x = scales.x.getPixelForTick(index); // Posisi X label
            const y = scales.y.getPixelForValue(index) + 10; // Posisi Y, tambahkan jarak di bawah label

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

let totalBenefitChartInstance = null; // Simpan referensi instance chart

export async function renderTotalBenefitChart(companies) {
    const ctx = document.getElementById("totalPotentialBenefitChart").getContext("2d");

    const labels = companies.map((item) => item.company_name);
    const data = companies.map((item) => item.total_benefit);
    const logos = companies.map((item) => item.logo);

    // Memuat logo sebelum membuat grafik
    await loadLogos(logos);

    // Jika chart sudah ada, destroy terlebih dahulu
    if (totalBenefitChartInstance) {
        totalBenefitChartInstance.destroy();
    }

    // Render Chart.js Bar Chart
    totalBenefitChartInstance = new Chart(ctx, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Total Potential Benefit",
                    data: data,
                    backgroundColor: "rgba(54, 162, 235, 0.5)",
                    borderColor: "rgba(54, 162, 235, 1)",
                    borderWidth: 1,
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
        plugins: [imagePlugin], // Daftarkan plugin untuk menggambar logo
    });
}

