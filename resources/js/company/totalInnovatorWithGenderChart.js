import Chart from "chart.js/auto";
import ChartDataLabels from "chartjs-plugin-datalabels";
import autocolors from "chartjs-plugin-autocolors";

export function renderTotalInnovatorWithGenderChart(chartDataTotalInnovatorWithGenderChart) {
    const ctx = document.getElementById("totalInnovatorWithGenderChart").getContext("2d");

    const labels = Object.keys(chartDataTotalInnovatorWithGenderChart);
    const maleData = labels.map((year) => chartDataTotalInnovatorWithGenderChart[year].laki_laki || 0);
    const femaleData = labels.map((year) => chartDataTotalInnovatorWithGenderChart[year].perempuan || 0);
    const outsourceData = labels.map((year) => chartDataTotalInnovatorWithGenderChart[year].outsourcing || 0);

    new Chart(ctx, {
        type: "bar",
        plugins: [ChartDataLabels],
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Laki-laki",
                    data: maleData,
                    backgroundColor: "#006dd9",
                    maxBarThickness: 60,
                },
                {
                    label: "Perempuan",
                    data: femaleData,
                    backgroundColor: "#db2d92",
                    maxBarThickness: 60,
                },
                {
                    label: "Outsource",
                    data: outsourceData,
                    backgroundColor: "#d8c600",
                    maxBarThickness: 60,
                }
            ],
        },
        options: {
            responsive: false,
            plugins: {
                legend: { position: "top" },
                datalabels: {
                    anchor: "end",
                    align: "top",
                },
            },
            scales: {
                x: { title: { display: true, text: "Tahun" } },
                y: {
                    title: { display: true, text: "Jumlah Innovator" },
                    beginAtZero: true,
                },
            },
        },
    });

    renderSummary(chartDataTotalInnovatorWithGenderChart);
}

function renderSummary(chartData) {
    const yearlyTotals = {};
    const yearlyGenderData = {};
    let totalMale = 0, totalFemale = 0, totalOutsource = 0;

    Object.entries(chartData).forEach(([year, data]) => {
        const maleCount = data.laki_laki || 0;
        const femaleCount = data.perempuan || 0;
        const outsourceCount = data.outsourcing || 0;
        const yearTotal = maleCount + femaleCount + outsourceCount;

        yearlyTotals[year] = yearTotal;
        yearlyGenderData[year] = { male: maleCount, female: femaleCount, outsourcing: outsourceCount };

        totalMale += maleCount;
        totalFemale += femaleCount;
        totalOutsource += outsourceCount;
    });

    const years = Object.keys(yearlyTotals).map((year) => parseInt(year)).sort((a, b) => b - a);
    const yearlyGrowth = {};
    for (let i = 0; i < years.length; i++) {
        const currentYear = years[i];
        const previousYear = years[i + 1];
        const growth = yearlyTotals[currentYear] - yearlyTotals[previousYear];
        const growthPercentage = ((growth / yearlyTotals[previousYear]) * 100).toFixed(1);
        yearlyGrowth[currentYear] = {
            absolute: growth,
            percentage: growthPercentage,
        };
    }

    let highestYear = years[0], lowestYear = years[0];
    years.forEach((year) => {
        if (yearlyTotals[year] > yearlyTotals[highestYear]) highestYear = year;
        if (yearlyTotals[year] < yearlyTotals[lowestYear]) lowestYear = year;
    });

    const averageTotal = (
        Object.values(yearlyTotals).reduce((a, b) => a + b, 0) / years.length
    ).toFixed(0);

    const totalInnovators = totalMale + totalFemale + totalOutsource;
    const malePercentage = ((totalMale / totalInnovators) * 100).toFixed(1);
    const femalePercentage = ((totalFemale / totalInnovators) * 100).toFixed(1);
    const outsourcePercentage = ((totalOutsource / totalInnovators) * 100).toFixed(1);

    const summaryHtml = `
        <div class="mt-4 p-4 bg-gray-100 rounded summary-card">
            <h3 class="text-lg font-semibold mb-3 text-center">Ringkasan Statistik Innovator</h3>
            <div class="container-fluid d-flex flex-row justify-content-between align-items-baseline flex-wrap">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-medium mb-2">Statistik Total:</h4>
                        <ul class="list-disc pl-5">
                            <li>Total keseluruhan: ${totalInnovators.toLocaleString()} innovator</li>
                            <li>Rata-rata per tahun: ${parseInt(averageTotal).toLocaleString()} innovator</li>
                            <li>Tahun tertinggi: ${highestYear} (${yearlyTotals[highestYear].toLocaleString()} innovator)</li>
                            <li>Tahun terendah: ${lowestYear} (${yearlyTotals[lowestYear].toLocaleString()} innovator)</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-medium mb-2">Distribusi Gender:</h4>
                        <ul class="list-disc pl-5">
                            <li>Laki-laki: ${totalMale.toLocaleString()} (${malePercentage}%)</li>
                            <li>Perempuan: ${totalFemale.toLocaleString()} (${femalePercentage}%)</li>
                            <li>Outsource: ${totalOutsource.toLocaleString()} (${outsourcePercentage}%)</li>
                        </ul>
                    </div>
                </div>
                <div class="mt-4">
                    <h4 class="font-medium mb-2">Pertumbuhan Tahunan:</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="px-4 py-2">Tahun</th>
                                    <th class="px-4 py-2">Jumlah</th>
                                    <th class="px-4 py-2">Pertumbuhan</th>
                                    <th class="px-4 py-2">Persentase</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${years.map((year, index) => `
                                    <tr class="${index % 2 === 0 ? "bg-white" : "bg-gray-200"}">
                                        <td class="px-4 py-2">${year}</td>
                                        <td class="px-4 py-2">${yearlyTotals[year].toLocaleString()}</td>
                                        <td class="px-4 py-2">${index + 1 == years.length ? "-" : (yearlyGrowth[year].absolute >= 0 ? "+" : "") + yearlyGrowth[year].absolute.toLocaleString()}</td>
                                        <td class="px-4 py-2">${index + 1 == years.length ? "-" : yearlyGrowth[year].percentage + "%"}</td>
                                    </tr>
                                `).join("")}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `;

    const summaryContainer = document.getElementById("chartSummary");
    if (summaryContainer) {
        summaryContainer.innerHTML = summaryHtml;
    }
}

// 👉 Tambahkan baris ini agar bisa diakses dari Blade
window.renderTotalInnovatorWithGenderChart = renderTotalInnovatorWithGenderChart;