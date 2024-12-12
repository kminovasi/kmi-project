import {
    Chart,
    LineController,
    LineElement,
    PointElement,
    LinearScale,
    Title,
    Tooltip,
    Legend,
    CategoryScale,
} from "chart.js";
import ChartDataLabels from "chartjs-plugin-datalabels"; // Import plugin

// Register the necessary chart components
Chart.register(
    LineController,
    LineElement,
    PointElement,
    LinearScale,
    Title,
    Tooltip,
    Legend,
    CategoryScale,
    ChartDataLabels
);

// Wait for the DOM to be fully loaded
document.addEventListener("DOMContentLoaded", () => {
    // Get the canvas element
    const ctx = document
        .getElementById("total-innovator-chart")
        .getContext("2d");

    // Transform the chartData structure for Line Chart
    const labels = chartData.datasets.map((dataset) => dataset.label); // Years (x-axis)
    const data = chartData.datasets.map((dataset) => dataset.data[0]); // Data (y-axis)

    // Create the line chart
    new Chart(ctx, {
        type: "line",
        data: {
            labels: labels, // Years
            datasets: [
                {
                    label: chartData.labels[0], // Company name
                    data: data, // Innovator data
                    borderColor: "#007bff", // Primary blue color
                    backgroundColor: "rgba(0, 123, 255, 0.2)", // Light blue background
                    tension: 0.1, // Slight curve to the line
                    borderWidth: 2,
                    pointBackgroundColor: chartData.datasets.map(
                        (dataset) => dataset.backgroundColor
                    ), // Point colors
                    pointBorderColor: chartData.datasets.map(
                        (dataset) => dataset.backgroundColor
                    ), // Border colors for points
                    pointRadius: 5,
                    pointHoverRadius: 7,
                },
            ],
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: "Jumlah Keterlibatan Inovator",
                },
                tooltip: {
                    mode: "index",
                    intersect: false,
                },
                datalabels: {
                    display: true,
                    align: "top",
                    anchor: "end",
                    formatter: (value) => value.toLocaleString(), // Format angka
                    font: {
                        weight: "bold",
                        size: 12,
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: "Jumlah Inovator",
                    },
                },
                x: {
                    title: {
                        display: true,
                        text: "Tahun",
                    },
                },
            },
        },
    });
});
