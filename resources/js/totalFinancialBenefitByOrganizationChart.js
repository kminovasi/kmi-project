import { Chart, registerables } from "chart.js";

// Daftarkan semua elemen yang dibutuhkan
Chart.register(...registerables);

export function initializeTotalFinancialChart(chartData) {
    const labels = Object.keys(chartData);
    const datasets = [];

    const currentYear = new Date().getFullYear();
    for (let year = currentYear - 3; year <= currentYear; year++) {
        datasets.push({
            label: year.toString(),
            data: labels.map((unit) => chartData[unit][year] || 0), // Data tahun tertentu
            backgroundColor: `rgba(${Math.random() * 255}, ${
                Math.random() * 255
            }, ${Math.random() * 255}, 0.6)`,
        });
    }

    const ctx = document.getElementById("totalFinancialChart").getContext("2d");
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
                    text: "Total Financial Benefit by Organization",
                },
            },
        },
    });
}
