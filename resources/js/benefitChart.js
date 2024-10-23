import { Chart, CategoryScale, LinearScale, BarController, BarElement } from 'chart.js';
import 'chartjs-plugin-annotation';

// Register necessary components
Chart.register(CategoryScale, LinearScale, BarController, BarElement);

// Define your colors for the chart
const colors = [
    'rgba(255, 99, 132)',
    'rgba(54, 162, 235)',
    'rgba(255, 206, 86)',
    // Add more colors as needed
];

// Get data from the data-attributes
const labels = JSON.parse(document.getElementById('chartData').dataset.labels);
const dataValues = JSON.parse(document.getElementById('chartData').dataset.data);
const logos = JSON.parse(document.getElementById('chartData').dataset.logos);

// Create a custom plugin for drawing images
const imagePlugin = {
    id: 'customImagePlugin',
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
                ctx.drawImage(
                    logoImages[index],
                    x,
                    y - 15, // Center the image vertically
                    30,     // width
                    30      // height
                );
            }
        });
    }
};

// Register the custom plugin
Chart.register(imagePlugin);

// Array to store loaded images
const logoImages = [];

// Load images and create chart
const initChart = async () => {
    try {
        // Load all images
        await Promise.all(logos.map((url, index) => {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.onload = () => {
                    logoImages[index] = img;
                    resolve();
                };
                img.onerror = reject;
                img.src = url;
            });
        }));

        // Create the chart
        const ctx = document.getElementById('benefitChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Benefit',
                    data: dataValues,
                    backgroundColor: colors.slice(0, labels.length),
                }]
            },
            options: {
                indexAxis: 'y',
                layout: {
                    padding: {
                        left: 50 // Add padding for images
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            display: false // Hide default labels
                        }
                    },
                    x: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

    } catch (error) {
        console.error('Error initializing chart:', error);
    }
};

// Initialize the chart
initChart();
