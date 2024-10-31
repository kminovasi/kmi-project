import { Chart, registerables } from "chart.js";
const drawValuePlugin = {
    id: "drawValue",
    afterDatasetsDraw: (chart, args, options) => {
        const ctx = chart.ctx;
        chart.data.datasets.forEach((dataset, datasetIndex) => {
            const meta = chart.getDatasetMeta(datasetIndex);
            if (!meta.hidden) {
                meta.data.forEach((element, index) => {
                    const value = dataset.data[index];
                    const position = element.tooltipPosition();

                    ctx.fillStyle = "black";
                    ctx.font = "12px Arial";
                    ctx.textAlign = "center";
                    ctx.textBaseline = "middle";
                    ctx.fillText(value, position.x, position.y);
                });
            }
        });
    },
};

// Register the custom plugin
Chart.register(...registerables, drawValuePlugin);

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
                    plugins: {
                        drawValue: true,
                    },
                },
            });
        }
    });
});
