import { Chart, registerables } from "chart.js";
import ChartDataLabels from "chartjs-plugin-datalabels"; // Import plugin

// Daftarkan semua elemen yang dibutuhkan
Chart.register(...registerables, ChartDataLabels);

export function initializeTotalTeamChart(chartData) {
    const labels = Object.keys(chartData);
    const datasets = [];

    // Determine the range of years dynamically from the chartData
    const years = new Set();
    labels.forEach(unit => {
        Object.keys(chartData[unit]).forEach(year => {
            years.add(parseInt(year));
        });
    });
    const sortedYears = Array.from(years).sort();

    sortedYears.forEach(year => {
        datasets.push({
            label: year.toString(),
            data: labels.map((unit) => chartData[unit][year] || 0), // Data tahun tertentu
            backgroundColor: `rgba(${Math.random() * 255}, ${
                Math.random() * 255
            }, ${Math.random() * 255}, 0.6)`,
        });
    });

    const ctx = document.getElementById("totalTeamChart").getContext("2d");
    new Chart(ctx, {
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
                    text: "Distribusi Ide dan Inovasi",
                },
                datalabels: {
                    // Konfigurasi plugin Data Labels
                    display: true,
                    align: "center",
                    anchor: "center",
                    color: 'black',
                    formatter: (value) => value.toLocaleString(), // Format angka (opsional)
                    font: {
                        weight: "bold",
                        size: 12,
                    },
                },
            },
        },
    });
}
