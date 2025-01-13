import ExcelJS from 'exceljs';
import jsPDF from 'jspdf';
import html2canvas from 'html2canvas';

document.addEventListener("DOMContentLoaded", function () {
    const exportExcelButton = document.querySelector('.export-excel-totalFinancialBenefitByOrganizationChart');
    const exportPdfButton = document.querySelector('.export-pdf-totalFinancialBenefitByOrganizationChart');

    if (exportExcelButton) {
        exportExcelButton.addEventListener('click', async function () {
            const chartCanvas = document.getElementById('totalFinancialChart');
            const chartData = window.chartData;
            const organizationUnitLabel = window.organizationUnitLabel;

            if (chartData && chartCanvas) {
                // Create a new workbook
                const workbook = new ExcelJS.Workbook();
                const worksheet = workbook.addWorksheet('Data');

                // Determine the range of years dynamically from the chartData
                const years = new Set();
                const labels = Object.keys(chartData);
                labels.forEach(unit => {
                    Object.keys(chartData[unit]).forEach(year => {
                        years.add(parseInt(year));
                    });
                });
                const sortedYears = Array.from(years).sort();

                // Add headers
                const headers = [organizationUnitLabel, ...sortedYears];
                worksheet.addRow(headers);

                // Add data
                labels.forEach((label) => {
                    const row = [label];
                    sortedYears.forEach(year => {
                        row.push(chartData[label][year] || 0);
                    });
                    worksheet.addRow(row);
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
                    tl: { col: 0, row: labels.length + 2 },
                    ext: { width: 500, height: 300 }
                });

                // Export file
                const buffer = await workbook.xlsx.writeBuffer();
                const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                const url = window.URL.createObjectURL(blob);

                const a = document.createElement('a');
                a.href = url;
                a.download = `total_financial_benefit_by_organization_${organizationUnitLabel}_${company_name}.xlsx`;
                a.click();
                window.URL.revokeObjectURL(url);
            }
        });
    }

    if (exportPdfButton) {
        exportPdfButton.addEventListener('click', async function () {
            const chartCanvas = document.getElementById('totalFinancialChart');
            const chartData = window.chartData;
            const organizationUnitLabel = window.organizationUnitLabel;

            if (chartData && chartCanvas) {
                // Create a new jsPDF instance
                const pdf = new jsPDF();

                // Add title
                pdf.setFontSize(18);
                pdf.text('Total Financial Benefit per ' + organizationUnitLabel, 10, 10);

                // Determine the range of years dynamically from the chartData
                const years = new Set();
                const labels = Object.keys(chartData);
                labels.forEach(unit => {
                    Object.keys(chartData[unit]).forEach(year => {
                        years.add(parseInt(year));
                    });
                });
                const sortedYears = Array.from(years).sort();

                // Calculate positions dynamically
                const labelX = 10;
                const yearXPositions = [labelX + pdf.getTextWidth(organizationUnitLabel) + 10];
                sortedYears.forEach((year, index) => {
                    if (index > 0) {
                        yearXPositions.push(yearXPositions[index - 1] + 50);
                    }
                });

                // Add table headers
                pdf.setFontSize(12);
                pdf.text(organizationUnitLabel, labelX, 20);
                sortedYears.forEach((year, index) => {
                    pdf.text(year.toString(), yearXPositions[index], 20);
                });

                // Add table data
                labels.forEach((label, index) => {
                    const yPosition = 30 + (index * 10);
                    pdf.text(label, labelX, yPosition);
                    sortedYears.forEach((year, yearIndex) => {
                        pdf.text((chartData[label][year] || 0).toString(), yearXPositions[yearIndex], yPosition);
                    });
                });

                // Convert chart to image and add to PDF
                html2canvas(chartCanvas).then(canvas => {
                    const imgData = canvas.toDataURL('image/png');
                    pdf.addImage(imgData, 'PNG', 10, 30 + (labels.length * 10), 180, 100);

                    // Save the PDF
                    pdf.save(`total_financial_benefit_by_organization ${organizationUnitLabel}_${company_name}.pdf`);
                });
            }
        });
    }
});
