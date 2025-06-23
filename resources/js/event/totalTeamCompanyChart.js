import Chart from "chart.js/auto";
import autocolors from 'chartjs-plugin-autocolors';
import ChartDataLabels from 'chartjs-plugin-datalabels';

export function renderTotalTeamCompanyChart(chartDataTotalTeamCompany) {
    const chartData = chartDataTotalTeamCompany;
    const labels = chartData.map((item) => item.company_name);
    const data = chartData.map((item) => item.total_teams);
    // Buat chart
    const ctx = document
        .getElementById("chartCanvasTotalTeamCompany")
        .getContext("2d");
    new Chart(ctx, {
        type: "bar",
        plugins: [autocolors, ChartDataLabels],
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Jumlah Tim",
                    data: data,
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            plugins: {
                autocolors: {
                    mode: 'data'
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
                },
            },
        },
    });
}

window.renderTotalTeamCompanyChart = renderTotalTeamCompanyChart;