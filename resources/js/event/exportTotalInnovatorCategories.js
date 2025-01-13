import ExcelJS from 'exceljs';
import jsPDF from 'jspdf';
import html2canvas from 'html2canvas';

document.addEventListener("DOMContentLoaded", function () {
    const exportExcelButton = document.querySelector('.export-excel-totalInnovatorCategories');
    const exportPdfButton = document.querySelector('.export-pdf-totalInnovatorCategories');

    if (exportExcelButton) {
        exportExcelButton.addEventListener('click', async function () {
            const chartCanvas = document.getElementById('totalInnovatorChart');
            const chartData = window.chartDataTotalInnovatorCategories;

            if (chartData && chartCanvas) {
                // Create a new workbook
                const workbook = new ExcelJS.Workbook();
                const worksheet = workbook.addWorksheet('Data');

                // Add headers
                const headers = ['Kategori', 'Total Inovator'];
                worksheet.addRow(headers);

                // Add data
                chartData.labels.forEach((label, index) => {
                    worksheet.addRow([label, chartData.data[index]]);
                });

                // Convert chart to image
                const chartImage = chartCanvas.toDataURL('image/png');

                // Add image to worksheet
                const imageId = workbook.addImage({
                    base64: chartImage.split(',')[1],
                    extension: 'png',
                });

                // Position image below data
                worksheet.addImage(imageId, {
                    tl: { col: 0, row: chartData.labels.length + 2 },
                    ext: { width: 500, height: 300 }
                });

                // Export file
                const buffer = await workbook.xlsx.writeBuffer();
                const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                const url = window.URL.createObjectURL(blob);

                const a = document.createElement('a');
                a.href = url;
                a.download = `total_innovator_by_categories_${event_name}.xlsx`;
                a.click();
                window.URL.revokeObjectURL(url);
            }
        });
    }

    if (exportPdfButton) {
        exportPdfButton.addEventListener('click', async function () {
            const chartCanvas = document.getElementById('totalInnovatorChart');
            const chartData = window.chartDataTotalInnovatorCategories;

            if (chartData && chartCanvas) {
                // Create a new jsPDF instance
                const pdf = new jsPDF();

                // Add title
                pdf.setFontSize(18);
                pdf.text(`Total Inovator per Kategori Event : ${event_name}`, 10, 10);

                // Add table headers
                pdf.setFontSize(12);
                pdf.text('Kategori', 10, 20);
                pdf.text('Total Inovator', 100, 20);

                // Add table data
                chartData.labels.forEach((label, index) => {
                    const yPosition = 30 + (index * 10);
                    pdf.text(label, 10, yPosition);
                    pdf.text(chartData.data[index].toString(), 100, yPosition);
                });

                // Convert chart to image and add to PDF
                html2canvas(chartCanvas).then(canvas => {
                    const imgData = canvas.toDataURL('image/png');
                    pdf.addImage(imgData, 'PNG', 10, 30 + (chartData.labels.length * 10), 180, 100);

                    // Save the PDF
                    pdf.save(`total_innovator_by_categories_${event_name}.pdf`);
                });
            }
        });
    }
});
