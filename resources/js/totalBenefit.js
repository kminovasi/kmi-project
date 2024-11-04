import { Chart } from "chart.js/auto";

export function initFinancialBenefitChart(chartData) {
    const ctxTotalBenefit = document
        .getElementById("financialBenefitChart")
        .getContext("2d");

    new Chart(ctxTotalBenefit, {
        type: "line",
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    label: "Total Financial Benefit per tahun",
                    data: chartData.data,
                    borderColor: "rgb(75, 192, 192)",
                    tension: 0.1,
                    fill: false,
                },
            ],
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return new Intl.NumberFormat("id-ID", {
                                style: "currency",
                                currency: "IDR",
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0,
                            }).format(value);
                        },
                    },
                },
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return new Intl.NumberFormat("id-ID", {
                                style: "currency",
                                currency: "IDR",
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0,
                            }).format(context.raw);
                        },
                    },
                },
            },
        },
    });
}
export function initPotentialBenefitChart(chartData) {
    const ctxTotalBenefit = document
        .getElementById("potentialBenefitChart")
        .getContext("2d");

    new Chart(ctxTotalBenefit, {
        type: "line",
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    label: "Total Potential Benefit per tahun",
                    data: chartData.data,
                    borderColor: "rgb(75, 192, 192)",
                    tension: 0.1,
                    fill: false,
                },
            ],
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return new Intl.NumberFormat("id-ID", {
                                style: "currency",
                                currency: "IDR",
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0,
                            }).format(value);
                        },
                    },
                },
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return new Intl.NumberFormat("id-ID", {
                                style: "currency",
                                currency: "IDR",
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0,
                            }).format(context.raw);
                        },
                    },
                },
            },
        },
    });
}
