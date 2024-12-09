import {
    Chart,
    CategoryScale,
    LinearScale,
    LineController,
    LineElement,
    Tooltip,
    Legend,
    PointElement,
} from "chart.js";
import ChartDataLabels from "chartjs-plugin-datalabels"; // Import plugin

// Registrasi komponen Chart.js
Chart.register(
    CategoryScale,
    LinearScale,
    LineController,
    LineElement,
    Tooltip,
    Legend,
    PointElement,
    ChartDataLabels
);

// Fungsi inisialisasi Chart.js
window.initializeChart = (canvasId, labels, data) => {
    const ctx = document.getElementById(canvasId).getContext("2d");
    new Chart(ctx, {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Financial Benefit",
                    data: data,
                    borderColor: "rgba(75, 192, 192, 1)",
                    borderWidth: 2,
                    fill: false,
                },
            ],
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                },
                datalabels: {
                    // Konfigurasi plugin Data Labels
                    display: true,
                    align: "end",
                    anchor: "end",
                    formatter: (value) => value.toLocaleString(), // Format angka (opsional)
                    font: {
                        weight: "bold",
                        size: 12,
                    },
                },
            },
        },
    });
};
