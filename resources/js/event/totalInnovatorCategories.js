import Chart from 'chart.js/auto';

/**
 * Render Total Innovator Chart
 * @param {string} canvasId - ID dari elemen canvas
 * @param {object} chartData - Data chart dari server
 */
export function renderTotalInnovatorChart(canvasId, chartData) {
    const ctx = document.getElementById(canvasId).getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Total Innovators per Category',
                data: chartData.data,
                backgroundColor: chartData.colors,
                borderColor: chartData.colors.map(color => color.replace('1)', '0.8)')),
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
                            return `${context.dataset.label}: ${context.raw}`;
                        }
                    }
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
        }
    });
}
