import { Chart, registerables } from "chart.js";
import autocolorPlugin from "chartjs-plugin-autocolors";
import chartDataLabel from 'chartjs-plugin-datalabels'

// Register required elements
Chart.register(...registerables);

export function initializeTotalInnovatorChart(chartData) {
    const labels = Object.keys(chartData);
    const datasets = [];

    const currentYear = new Date().getFullYear();
    for (let year = currentYear - 3; year <= currentYear; year++) {
        datasets.push({
            label: year.toString(),
            data: labels.map((unit) => chartData[unit][year] || 0),
            maxBarThickness: 40, // Maximum bar thickness
        });
    }

    const ctx = document.getElementById("totalInnovatorChart").getContext("2d");

    const calculateFontSize = () => {
        const screenWidth = window.innerWidth;
        const baseFontSize = 10; // Default font size for large screens
        const minFontSize = 8;  // Minimum font size
        const dataFactor = Math.max(labels.length / 10, 1); // Adjust font size based on data count

        let fontSize = baseFontSize / dataFactor;
        fontSize = Math.max(fontSize, minFontSize); // Ensure font size does not go below minimum

        if (screenWidth < 576) return Math.max(fontSize - 2, minFontSize); // Small screens
        if (screenWidth < 768) return Math.max(fontSize - 1, minFontSize); // Medium screens
        return fontSize; // Large screens
    };

    const chartConfig = {
        type: "bar",
        plugins: [autocolorPlugin, chartDataLabel],
        data: {
            labels: labels,
            datasets: datasets,
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: "top",
                    labels: {
                        font: {
                            size: 14,
                        },
                    },
                },
                autocolorPlugin: {
                    mode: 'data',
                },
                title: {
                    display: true,
                    text: "Total Innovators",
                    font: {
                        size:14
                    },
                },
                datalabels: {
                    display: true,
                    align: "center",
                    anchor: "center",
                    color: "black",
                    formatter: (value) => value.toLocaleString(),
                    font: {
                        weight: "bold",
                        size: 14,
                    },
                },
            },
            scales: {
                x: {
                    ticks: {
                        font: {
                            size: calculateFontSize(), // Dynamic font size for x-axis labels
                        },
                        maxRotation: 45, // Rotate labels if needed
                        minRotation: 0,
                    },
                },
                y: {
                    beginAtZero: true, // Ensure y-axis starts at 0
                },
            },
        },
    };

    const chart = new Chart(ctx, chartConfig);

    // Update font size dynamically on window resize
    window.addEventListener("resize", () => {
        const newFontSize = calculateFontSize();
        chart.options.plugins.legend.labels.font.size = newFontSize;
        chart.options.plugins.title.font.size = newFontSize + 4;
        chart.options.plugins.datalabels.font.size = newFontSize;
        chart.options.scales.x.ticks.font.size = newFontSize; // Update x-axis label font size
        chart.update();
    });
}
