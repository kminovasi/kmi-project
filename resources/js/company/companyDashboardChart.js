import { Chart, registerables } from 'chart.js';
import ChartDataLabels from 'chartjs-plugin-datalabels';

Chart.register(...registerables, ChartDataLabels);
document.addEventListener("DOMContentLoaded", function () {
    // Paper Count Chart
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
                    datalabels: {
                        color: 'black',
                        anchor: 'center', // Center the label horizontally
                        align: 'center', // Center the label vertically
                        font: {
                            weight: 'bold',
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

// Directorate Chart
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById("directorateChart").getContext("2d");

    const directorateArray = Object.entries(window.directorateData).map(
        ([key, value]) => ({
            directorate_name: key,
            ...value,
        })
    );

    const filteredData = directorateArray.filter(
        (item) =>
            item.directorate_name &&
            item.directorate_name !== "-" &&
            item.directorate_name !== ""
    );

    filteredData.sort(
        (a, b) =>
            b.total_ideas +
            b.total_innovations -
            (a.total_ideas + a.total_innovations)
    );

    const itemHeight = 40;
    const minHeight = 400;
    const calculatedHeight = Math.max(
        minHeight,
        filteredData.length * itemHeight
    );

    document.querySelector(
        ".chart-wrapper"
    ).style.height = `${calculatedHeight}px`;

    const directorateNames = filteredData.map((item) => item.directorate_name);
    const totalIdeas = filteredData.map((item) => item.total_ideas);
    const totalInnovations = filteredData.map((item) => item.total_innovations);

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
                datalabels: {
                    color: 'black',
                    anchor: 'center', // Center the label horizontally
                    align: 'center', // Center the label vertically
                    font: {
                        weight: 'bold',
                        size: 12,
                    },
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
                        callback: function (value) {
                            const label = this.getLabelForValue(value);
                            if (label.length > 30) {
                                return label.substr(0, 27) + "...";
                            }
                            return label;
                        },
                    },
                },
            },
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 0,
                    bottom: 0,
                },
            },
        },
    });

    const legendItems = [
        { label: "Total Ide", color: "rgba(54, 162, 235, 0.7)" },
        { label: "Total Innovasi", color: "rgba(255, 99, 132, 0.7)" },
    ];

    const legendContainer = document.getElementById("chartLegend");
    legendContainer.innerHTML = "";
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

// Innovator Directorate Chart
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document
        .getElementById("innovatorDirectorateChart")
        .getContext("2d");
    const data = window.innovatorDirectorateData;

    const labels = Object.keys(data).map((label) =>
        label === "-" || !label.trim() ? "Tidak Masuk Unit Organisasi" : label
    );
    const values = Object.values(data);

    new Chart(ctx, {
        type: "bar",
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
            indexAxis: "y",
            responsive: true,
            plugins: {
                legend: {
                    display: false,
                },
                datalabels: {
                    color: 'black',
                    anchor: 'center', // Center the label horizontally
                    align: 'center', // Center the label vertically
                    font: {
                        weight: 'bold',
                        size: 12,
                    },
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

// Potential Benefit Chart
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document
        .getElementById("potentialBenefitChart")
        .getContext("2d");
    const data = window.potentialBenefitsData;

    const labels = Object.keys(data).map((label) =>
        label === "-" || !label.trim() ? "Tidak Masuk Unit Organisasi" : label
    );
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
        plugins: [
            {
                afterDraw: function (chart) {
                    var ctx = chart.ctx;
                    chart.data.datasets.forEach(function (dataset, i) {
                        var meta = chart.getDatasetMeta(i);
                        meta.data.forEach(function (bar, index) {
                            var data = dataset.data[index];
                            if (data > 0) {
                                ctx.fillStyle = "black";
                                ctx.textAlign = "left";
                                ctx.textBaseline = "middle";
                                var padding = 5;
                                var xPos = bar.x - bar.width / 2;
                                ctx.fillText(
                                    data.toLocaleString(),
                                    xPos + padding,
                                    bar.y
                                );
                            }
                        });
                    });
                },
            },
        ],
    });
});
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document
        .getElementById("financialBenefitOrganization")
        .getContext("2d");
    const data = window.financialBenefitsData;

    const labels = Object.keys(data).map((label) =>
        label === "-" || !label.trim() ? "Tidak Masuk Unit Organisasi" : label
    );
    const values = Object.values(data);

    new Chart(ctx, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Total Financial Benefit",
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
        plugins: [
            {
                afterDraw: function (chart) {
                    var ctx = chart.ctx;
                    chart.data.datasets.forEach(function (dataset, i) {
                        var meta = chart.getDatasetMeta(i);
                        meta.data.forEach(function (bar, index) {
                            var data = dataset.data[index];
                            if (data > 0) {
                                ctx.fillStyle = "black";
                                ctx.textAlign = "left";
                                ctx.textBaseline = "middle";
                                var padding = 5;
                                var xPos = bar.x - bar.width / 2;
                                ctx.fillText(
                                    data.toLocaleString(),
                                    xPos + padding,
                                    bar.y
                                );
                            }
                        });
                    });
                },
            },
        ],
    });
});
