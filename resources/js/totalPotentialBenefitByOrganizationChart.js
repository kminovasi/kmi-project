import { Chart, registerables } from "chart.js";
import ChartDataLabels from "chartjs-plugin-datalabels";

// Daftarkan semua elemen yang dibutuhkan
Chart.register(...registerables, ChartDataLabels);

export function initializeTotalPotentialChart(chartData) {
    const labels = Object.keys(chartData);
    const datasets = [];

    const currentYear = new Date().getFullYear();
    for (let year = currentYear - 3; year <= currentYear; year++) {
        datasets.push({
            label: year.toString(),
            data: labels.map((unit) => chartData[unit][year] || 0), // Data tahun tertentu
            backgroundColor: `rgba(${Math.random() * 255}, ${Math.random() * 255}, ${Math.random() * 255}, 0.6)`,
        });
    }

    const ctx = document.getElementById("totalPotentialChart").getContext("2d");
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
                    text: "Total Benefit Potensial Keuangan Berdasarkan Organisasi",
                },
                datalabels: {
                    // Konfigurasi plugin Data Labels
                    display: true,
                    align: (context) =>
                        context.dataset.data[context.dataIndex] < 10
                            ? "end"
                            : "center", // Jika kecil, posisikan di luar
                    anchor: (context) =>
                        context.dataset.data[context.dataIndex] < 10
                            ? "end"
                            : "center",
                    color: (context) =>
                        context.dataset.data[context.dataIndex] < 10
                            ? "red"
                            : "black", // Warna merah untuk angka kecil
                    formatter: (value) =>
                        formatRupiah(value.toLocaleString()), // Format angka
                    font: {
                        weight: "bold",
                        size: 12,
                    },
                    padding: 4, // Tambahkan padding agar teks tidak menempel
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
                        text: "Unit Organisasi",
                        font: {
                            size: 14,
                            weight: "bold",
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
