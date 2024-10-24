import { Chart } from "chart.js/auto";

document.addEventListener("DOMContentLoaded", function () {
    const chartElement = document.getElementById("paperCountChart");
    if (chartElement) {
        const chartData = JSON.parse(chartElement.dataset.chart);
        const companyName = chartElement.dataset.company;

        new Chart(chartElement.getContext("2d"), {
            type: "bar",
            data: {
                labels: chartData.years,
                datasets: [
                    {
                        label: `Paper Count`,
                        data: chartData.paperCounts,
                        backgroundColor: "rgba(54, 162, 235, 0.8)",
                        borderColor: "rgba(54, 162, 235, 1)",
                        borderWidth: 1,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        backgroundColor: "rgba(0, 0, 0, 0.8)",
                        titleFont: {
                            size: 14,
                        },
                        bodyFont: {
                            size: 12,
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: "Jumlah makalah Inovasi",
                            font: {
                                size: 14,
                                weight: "bold",
                            },
                        },
                        ticks: {
                            font: {
                                size: 12,
                            },
                        },
                    },
                    x: {
                        title: {
                            display: true,
                            text: "Tahun",
                            font: {
                                size: 14,
                                weight: "bold",
                            },
                        },
                        ticks: {
                            font: {
                                size: 12,
                            },
                        },
                    },
                },
            },
        });
    }
});
