import Chart from 'chart.js/auto';

export function renderTotalInnovatorStagesChart(canvasId, chartData) {
    const ctx = document.getElementById(canvasId).getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Jumlah Team',
                data: chartData.data,
                backgroundColor: [
                    '#AED6F1', // Pastel Blue
                    '#A3E4D7', // Pastel Green
                    '#F9E79F', // Pastel Yellow
                    '#F5B7B1', // Pastel Pink
                    '#ABEBC6', // Pastel Mint
                    '#D7BDE2', // Pastel Purple
                    '#D6DBDF', // Pastel Gray
                    '#F7DC6F', // Pastel Orange
                    '#FAD7A0'  // Pastel Peach
                ],
                borderColor: '#fff',
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
                datalabels: {
                    color: '#000',
                    font: {
                        weight: 'bold', // Membuat angka di dalam chart menjadi bold
                        size: 20 // Ukuran font angka
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
