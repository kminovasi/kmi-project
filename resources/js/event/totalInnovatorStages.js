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
                    '#3498db', '#1abc9c', '#f39c12', '#e74c3c',
                    '#2ecc71', '#9b59b6', '#34495e', '#95a5a6', '#e67e22'
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
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Team'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Tahapan'
                    }
                }
            }
        }
    });
}
