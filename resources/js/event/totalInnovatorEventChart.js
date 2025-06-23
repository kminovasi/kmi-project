import { Chart, registerables } from "chart.js";
import ChartDataLabels from "chartjs-plugin-datalabels";
import autocolors from 'chartjs-plugin-autocolors';

Chart.register(...registerables, ChartDataLabels);

// Fungsi untuk memetakan organizationUnit ke format yang lebih deskriptif
function formatOrganizationUnit(unit) {
    const mapping = {
        directorate_name: "Direktorat",
        group_function_name: "Group Head",
        department_name: "Departemen",
        unit_name: "Unit",
        section_name: "Seksi",
        sub_section_of: "Sub Seksi",
    };
    return mapping[unit] || unit; // Kembalikan unit asli jika tidak ditemukan
}

export function initializeTotalInnovatorEventChart(chartData, canvasId, organizationUnit) {
    const ctx = document.getElementById(canvasId).getContext("2d");

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
                    text: `Total Inovator Berdasarkan ${ organizationUnit !== null ? formatOrganizationUnit(organizationUnit) : 'Direktorat' }`,
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

window.initializeTotalInnovatorEventChart = initializeTotalInnovatorEventChart;