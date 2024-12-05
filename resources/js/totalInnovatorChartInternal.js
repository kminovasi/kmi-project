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

// Register the necessary chart components
Chart.register(
    LineController,
    LineElement,
    PointElement,
    LinearScale,
    Title,
    Tooltip,
    Legend,
    CategoryScale
);

// Wait for the DOM to be fully loaded
document.addEventListener("DOMContentLoaded", () => {
    // Get the canvas element
    const ctx = document
        .getElementById("total-innovator-chart")
        .getContext("2d");

    // Transform data into a line-compatible format
    const transformedDatasets = chartData.datasets.map((dataset) => ({
        label: dataset.label,
        data: dataset.data,
        borderColor: dataset.backgroundColor, // Use the background color as the border color
        backgroundColor: dataset.backgroundColor, // Set the background color
        fill: false, // Line chart without fill under the curve
        tension: 0.1, // Slight curve to the line
        borderWidth: 2,
        pointRadius: 5,
    }));

    // Create the chart
    new Chart(ctx, {
        type: "line", // Ensure this is 'line' for a line chart
        data: {
            labels: chartData.datasets.map((dataset) => dataset.label), // Use labels from the dataset
            datasets: transformedDatasets,
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
                legend: {
                    display: true,
                    position: "top",
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
