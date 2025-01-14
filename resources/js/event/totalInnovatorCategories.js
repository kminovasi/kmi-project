import Chart from 'chart.js/auto';
import ChartDataLabels from 'chartjs-plugin-datalabels';
import autocolors from 'chartjs-plugin-autocolors';

// /**
//  * Render Total Innovator Chart
//  * @param {string} canvasId - ID dari elemen canvas
//  * @param {object} chartData - Data chart dari server
//  */


export function renderTotalInnovatorChart(canvasId, chartData) {
    const ctx = document.getElementById(canvasId).getContext('2d');

    new Chart(ctx, {

        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Total Inovator per Kategori', // Label dalam bahasa Indonesia
                data: chartData.data,
                borderWidth: 1,
                maxBarThickness: 40,

            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                autocolors: {
                    mode: 'data',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: ${context.raw}`;
                        }
                    }
                },
                datalabels: {
                    anchor: 'center', // Menempatkan teks di tengah batang
                    align: 'center', // Memastikan teks berada di dalam batang
                    formatter: function(value) {
                        return value; // Menampilkan nilai pada batang
                    },
                    font: {
                        size: 12, // Ukuran teks
                        weight: 'bold' // Teks bold
                    },
                    color: '#000' // Warna teks hitam
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        },
        plugins: [ChartDataLabels, autocolors] // Aktifkan plugin DataLabels
    });
}


