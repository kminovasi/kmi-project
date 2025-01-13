import ExcelJS from 'exceljs';

document.addEventListener("DOMContentLoaded", function () {
    const exportButtons = document.querySelectorAll('.export-excel');

    exportButtons.forEach(button => {
        button.addEventListener('click', async function () {
            const companyId = this.getAttribute('data-company-id');
            const companyName = this.closest('.card').querySelector('.card-title').textContent.trim();
            const chartCanvas = document.querySelector(`#innovatorChart_${companyId}`);
            const chartData = window.chartData[`innovatorChart_${companyId}`];

            if (chartData && chartCanvas) {
                // Buat workbook baru
                const workbook = new ExcelJS.Workbook();
                const worksheet = workbook.addWorksheet('Data');

                // Tambahkan data
                const headers = ['Kategori', 'Total Innovator'];
                worksheet.addRow(headers);

                chartData.labels.forEach((label, index) => {
                    worksheet.addRow([label, chartData.datasets[0].data[index]]);
                });

                // Konversi chart ke gambar
                const chartImage = chartCanvas.toDataURL('image/png');

                // Tambahkan gambar ke worksheet
                const imageId = workbook.addImage({
                    base64: chartImage.split(',')[1],
                    extension: 'png',
                });

                // Posisikan gambar di bawah data
                worksheet.addImage(imageId, {
                    tl: { col: 0, row: chartData.labels.length + 2 },
                    ext: { width: 500, height: 300 }
                });

                // Ekspor file
                const buffer = await workbook.xlsx.writeBuffer();
                const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                const url = window.URL.createObjectURL(blob);

                const a = document.createElement('a');
                a.href = url;
                a.download = `total_innovator_per_kategori_${companyName}.xlsx`;
                a.click();
                window.URL.revokeObjectURL(url);
            }
        });
    });
});
