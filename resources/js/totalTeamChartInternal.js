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
    const ctx = document.getElementById("total-team-chart").getContext("2d");

    // Prepare the data from chartDataTotalTeam
    const labels = chartDataTotalTeam.datasets.map((dataset) => dataset.label);
    const data = chartDataTotalTeam.datasets.map((dataset) => dataset.data[0]);

    // Create the line chart
    new Chart(ctx, {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                {
                    label: chartDataTotalTeam.labels[0],
                    data: data,
                    borderColor: "#007bff", // Primary blue color
                    backgroundColor: "rgba(0, 123, 255, 0.2)", // Light blue background
                    tension: 0.1, // Slight curve to the line
                    borderWidth: 2,
                    pointBackgroundColor: chartDataTotalTeam.datasets.map(
                        (dataset) => dataset.backgroundColor
                    ),
                    pointBorderColor: chartDataTotalTeam.datasets.map(
                        (dataset) => dataset.backgroundColor
                    ),
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
                    text: "Total Team Over Years",
                },
                tooltip: {
                    mode: "index",
                    intersect: false,
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: "Number of Team Members",
                    },
                },
                x: {
                    title: {
                        display: true,
                        text: "Year",
                    },
                },
            },
        },
    });
});
