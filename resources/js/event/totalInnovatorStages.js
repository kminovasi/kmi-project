import Chart from 'chart.js/auto';
import autocolors from 'chartjs-plugin-autocolors';
import ChartDataLabels from "chartjs-plugin-datalabels";

export function renderTotalInnovatorStagesChart(canvasId, chartData) {
    const ctx = document.getElementById(canvasId).getContext('2d');

    new Chart(ctx, {
        plugins: [autocolors, ChartDataLabels],
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Jumlah Team',
                data: chartData.data,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `Jumlah: ${context.raw}`;
                        }
                    }
                },
                autocolors: {
                    mode: 'data'
                },
                datalabels: {
                    color: '#000',
                    font: {
                        weight: 'bold', // Membuat angka di dalam chart menjadi bold
                        size: 14 // Ukuran font angka
                    },
                    anchor: 'center', // Tempatkan angka di bagian atas batang
                    align: 'center' // Penempatan angka di dalam batang
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Team',
                        font: {
                            size: 14, // Ukuran font
                            weight: 'bold' // Membuat teks Y axis menjadi bold
                        }
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Tahapan',
                        font: {
                            size: 14, // Ukuran font
                            weight: 'bold' // Membuat teks X axis menjadi bold
                        }
                    }
                }
            }
        }
    });
}
