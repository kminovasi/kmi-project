import jsPDF from 'jspdf';
import html2canvas from 'html2canvas';

document.addEventListener("DOMContentLoaded", function () {
    const exportButtons = document.querySelectorAll('.export-pdf');

    exportButtons.forEach(button => {
        button.addEventListener('click', async function () {
            const companyId = this.getAttribute('data-company-id');
            const companyName = this.closest('.card').querySelector('.card-title').textContent.trim();
            const chartCanvas = document.querySelector(`#innovatorChart_${companyId}`);
            const chartData = window.chartData[`innovatorChart_${companyId}`];

            if (chartData && chartCanvas) {
                // Create a new jsPDF instance
                const pdf = new jsPDF();

                // Add title
                pdf.setFontSize(18);
                pdf.text(`Total Innovator per Kategori - ${companyName}`, 10, 10);

                // Add table headers
                pdf.setFontSize(12);
                pdf.text('Kategori', 10, 20);
                pdf.text('Total Innovator', 100, 20);

                // Add table data
                chartData.labels.forEach((label, index) => {
                    pdf.text(label, 10, 30 + (index * 10));
                    pdf.text(chartData.datasets[0].data[index].toString(), 100, 30 + (index * 10));
                });

                // Convert chart to image and add to PDF
                html2canvas(chartCanvas).then(canvas => {
                    const imgData = canvas.toDataURL('image/png');
                    pdf.addImage(imgData, 'PNG', 10, 30 + (chartData.labels.length * 10), 180, 100);

                    // Save the PDF
                    pdf.save(`total_innovator_per_kategori_${companyName}.pdf`);
                });
            }
        });
    });
});
