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

// Fungsi untuk memformat nilai ke dalam bentuk Rupiah
const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value);
};

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
                tooltip: {
                    callbacks: {
                        label: (tooltipItem) => {
                            return formatRupiah(tooltipItem.raw);
                        }
                    }
                },
                datalabels: {
                    formatter: (value) => {
                        return formatRupiah(value);
                    },
                    color: 'black',
                    anchor: 'center', // Center the label horizontally
                    align: 'center', // Center the label vertically
                }
            },
            scales: {
                y: {
                    ticks: {
                        callback: function(value) {
                            return formatRupiah(value);
                        }
                    }
                }
            }
        }
    });
};
