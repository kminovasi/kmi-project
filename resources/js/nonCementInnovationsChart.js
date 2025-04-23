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
    const ctx = document.getElementById("non-cement-innovation-chart")?.getContext("2d");
    const chartData = window.nonCementInnovationChartData;

    if (!ctx || !chartData) {
        console.error("Canvas atau data tidak ditemukan.");
        return;
    }

    const { labels, implemented, idea_box, logos } = chartData;
    const logoImages = [];

    // Load images
    Promise.all(
        logos.map((url, i) => {
            return new Promise((resolve) => {
                const img = new Image();
                img.crossOrigin = "anonymous"; // safe load
                img.onload = () => {
                    logoImages[i] = img;
                    resolve();
                };
                img.onerror = (e) => {
                    console.warn(`Gagal load logo ke-${i}`, url);
                    resolve();
                };
                img.src = url;
            });
        })
    ).then(() => {
        const imagePlugin = {
            id: "customImagePlugin",
            afterDraw(chart) {
                const { ctx, chartArea, scales } = chart;
                const xScale = scales.x;

                chart.data.labels.forEach((_, i) => {
                    const img = logoImages[i];
                    if (!img) return;

                    const x = xScale.getPixelForValue(i);
                    const y = chartArea.bottom;
                    const aspectRatio = img.width / img.height;
                    const imgWidth = 40; // Lebar gambar
                    const imgHeight = imgWidth / aspectRatio; // Tinggi gambar berdasarkan rasio aspek

                    ctx.drawImage(img, x - imgWidth / 2, y, imgWidth, imgHeight); // Gambar logo
                });
            }
        };

        new Chart(ctx, {
            type: "bar",
            data: {
                labels: labels,
                datasets: [
                    {
                        label: "Implemented",
                        data: implemented,
                        backgroundColor: "#67a9cf"
                    },
                    {
                        label: "Idea Box",
                        data: idea_box,
                        backgroundColor: "#ef8a62"
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        bottom: 50
                    }
                },
                plugins: {
                    legend: {
                        position: "top"
                    },
                    tooltip: {
                        callbacks: {
                            title: (tooltipItems) => {
                                return labels[tooltipItems[0].dataIndex];
                            }
                        }
                    },
                    datalabels: {
                        anchor: "center",
                        align: "center",
                        font: {
                            size: 14,
                            weight: "bold"
                        },
                        color: "#000"
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            display: false
                        },
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: "Jumlah Inovasi"
                        }
                    }
                }
            },
            plugins: [ChartDataLabels, imagePlugin]
        });
    });
});
