import { Chart, registerables } from "chart.js";
import ChartDataLabels from "chartjs-plugin-datalabels";

Chart.register(...registerables, ChartDataLabels);

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
                    label: "Total Inovator",
                    data: data,
                    backgroundColor: [
                         // Pastel Pink
                        '#FFDAB9', // Peach Puff
                        '#FFFACD', // Lemon Chiffon
                        '#E0FFFF', // Light Cyan
                        '#D8BFD8', // Thistle
                        '#B0E0E6', // Powder Blue
                        '#AFEEEE', // Pale Turquoise
                        '#F5DEB3', // Wheat
                        '#98FB98'  // Pale Green
                    ],
                    borderColor: "rgba(75, 192, 192, 1)",
                    borderWidth: 0,
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
