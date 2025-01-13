import ExcelJS from 'exceljs';
import jsPDF from 'jspdf';
import html2canvas from 'html2canvas';

document.addEventListener("DOMContentLoaded", function () {
    const exportExcelButton = document.querySelector('.export-excel-totalInnovatorStages');
    const exportPdfButton = document.querySelector('.export-pdf-totalInnovatorStages');

    // Data dari server
    const chartData = window.chartDataExportTotalInnovatorStages; // Pastikan chartData tersedia di window

    if (exportExcelButton) {
        exportExcelButton.addEventListener('click', async function () {
            const chartCanvas = document.getElementById('totalInnovatorStagesChart');

            if (chartData && chartCanvas) {
                // Create a new workbook
                const workbook = new ExcelJS.Workbook();
                const worksheet = workbook.addWorksheet('Total Inovator per Tahap');

                // Add headers
                const headers = ['Tahap', 'Jumlah Inovator'];
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
                a.download = `total_innovator_stages_${event_name}.xlsx`;
                a.click();
                window.URL.revokeObjectURL(url);
            }
        });
    }

    if (exportPdfButton) {
        exportPdfButton.addEventListener('click', async function () {
            const chartCanvas = document.getElementById('totalInnovatorStagesChart');

            if (chartData && chartCanvas) {
                // Create a new jsPDF instance
                const pdf = new jsPDF();

                // Add title
                pdf.setFontSize(18);
                pdf.text(`Total Inovator per Tahap Event : ${$event_name}`, 10, 10);

                // Add table headers
                pdf.setFontSize(12);
                let yPosition = 20;
                pdf.text('Tahap', 10, yPosition);
                pdf.text('Jumlah Inovator', 100, yPosition);

                // Add table data
                chartData.labels.forEach((label, index) => {
                    yPosition += 10;
                    pdf.text(label, 10, yPosition);
                    pdf.text(chartData.data[index].toString(), 100, yPosition);
                });

                // Convert chart to image and add to PDF
                html2canvas(chartCanvas).then(canvas => {
                    const imgData = canvas.toDataURL('image/png');
                    pdf.addImage(imgData, 'PNG', 10, yPosition + 10, 180, 100);

                    // Save the PDF
                    pdf.save(`total_innovator_stages_${event_name}.pdf`);
                });
            }
        });
    }
});
