import {
    Chart,
    CategoryScale,
    LinearScale,
    BarController,
    BarElement,
    Tooltip,
} from "chart.js";
import ChartDataLabels from 'chartjs-plugin-datalabels';
import autocolors from 'chartjs-plugin-autocolors'; // Import AutoColors
import toRupiah from '@develoka/angka-rupiah-js';

// Register necessary components
Chart.register(
    CategoryScale,
    LinearScale,
    BarController,
    BarElement,
    Tooltip,
    ChartDataLabels,
    autocolors // Register AutoColors plugin
);

// Get data from the data-attributes
const labels = JSON.parse(document.getElementById("chartDataAkumulasiBenefit").dataset.labels);
const dataValues = JSON.parse(
    document.getElementById("chartDataAkumulasiBenefit").dataset.data
);
const logos = JSON.parse(document.getElementById("chartDataAkumulasiBenefit").dataset.logos);

// Create a custom plugin for drawing images
const imagePlugin = {
    id: "customImagePlugin",
    afterDraw: (chart, args, options) => {
        const { ctx, chartArea, scales } = chart;

        chart.data.labels.forEach((label, index) => {
            const yScale = scales.y;
            const xScale = scales.x;

            // Calculate position for the image
            const y = yScale.getPixelForValue(index);
            const x = chartArea.left - 40; // Adjust this value to position the image

            // Draw the image if it exists in logoImages
            if (logoImages[index]) {
                const img = logoImages[index];

                // Calculate aspect ratio
                const aspectRatio = img.width / img.height;
                const imgWidth = 30; // Set your desired width
                const imgHeight = imgWidth / aspectRatio; // Calculate height based on aspect ratio

                ctx.drawImage(
                    img,
                    x,
                    y - imgHeight / 2, // Center the image vertically
                    imgWidth, // width
                    imgHeight // height
                );
            }
        });
    },
};

// Array to store loaded images
const logoImages = [];

// Load images and create chart
const initChart = async () => {
    try {
        // Load all images
        await Promise.all(
            logos.map((url, index) => {
                return new Promise((resolve, reject) => {
                    const img = new Image();
                    img.onload = () => {
                        logoImages[index] = img;
                        resolve();
                    };
                    img.onerror = reject;
                    img.src = url;
                });
            })
        );

        // Create the chart
        const ctx = document.getElementById("benefitChart").getContext("2d");
        const chart = new Chart(ctx, {
            type: "bar",
            data: {
                labels: labels,
                datasets: [
                    {
                        label: "Benefit",
                        data: dataValues,
                        // Removed backgroundColor for AutoColors plugin to work
                    },
                ],
            },
            options: {
                indexAxis: "y",
                layout: {
                    padding: {
                        left: 50, // Add padding for images
                    },
                },
                scales: {
                    y: {
                        ticks: {
                            display: false, // Hide default labels
                        },
                    },
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return toRupiah(value, { useUnit: true, longUnit: true, spaceBeforeUnit: true, formal: false });
                            },
                        },
                    },
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    autocolors: {
                        mode: 'data'
                    },
                    tooltip: {
                        callbacks: {
                            title: (tooltipItems) => {
                                // Display the label of the hovered item
                                return labels[tooltipItems[0].dataIndex];
                            },
                            label: (tooltipItem) => {
                                // Display the value of the hovered item
                                return `Nilai: ${toRupiah(dataValues[tooltipItem.dataIndex], {useUnit: true, longUnit: true, spaceBeforeUnit: true, formal: false})}`;
                            },
                        },
                    },
                    datalabels: {
                        formatter: (value) => toRupiah(value, {useUnit: true, longUnit: true, spaceBeforeUnit: true, formal: false}),
                        color: 'black',
                        anchor: 'center', // Center the label horizontally
                        align: 'right', // Center the label vertically
                        font: {
                            weight: 'bold',
                            size: 17,
                        },
                    },
                    customImagePlugin: imagePlugin,
                },
            },
            plugins: [imagePlugin], // Add plugins to Chart instance
        });
    } catch (error) {
        console.error("Error initializing chart:", error);
    }
};

// Initialize the chart
initChart();


document.addEventListener('DOMContentLoaded', () => {
    // Ambil elemen data dari DOM
    const financialDataElement = document.getElementById('financialBenefitsData');

    if (financialDataElement) {
        // Ambil data benefits dari atribut data
        const financialBenefits = JSON.parse(financialDataElement.dataset.benefits);
        const potentialBenefits = JSON.parse(financialDataElement.dataset.potentialBenefits);

        // Render financial benefits
        const financialBenefitsContainer = document.getElementById('financialBenefits');
        financialBenefits.forEach(benefit => {
            const benefitItem = document.createElement('div');
            benefitItem.className = 'financial-benefit-item';
            benefitItem.innerHTML = `
                <span class="financial-benefit-year">${benefit.year}</span>
                <span class="financial-benefit-total">
                ${toRupiah(benefit.total, { useUnit: true, longUnit: true, spaceBeforeUnit: true, formal: false })}

                </span>
            `;
            financialBenefitsContainer.appendChild(benefitItem);
        });

        // Render potential benefits
        const potentialBenefitsContainer = document.getElementById('potentialBenefits');
        potentialBenefits.forEach(benefit => {
            const benefitItem = document.createElement('div');
            benefitItem.className = 'financial-benefit-item';
            benefitItem.innerHTML = `
                <span class="financial-benefit-year">${benefit.year}</span>
                <span class="financial-benefit-total">
                ${toRupiah(benefit.total, { useUnit: true, longUnit: true, spaceBeforeUnit: true, formal: false })}

                </span>
            `;
            potentialBenefitsContainer.appendChild(benefitItem);
        });
    }
});
