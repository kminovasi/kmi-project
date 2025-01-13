import ExcelJS from 'exceljs';
import jsPDF from 'jspdf';
import html2canvas from 'html2canvas';

document.addEventListener("DOMContentLoaded", function () {
    const exportExcelButton = document.querySelector('.export-excel-totalBenefitCompanyChart');
    const exportPdfButton = document.querySelector('.export-pdf-totalBenefitCompanyChart');

    if (exportExcelButton) {
        exportExcelButton.addEventListener('click', async function () {
            const chartCanvas = document.getElementById('totalBenefitChart');
            const chartData = window.chartData;

            if (chartData && chartCanvas) {
                // Create a new workbook
                const workbook = new ExcelJS.Workbook();
                const worksheet = workbook.addWorksheet('Data');

                // Add headers
                const headers = ['Perusahaan', 'Akumulasi Total Finansial Benefit'];
                worksheet.addRow(headers);

                // Add data
                chartData.forEach((item) => {
                    worksheet.addRow([item.company_name, item.total_benefit]);
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
                    tl: { col: 0, row: chartData.length + 2 },
                    ext: { width: 500, height: 300 }
                });

                // Export file
                const buffer = await workbook.xlsx.writeBuffer();
                const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                const url = window.URL.createObjectURL(blob);

                const a = document.createElement('a');
                a.href = url;
                a.download = `total_benefit_company_${event_name}.xlsx`;
                a.click();
                window.URL.revokeObjectURL(url);
            }
        });
    }

    if (exportPdfButton) {
        exportPdfButton.addEventListener('click', async function () {
            const chartCanvas = document.getElementById('totalBenefitChart');
            const chartData = window.chartData;

            if (chartData && chartCanvas) {
                // Create a new jsPDF instance
                const pdf = new jsPDF();

                // Add title
                pdf.setFontSize(18);
                pdf.text(`Total Benefit Perusahaan`, 10, 10);

                // Add table headers
                pdf.setFontSize(12);
                let yPosition = 20;
                pdf.text('Perusahaan', 10, yPosition);
                pdf.text('Akumulasi Total Finansial Benefit', 100, yPosition);

                // Add table data
                chartData.forEach((item) => {
                    yPosition += 10;
                    pdf.text(item.company_name, 10, yPosition);

                    // Format total_benefit as Rupiah
                    const formattedBenefit = new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                    }).format(item.total_benefit);

                    pdf.text(formattedBenefit, 100, yPosition);
                });

                // Convert chart to image and add to PDF
                html2canvas(chartCanvas).then(canvas => {
                    const imgData = canvas.toDataURL('image/png');
                    pdf.addImage(imgData, 'PNG', 10, yPosition + 10, 180, 100);

                    // Save the PDF
                    pdf.save(`total_benefit_company_${event_name}.pdf`);
                });
            }
        });
    }

});
