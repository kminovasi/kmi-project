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
import ChartDataLabels from "chartjs-plugin-datalabels"; // Import plugin

// Fungsi untuk memformat nilai ke dalam bentuk Rupiah
const formatRupiah = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);
};

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
    ChartDataLabels,
);

const logoImages = [];

// Memuat logo
const loadLogos = async (logos) => {
    try {
        await Promise.all(
            logos.map((url, index) => {
                return new Promise((resolve, reject) => {
                    const img = new Image();
                    img.src = url;
                    img.onload = () => {
                        logoImages[index] = img;
                        resolve();
                    };
                    img.onerror = reject;
                });
            }),
        );
    } catch (error) {
        console.error("Error loading logos:", error);
    }
};

// Plugin untuk menampilkan logo di chart
const imagePlugin = {
    id: "customImagePlugin",
    afterDraw: (chart) => {
        const { ctx, chartArea, scales } = chart;
        const yScale = scales.y;

        chart.data.labels.forEach((label, index) => {
            const y = yScale.getPixelForValue(index); // Posisi sesuai sorting terbaru
            const x = chartArea.left - 50; // Sesuaikan posisi ke kiri

            if (logoImages[index]) {
                const img = logoImages[index];

                // Calculate aspect ratio
                const aspectRatio = img.width / img.height;
                const imgWidth = 30; // Set your desired width
                const imgHeight = imgWidth / aspectRatio; // Calculate height based on aspect ratio

                ctx.drawImage(
                    img,
                    x,
                    y - imgHeight / 2, // Center the image vertically
                    imgWidth, // width
                    imgHeight, // height
                );
            }
        });
    },
};

// Tunggu logo dimuat sebelum membuat chart
document.addEventListener("DOMContentLoaded", async () => {
    const ctx = document.getElementById("total-benefit-chart").getContext("2d");

    await loadLogos(chartDataTotalBenefit.logos);
    const chartType = chartDataTotalBenefit.isSuperadmin ? "bar" : "line";

    // Sorting berdasarkan nilai terbesar ke terkecil
    let sortedData = chartDataTotalBenefit.labels
        .map((label, index) => ({
            label: label,
            logo: chartDataTotalBenefit.logos[index],
            value: chartDataTotalBenefit.datasets[0].data[index],
        }))
        .sort((a, b) => b.value - a.value);

    // Update chartData dengan data yang telah diurutkan
    chartDataTotalBenefit.labels = sortedData.map((item) => item.label);
    chartDataTotalBenefit.logos = sortedData.map((item) => item.logo);
    chartDataTotalBenefit.datasets[0].data = sortedData.map(
        (item) => item.value,
    );

    // Update logoImages agar sesuai dengan urutan baru
    logoImages.length = 0;
    await loadLogos(chartDataTotalBenefit.logos);

    new Chart(ctx, {
        type: chartType,
        data: {
            labels: chartDataTotalBenefit.labels,
            datasets: chartDataTotalBenefit.datasets,
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: "y", // Membuat chart horizontal
            layout: {
                padding: {
                    left: 50, // Tambahkan padding untuk logo
                },
            },
            plugins: {
                legend: {
                    position: "top",
                },
                title: {
                    display: true,
                    text: "Total Benefit Finansial (Tahun Ini)",
                },
                datalabels: {
                    display: true,
                    color: "black",
                    align: "end",
                    anchor: "center",
                    formatter: (value) => formatRupiah(value),
                    font: {
                        weight: "bold",
                        size: 12,
                    },
                },
                tooltip: {
                    callbacks: {
                        label: (tooltipItem) => {
                            return formatRupiah(tooltipItem.raw);
                        },
                    },
                },
            },
            scales: {
                y: {
                    ticks: {
                        display: chartDataTotalBenefit.isSuperadmin
                            ? false
                            : true,
                    },
                },
                x: {
                    title: {
                        display: true,
                        text: "Benefit Finansial",
                    },
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return formatRupiah(value);
                        },
                    },
                },
            },
        },
        plugins: [imagePlugin],
    });
});

document.addEventListener("DOMContentLoaded", async () => {
    const ctx = document
        .getElementById("total-potential-benefit-chart")
        .getContext("2d");
    const chartType = chartDataTotalPotentialBenefit.isSuperadmin
        ? "bar"
        : "line";

    // Sorting berdasarkan total nilai terbesar ke terkecil
    let sortedData = chartDataTotalPotentialBenefit.labels
        .map((label, index) => ({
            label: label,
            logo: chartDataTotalPotentialBenefit.logos[index],
            values: chartDataTotalPotentialBenefit.datasets.map(
                (dataset) => dataset.data[index],
            ),
        }))
        .sort(
            (a, b) =>
                b.values.reduce((sum, v) => sum + v, 0) -
                a.values.reduce((sum, v) => sum + v, 0),
        );

    // Update chartDataTotalPotentialBenefit dengan data yang telah diurutkan
    chartDataTotalPotentialBenefit.labels = sortedData.map(
        (item) => item.label,
    );
    chartDataTotalPotentialBenefit.logos = sortedData.map((item) => item.logo);

    // Update setiap dataset dengan urutan yang benar
    chartDataTotalPotentialBenefit.datasets.forEach((dataset, i) => {
        dataset.data = sortedData.map((item) => item.values[i]);
    });

    // Update logoImages agar sesuai dengan urutan baru
    logoImages.length = 0;
    await loadLogos(chartDataTotalPotentialBenefit.logos);

    new Chart(ctx, {
        type: chartType,
        data: {
            labels: chartDataTotalPotentialBenefit.labels,
            datasets: chartDataTotalPotentialBenefit.datasets,
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: chartDataTotalPotentialBenefit.isSuperadmin ? "y" : "x",
            layout: {
                padding: {
                    left: 50, // Tambahkan padding kanan untuk logo
                },
            },
            plugins: {
                legend: {
                    position: "top",
                },
                title: {
                    display: true,
                    text: "Total Potential Benefit (Tahun Ini)",
                },
                datalabels: {
                    display: true,
                    color: "black",
                    align: "end",
                    anchor: "center",
                    formatter: (value) => formatRupiah(value),
                    font: {
                        weight: "bold",
                        size: 12,
                    },
                },
                tooltip: {
                    callbacks: {
                        label: (tooltipItem) => {
                            return formatRupiah(tooltipItem.raw);
                        },
                    },
                },
            },
            scales: {
                y: {
                    ticks: {
                        display: chartDataTotalPotentialBenefit.isSuperadmin
                            ? false
                            : true,
                    },
                },
                x: {
                    title: {
                        display: true,
                        text: "Potential Benefit",
                    },
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return formatRupiah(value);
                        },
                    },
                },
            },
        },
        plugins: [imagePlugin],
    });
});
