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

document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById("directorateChart").getContext("2d");

    const directorateNames = window.directorateData.map(
        (item) => item.directorate_name
    );
    const totalIdeas = window.directorateData.map((item) => item.total_ideas);
    const totalInnovations = window.directorateData.map(
        (item) => item.total_innovations
    );

    new Chart(ctx, {
        type: "bar",
        data: {
            labels: directorateNames,
            datasets: [
                {
                    label: "Total Ideas",
                    data: totalIdeas,
                    backgroundColor: "rgba(54, 162, 235, 0.7)",
                    borderColor: "rgba(54, 162, 235, 1)",
                    borderWidth: 1,
                    barPercentage: 1,
                    categoryPercentage: 0.5,
                },
                {
                    label: "Total Innovations",
                    data: totalInnovations,
                    backgroundColor: "rgba(255, 99, 132, 0.7)",
                    borderColor: "rgba(255, 99, 132, 1)",
                    borderWidth: 1,
                    barPercentage: 1,
                    categoryPercentage: 0.5,
                },
            ],
        },
        options: {
            indexAxis: "y",
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
                title: {
                    display: false,
                },
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: {
                        display: false,
                    },
                    ticks: {
                        font: {
                            size: 12,
                        },
                    },
                },
                y: {
                    grid: {
                        display: false,
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

    // Custom legend
    const legendItems = [
        { label: "Total Ide", color: "rgba(54, 162, 235, 0.7)" },
        { label: "Total Innovasi", color: "rgba(255, 99, 132, 0.7)" },
    ];

    const legendContainer = document.getElementById("chartLegend");
    legendItems.forEach((item) => {
        const legendItem = document.createElement("div");
        legendItem.classList.add("legend-item");
        legendItem.innerHTML = `
            <span class="legend-color" style="background-color: ${item.color}"></span>
            <span class="legend-label">${item.label}</span>
        `;
        legendContainer.appendChild(legendItem);
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const ctx = document
        .getElementById("innovatorDirectorateChart")
        .getContext("2d");
    const data = window.innovatorDirectorateData;

    const labels = Object.keys(data);
    const values = Object.values(data);

    new Chart(ctx, {
        type: "bar", // Tetap menggunakan 'bar', tapi kita akan mengubah orientasinya
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Jumlah Inovator",
                    data: values,
                    backgroundColor: "rgba(54, 162, 235, 0.8)",
                    borderColor: "rgba(54, 162, 235, 1)",
                    borderWidth: 1,
                },
            ],
        },
        options: {
            indexAxis: "y", // Ini mengubah orientasi menjadi horizontal
            responsive: true,
            plugins: {
                legend: {
                    display: false,
                },
            },
            scales: {
                x: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: "Jumlah Inovator",
                    },
                },
            },
        },
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const ctx = document
        .getElementById("potentialBenefitChart")
        .getContext("2d");
    const data = window.potentialBenefitsData;

    const labels = Object.keys(data);
    const values = Object.values(data);

    new Chart(ctx, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Total Potential Benefit",
                    data: values,
                    backgroundColor: [
                        "rgba(255, 99, 132, 0.8)",
                        "rgba(54, 162, 235, 0.8)",
                        "rgba(255, 206, 86, 0.8)",
                        "rgba(75, 192, 192, 0.8)",
                        "rgba(153, 102, 255, 0.8)",
                        "rgba(255, 159, 64, 0.8)",
                    ],
                    borderColor: [
                        "rgba(255, 99, 132, 1)",
                        "rgba(54, 162, 235, 1)",
                        "rgba(255, 206, 86, 1)",
                        "rgba(75, 192, 192, 1)",
                        "rgba(153, 102, 255, 1)",
                        "rgba(255, 159, 64, 1)",
                    ],
                    borderWidth: 1,
                },
            ],
        },
        options: {
            indexAxis: "y",
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: "Direktorat",
                    },
                },
                x: {
                    title: {
                        display: true,
                        text: "Potential Benefit",
                    },
                },
            },
        },
    });
});
