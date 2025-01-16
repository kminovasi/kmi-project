import ExcelJS from 'exceljs';
import jsPDF from 'jspdf';
import html2canvas from 'html2canvas';

document.addEventListener("DOMContentLoaded", function () {
    const exportExcelButton = document.querySelector('.export-excel-totalInnovatorWithGender');
    const exportPdfButton = document.querySelector('.export-pdf-totalInnovatorWithGender');

    if (exportExcelButton) {
        console.log('ok')
        exportExcelButton.addEventListener('click', async function () {
            const chartCanvas = document.getElementById('totalInnovatorWithGenderChart');
            const chartData = window.chartDataTotalInnovatorWithGenderChart;
            const companyName = window.company_name;

            if (chartData && chartCanvas) {
                const workbook = new ExcelJS.Workbook();
                const worksheet = workbook.addWorksheet('Data Innovator');

                // Tambahkan header
                const headers = ['Tahun', 'Laki-laki', 'Perempuan', 'Total'];
                worksheet.addRow(headers);

                // Tambahkan data ke worksheet
                Object.entries(chartData).forEach(([year, data]) => {
                    worksheet.addRow([year, data.laki_laki, data.perempuan, data.total]);
                });

                // Tambahkan chart sebagai gambar
                const chartImage = chartCanvas.toDataURL('image/png');
                const imageId = workbook.addImage({
                    base64: chartImage.split(',')[1],
                    extension: 'png',
                });

                worksheet.addImage(imageId, {
                    tl: { col: 0, row: Object.keys(chartData).length + 2 },
                    ext: { width: 500, height: 300 },
                });

                // Ekspor file
                const buffer = await workbook.xlsx.writeBuffer();
                const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                const url = window.URL.createObjectURL(blob);

                const a = document.createElement('a');
                a.href = url;
                a.download = `total_innovator_${companyName}.xlsx`;
                a.click();
                window.URL.revokeObjectURL(url);
            }
        });
    }

    if (exportPdfButton) {
        exportPdfButton.addEventListener('click', async function () {
            const chartCanvas = document.getElementById('totalInnovatorWithGenderChart');
            const chartData = window.chartDataTotalInnovatorWithGenderChart;
            const companyName = window.company_name;

            if (chartData && chartCanvas) {
                const pdf = new jsPDF();

                // Tambahkan judul
                pdf.setFontSize(18);
                pdf.text('Total Innovator per Tahun', 10, 10);

                // Tambahkan tabel
                pdf.setFontSize(12);
                const headers = ['Tahun', 'Laki-laki', 'Perempuan', 'Total'];
                let startY = 20;

                headers.forEach((header, index) => {
                    pdf.text(header, 10 + index * 40, startY);
                });

                startY += 10;
                Object.entries(chartData).forEach(([year, data]) => {
                    pdf.text(year, 10, startY);
                    pdf.text(data.laki_laki.toString(), 50, startY);
                    pdf.text(data.perempuan.toString(), 90, startY);
                    pdf.text(data.total.toString(), 130, startY);
                    startY += 10;
                });

                // Tambahkan chart ke PDF
                html2canvas(chartCanvas).then(canvas => {
                    const imgData = canvas.toDataURL('image/png');
                    pdf.addImage(imgData, 'PNG', 10, startY, 180, 100);

                    // Simpan PDF
                    pdf.save(`total_innovator_${companyName}.pdf`);
                });
            }
        });
    }
});
