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

    // Memuat logo sebelum membuat grafik
    await loadLogos(chartData.logos);

    // Membuat grafik
    const chart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: chartData.labels, // Nama perusahaan
            datasets: chartData.datasets, // Data inovator (berisi label tahun dan warna batang)
        },
        options: {
            responsive: true,
            layout: {
                padding: {
                    bottom: 50, // Tambahkan padding bawah untuk ruang logo
                },
            },
            plugins: {
                legend: {
                    display: true, // Aktifkan legend untuk warna tahun
                    position: "top", // Legend di atas chart
                    labels: {
                        font: {
                            size: 12, // Ukuran font legend
                        },
                        boxWidth: 20, // Lebar kotak warna di legend
                        padding: 15, // Jarak antar item legend
                    },
                },
                title: {
                    display: true,
                    text: "Jumlah Keterlibatan Inovator per Perusahaan (2020–2023)",
                },
            },
            scales: {
                x: {
                    title: {
                        display: false,
                        text: "Perusahaan",
                    },
                    ticks: {
                        display: false, // Sembunyikan teks label agar hanya logo yang tampil
                    },
                },
                y: {
                    title: {
                        display: true,
                        text: "Jumlah Inovator",
                    },
                },
            },
        },
        plugins: [imagePlugin], // Daftarkan plugin untuk menggambar logo
    });
});
