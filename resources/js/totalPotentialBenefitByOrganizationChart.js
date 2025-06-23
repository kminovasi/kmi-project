import { Chart, registerables } from "chart.js";
import ChartDataLabels from "chartjs-plugin-datalabels";

// Daftarkan semua elemen yang dibutuhkan
Chart.register(...registerables);

export function initializeTotalPotentialChart(chartData) {
    const labels = Object.keys(chartData);
    const datasets = [];

    const calculateFontSize = () => {
        const screenWidth = window.innerWidth;
        const baseFontSize = 10; // Default font size for large screens
        const minFontSize = 6; // Minimum font size
        const dataFactor = Math.max(labels.length / 10, 1); // Adjust font size based on data count

        let fontSize = baseFontSize / dataFactor;
        fontSize = Math.max(fontSize, minFontSize); // Ensure font size does not go below minimum

        if (screenWidth < 576) return Math.max(fontSize - 2, minFontSize); // Small screens
        if (screenWidth < 768) return Math.max(fontSize - 1, minFontSize); // Medium screens
        return fontSize; // Large screens
    };

    // Determine the range of years dynamically from the chartData
    const years = new Set();
    labels.forEach((unit) => {
        Object.keys(chartData[unit]).forEach((year) => {
            years.add(parseInt(year));
        });
    });
    const sortedYears = Array.from(years);

    if (sortedYears.length > 0) {
        const firstYear = sortedYears[0]; // Ambil tahun pertama dari daftar

        datasets.push({
            label: firstYear.toString(),
            data: labels.map((unit) => chartData[unit][firstYear] || 0),
            maxBarThickness: 60,
            backgroundColor: "#4e9000",
        });
    }
    const ctx = document.getElementById("totalPotentialChart").getContext("2d");
    new Chart(ctx, {
        plugins: [ChartDataLabels],
        type: "bar", // Tipe chart
        data: {
            labels: labels, // Nama unit
            datasets: datasets, // Data berdasarkan tahun
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: "top",
                },
                title: {
                    display: true,
                    text: "Total Benefit Potensial Keuangan Berdasarkan Organisasi",
                },
                datalabels: {
                    // Konfigurasi plugin Data Labels
                    display: true,
                    align: "top", // Jika kecil, posisikan di luar
                    anchor: "end",
                    color: "black", // Warna merah untuk angka kecil
                    formatter: (value) => formatRupiah(value.toLocaleString()), // Format angka
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
                        text: "Total Benefit Potensial Keuangan (IDR)",
                        font: {
                            size: 14,
                            weight: "bold",
                        },
                    },
                },
                x: {
                    title: {
                        display: true,
                        font: {
                            size: 14,
                            weight: "bold",
                        },
                    },
                    ticks: {
                        font: {
                            size: calculateFontSize(),
                        },
                    },
                },
            },
        },
    });
}

const formatRupiah = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);
};

window.initializeTotalPotentialChart = initializeTotalPotentialChart;