import { Chart, registerables } from 'chart.js';
import ChartDataLabels from 'chartjs-plugin-datalabels';

Chart.register(...registerables, ChartDataLabels);

document.addEventListener("DOMContentLoaded", function() {
    const charts = JSON.parse(document.getElementById('charts-data-persebaran-inovasi-setiap-perusahaan').textContent);
    const categories = JSON.parse(document.getElementById('categories-data').textContent);

    charts.forEach((chart, index) => {

        let ctx = document.getElementById('persebaran-inovasi-perusahaan-chart-' + index).getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chart.categories,
                datasets: [{
                    label: chart.company,
                    data: chart.data,
                    backgroundColor: chart.categories.map(cat => categories[cat] ?? '#ffffff'),
                    borderWidth: 1,
                    barThickness: 10
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#000'
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        display: false,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                },
                plugins: {
                    legend: {
                        labels: {
                            color: '#000'
                        }
                    },
                    tooltip: {
                        position: 'nearest',
                        intersect: true,
                    },
                    drawValue: true, // Enable custom plugin,
                    datalabels: {
                        color: 'black',
                        anchor: 'center', // Center the label horizontally
                        align: 'center', // Center the label vertically
                        font: {
                            weight: 'bold',
                            size: 12,
                        },
                    },

                }
            }
        });
    });
});
