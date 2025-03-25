import { Chart, registerables } from "chart.js";
import ChartDataLabels from "chartjs-plugin-datalabels";

// Daftarkan semua elemen yang dibutuhkan
Chart.register(...registerables);

export function initializeTotalTeamChart(chartData) {
    const labels = Object.keys(chartData);
    const datasets = [];

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
            backgroundColor: "#38507a",
        });
    }

    const ctx = document.getElementById("totalTeamChart").getContext("2d");
    const calculateFontSize = () => {
        const screenWidth = window.innerWidth;
        const baseFontSize = 10; // Default font size for large screens
        const minFontSize = 1; // Minimum font size
        const dataFactor = Math.max(labels.length / 10, 1); // Adjust font size based on data count

        let fontSize = baseFontSize / dataFactor;
        fontSize = Math.max(fontSize, minFontSize); // Ensure font size does not go below minimum

        if (screenWidth < 576) return Math.max(fontSize - 2, minFontSize); // Small screens
        if (screenWidth < 768) return Math.max(fontSize - 1, minFontSize); // Medium screens
        return fontSize; // Large screens
    };
    new Chart(ctx, {
        type: "bar", // Tipe chart
        plugins: [ChartDataLabels],
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
                autocolorPlugin: {
                    mode: "data",
                },
                title: {
                    display: true,
                    text: "Distribusi Ide dan Inovasi",
                },
                datalabels: {
                    // Konfigurasi plugin Data Labels
                    display: true,
                    align: "center",
                    anchor: "center",
                    color: "white",
                    formatter: (value) => value.toLocaleString(), // Format angka (opsional)
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
                        maxRotation: 0, // Rotate labels if needed
                        minRotation: 0,
                    },
                },
            },
        },
    });
}
