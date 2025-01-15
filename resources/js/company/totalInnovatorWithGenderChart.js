import Chart from 'chart.js/auto';
import ChartDataLabels from 'chartjs-plugin-datalabels';
import autocolors from 'chartjs-plugin-autocolors';

export function renderTotalInnovatorWithGenderChart(chartDataTotalInnovatorWithGenderChart) {
    const ctx = document.getElementById('totalInnovatorWithGenderChart').getContext('2d');

    const labels = Object.keys(chartDataTotalInnovatorWithGenderChart);
    const maleData = labels.map(year => chartDataTotalInnovatorWithGenderChart[year].laki_laki || 0);
    const femaleData = labels.map(year => chartDataTotalInnovatorWithGenderChart[year].perempuan || 0);
    const totalData = labels.map(year => chartDataTotalInnovatorWithGenderChart[year].laki_laki + chartDataTotalInnovatorWithGenderChart[year].perempuan || 0);

    new Chart(ctx, {
        type: 'bar',
        plugins: [ChartDataLabels, autocolors],
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Total Innovator',
                    data: totalData,
                    maxBarThickness: 40
                },
                {
                    label: 'Laki-laki',
                    data: maleData,
                    maxBarThickness: 40
                },
                {
                    label: 'Perempuan',
                    data: femaleData,
                    maxBarThickness: 40
                },
            ],
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                datalabels: {
                    anchor: 'end',
                    align: 'top',
                },
                autocolors: {
                    mode: 'data'
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Tahun',
                    },
                },
                y: {
                    title: {
                        display: true,
                        text: 'Jumlah Innovator',
                    },
                    beginAtZero: true,
                },
            },
        },
    });



    // Render summary
    renderSummary(chartDataTotalInnovatorWithGenderChart);
}


function renderSummary(chartData) {
    // Get total for each year and gender
    const yearlyTotals = {};
    const yearlyGenderData = {};
    let totalMale = 0;
    let totalFemale = 0;

    Object.entries(chartData).forEach(([year, data]) => {
        const maleCount = data.laki_laki || 0;
        const femaleCount = data.perempuan || 0;
        const yearTotal = maleCount + femaleCount;

        yearlyTotals[year] = yearTotal;
        yearlyGenderData[year] = { male: maleCount, female: femaleCount };

        totalMale += maleCount;
        totalFemale += femaleCount;
    });

    // Calculate yearly growth
    const years = Object.keys(yearlyTotals).sort();
    const yearlyGrowth = {};
    for (let i = 1; i < years.length; i++) {
        const currentYear = years[i];
        const previousYear = years[i-1];
        const growth = yearlyTotals[currentYear] - yearlyTotals[previousYear];
        const growthPercentage = ((growth / yearlyTotals[previousYear]) * 100).toFixed(1);
        yearlyGrowth[currentYear] = {
            absolute: growth,
            percentage: growthPercentage
        };
    }

    // Find year with highest and lowest total
    let highestYear = years[0];
    let lowestYear = years[0];
    years.forEach(year => {
        if (yearlyTotals[year] > yearlyTotals[highestYear]) highestYear = year;
        if (yearlyTotals[year] < yearlyTotals[lowestYear]) lowestYear = year;
    });

    // Calculate average yearly total
    const averageTotal = (Object.values(yearlyTotals).reduce((a, b) => a + b, 0) / years.length).toFixed(0);

    // Calculate gender ratio
    const totalInnovators = totalMale + totalFemale;
    const malePercentage = ((totalMale / totalInnovators) * 100).toFixed(1);
    const femalePercentage = ((totalFemale / totalInnovators) * 100).toFixed(1);

    // Create summary HTML
    const summaryHtml = `
        <div class="mt-4 p-4 bg-gray-100 rounded-lg">
            <h3 class="text-lg font-semibold mb-3">Ringkasan Statistik Innovator</h3>

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
                                <tr class="${index % 2 === 0 ? 'bg-white' : 'bg-gray-50'}">
                                    <td class="px-4 py-2">${year}</td>
                                    <td class="px-4 py-2">${yearlyTotals[year].toLocaleString()}</td>
                                    <td class="px-4 py-2">${index === 0 ? '-' : (yearlyGrowth[year].absolute >= 0 ? '+' : '') + yearlyGrowth[year].absolute.toLocaleString()}</td>
                                    <td class="px-4 py-2">${index === 0 ? '-' : yearlyGrowth[year].percentage + '%'}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;

    // Render the summary to the DOM
    const summaryContainer = document.getElementById('chartSummary');
    if (summaryContainer) {
        summaryContainer.innerHTML = summaryHtml;
    }
}
