import { Chart, registerables } from "chart.js";

Chart.register(...registerables);

export function initializeTotalInnovatorEventChart(chartData) {
    const ctx = document.getElementById("totalInnovatorEventChart").getContext("2d");

    const labels = Object.keys(chartData);
    const data = Object.values(chartData);

    new Chart(ctx, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Total Innovators",
                    data: data,
                    backgroundColor: "rgba(75, 192, 192, 0.6)",
                    borderColor: "rgba(75, 192, 192, 1)",
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: "top",
                },
                title: {
                    display: true,
                    text: "Total Innovators by Organization",
                },
                datalabels: {
                    display: true,
                    anchor: "end",
                    align: "top",
                    formatter: (value) => value,
                    font: {
                        weight: "bold",
                        size: 12,
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                    },
                },
            },
        },
    });
}
