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
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
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
    ChartDataLabels
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
            })
        );
    } catch (error) {
        console.error("Error loading logos:", error);
    }
};

const imagePlugin = {
    id: "customImagePlugin",
    afterDraw: (chart) => {
        const { ctx, chartArea, scales } = chart;

        chart.data.labels.forEach((label, index) => {
            const yScale = scales.y;

            // Calculate position for the image
            const y = yScale.getPixelForValue(index);
            const x = chartArea.left - 40; // Adjust this value to position the image

            // Draw the image if it exists in logoImages
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
                    imgHeight // height
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

    new Chart(ctx, {
        type: chartType,
        data: {
            labels: chartDataTotalBenefit.labels,
            datasets: chartDataTotalBenefit.datasets,
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: chartDataTotalBenefit.isSuperadmin ? "y" : "x", // Ini adalah kunci untuk membuat chart horizontal
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
                    text: "Total Financial Benefit (Last 4 Years)",
                },
                datalabels: {
                    // Konfigurasi plugin Data Labels
                    display: true,
                    color: 'black',
                    align: "center", // Center the label vertically
                    anchor: "center", // Center the label horizontally
                    formatter: (value) => formatRupiah(value), // Format angka ke dalam Rupiah
                    font: {
                        weight: "bold",
                        size: 17,
                    },
                },
                tooltip: {
                    callbacks: {
                        label: (tooltipItem) => {
                            return formatRupiah(tooltipItem.raw);
                        }
                    }
                }
            },
            scales: {
                y: {
                    // Sekarang ini adalah sumbu x
                    ticks: {
                        display: chartDataTotalBenefit.isSuperadmin
                            ? false
                            : true,
                    },
                },
                x: {
                    // Sekarang ini adalah sumbu y
                    title: {
                        display: true,
                        text: "Finansial Benefit",
                    },
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return formatRupiah(value);
                        }
                    }
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
    const chartType = chartDataTotalBenefit.isSuperadmin ? "bar" : "line";

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
            indexAxis: chartDataTotalBenefit.isSuperadmin ? "y" : "x",
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
                    text: "Total Financial Benefit (Last 4 Years)",
                },
                datalabels: {
                    // Konfigurasi plugin Data Labels
                    display: true,
                    align: "center", // Center the label vertically
                    anchor: "center", // Center the label horizontally
                    formatter: (value) => formatRupiah(value), // Format angka ke dalam Rupiah
                    font: {
                        weight: "bold",
                        size: 17,
                    },
                },
                tooltip: {
                    callbacks: {
                        label: (tooltipItem) => {
                            return formatRupiah(tooltipItem.raw);
                        }
                    }
                }
            },
            scales: {
                y: {
                    // Sekarang ini adalah sumbu x
                    ticks: {
                        display: chartDataTotalBenefit.isSuperadmin
                            ? false
                            : true,
                    },
                },
                x: {
                    // Sekarang ini adalah sumbu y
                    title: {
                        display: true,
                        text: "Potensial Benefit",
                    },
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return formatRupiah(value);
                        }
                    }
                },
            },
        },
        plugins: [imagePlugin],
    });
});
