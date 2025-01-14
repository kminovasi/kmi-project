import { Chart, registerables } from "chart.js";
import ChartDataLabels from "chartjs-plugin-datalabels";
import autocolors from 'chartjs-plugin-autocolors';

Chart.register(...registerables, ChartDataLabels);

export function initializeTotalInnovatorEventChart(chartData) {
    const ctx = document.getElementById("totalInnovatorEventChart").getContext("2d");

    const labels = Object.keys(chartData);
    const data = Object.values(chartData);

    new Chart(ctx, {
        plugins: [autocolors, ChartDataLabels],
        type: "bar",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Total Inovator",
                    data: data,
                    borderWidth: 1,
                    maxBarThickness: 40
                },
            ],
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: "top",
                    labels: {
                        color: "#000", // Warna hitam untuk teks legenda
                    },
                },
                autocolors: {
                    mode: 'data'
                },
                title: {
                    display: true,
                    text: "Total Inovator Berdasarkan Organisasi",
                    font: {
                        size: 16,
                        weight: "bold",
                    },
                },
                datalabels: {
                    display: true,
                    anchor: "center", // Menempatkan teks di tengah batang
                    align: "center",
                    formatter: (value) => value, // Menampilkan nilai
                    font: {
                        weight: "bold",
                        size: 20,
                    },
                    color: "#000", // Warna hitam untuk teks
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 2,
                        color: "#000", // Warna hitam untuk angka sumbu Y
                    },
                    title: {
                        display: true,
                        text: "Jumlah Inovator",
                        font: {
                            size: 14,
                            weight: "bold",
                        },
                        color: "#000",
                    },
                },
                x: {
                    ticks: {
                        color: "#000", // Warna hitam untuk teks sumbu X
                    },
                    title: {
                        display: true,
                        text: "Organisasi",
                        font: {
                            size: 14,
                            weight: "bold",
                        },
                        color: "#000",
                    },
                },
            },
        },
    });
}
