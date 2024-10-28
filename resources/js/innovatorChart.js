import { Chart, registerables } from "chart.js";
Chart.register(...registerables);

document.addEventListener("DOMContentLoaded", function () {
    const charts = document.querySelectorAll('canvas[id^="innovatorChart_"]');

    charts.forEach((canvas) => {
        const ctx = canvas.getContext("2d");
        const chartId = canvas.id;
        const chartData = window.chartData[chartId];

        if (chartData) {
            new Chart(ctx, {
                type: "bar",
                data: chartData,
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                        },
                    },
                },
            });
        }
    });
});
