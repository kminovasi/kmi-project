import { Chart, registerables } from "chart.js";
import ChartDataLabels from "chartjs-plugin-datalabels";
import autocolors from "chartjs-plugin-autocolors";

// Register the custom plugin
Chart.register(...registerables, ChartDataLabels);

document.addEventListener("DOMContentLoaded", function () {
    const charts = document.querySelectorAll('canvas[id^="innovatorChart_"]');

    charts.forEach((canvas) => {
        const ctx = canvas.getContext("2d");
        const chartId = canvas.id;
        const chartData = window.chartData[chartId];

        if (chartData) {
            new Chart(ctx, {
                plugins: [autocolors],
                type: "bar",
                data: {
                    ...chartData,
                    datasets: chartData.datasets.map((dataset) => ({
                        ...dataset,
                        label: "Total Innovator per Kategori",
                        maxBarThickness: 40,
                    })),
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true, // Set to true to maintain aspect ratio
                    scales: {
                        y: {
                            beginAtZero: true,
                        },
                    },
                    plugins: {
                        autocolors: {
                            mode: "data",
                        },
                        datalabels: {
                            color: "black",
                            anchor: "center", // Center the label horizontally
                            align: "center", // Center the label vertically
                            font: {
                                weight: "bold",
                                size: 12,
                            },
                        },
                        legend: {
                            display: true,
                            labels: {
                                font: {
                                    size: 12,
                                    weight: "bold",
                                },
                            },
                        },
                    },
                },
            });
        }
    });
});
