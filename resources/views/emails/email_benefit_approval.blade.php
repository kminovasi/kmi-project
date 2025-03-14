<!DOCTYPE html>
<html>

<head>
    <title>Benefit Approval Notification</title>

    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style>
</head>

<body>
    <img src="{{ asset('assets/login-frame.jpg') }}" alt="Header Image" style="width: 700px; height: 210px;">

    @if ($status == 'accepted benefit by facilitator')
        <h2>Selamat! Benefit Anda Telah Disetujui oleh Fasilitator</h2>
        <p>Terlampir dalam email ini adalah keterangan benefit yang telah disetujui.</p>
    @elseif($status == 'rejected benefit by facilitator')
        <h2>Mohon Maaf! Benefit Anda Belum Disetujui oleh Fasilitator</h2>
        <p>Terlampir dalam email ini adalah keterangan benefit yang telah direject.</p>
    @elseif($status == 'revision benefit by facilitator')
        <h2>Mohon Maaf! Benefit Anda mendapatkan revisi dari Fasilitator</h2>
        <p>Terlampir dalam email ini adalah keterangan benefit yang telah di revisi.</p>
    @elseif($status == 'accepted benefit by general manager')
        <h2>Selamat! Benefit Anda Telah Disetujui oleh General Manager (GM)</h2>
        <p>Terlampir dalam email ini adalah keterangan benefit yang telah disetujui.</p>
    @elseif($status == 'rejected benefit by general manager')
        <h2>Mohon Maaf! Benefit Anda Belum Disetujui oleh General Manager (GM)</h2>
        <p>Terlampir dalam email ini adalah keterangan benefit yang telah direject.</p>
    @elseif($status == 'revision benefit by general manager')
        <h2>Mohon Maaf! Benefit Anda mendapatkan revisi dari General Manager (GM)</h2>
        <p>Terlampir dalam email ini adalah keterangan benefit yang telah revisi.</p>
    @endif

    <p></p>
    <div class="indented" style="padding-left: 55px;">
        <p>Judul Inovasi: {{ $paper->innovation_title }}</p>
        <p>Nama Team: {{ $paper->team->team_name }}</p>
        @if ($leaderName)
            <p>Ketua: {{ $leaderName }}</p>
        @else
            <p>Ketua: Tidak ada informasi</p>
        @endif
        <p>Lokasi Implementasi Inovasi: {{ $inovasi_lokasi->inovasi_lokasi }}</p>
        <p>Benefit Financial (Real): {{ $benefitFinancial }}</p>
        <p>Benefit Potential: {{ $benefitPotential }}</p>
        <p>Potensi Replikasi: {{ $potensiReplikasi }}</p>
        <p>Benefit Non Financial : {{ $benefitNonFinancial }}</p>
    </div>

    <p></p>
    @if ($status == 'accepted benefit by facilitator')
        <p>Selamat, benefit Anda telah disetujui oleh Fasilitator. Silakan untuk lanjut ke tahap berikutnya.</p>
    @elseif($status == 'rejected benefit by facilitator')
        <p>Maaf, benefit Anda belum disetujui oleh Fasilitator sehingga anda masih belum dapat lanjut ke tahap
            berikutnya.</p>
    @elseif($status == 'revision benefit by facilitator')
        <p>Maaf, benefit Anda mendapatkan revisi dari Fasilitator sehingga anda masih belum dapat lanjut ke tahap
            berikutnya, Mohon di cek komentar dari fasilitator</p>
    @elseif($status == 'accepted benefit by general manager')
        <p>Selamat, benefit Anda telah disetujui oleh General Manager (GM). Silakan untuk lanjut ke tahap berikutnya.
        </p>
    @elseif($status == 'revision benefit by general manager')
        <p>Maaf, benefit Anda mendapatkan revisi dari General Manager (GM). sehingga anda masih belum dapat lanjut ke
            tahap
            berikutnya, Mohon di cek komentar dari General Manager.
        </p>
    @elseif($status == 'rejected benefit by general manager')
        <p>Maaf, benefit Anda belum disetujui oleh General Manager (GM) sehingga anda masih belum dapat lanjut ke tahap
            berikutnya.</p>
    @endif

    <p>Informasi lebih lanjut silakan kunjungi Portal Inovasi pada url berikut www.example.com</p>
    <p>Terimakasih</p>

    <p></p>
    <p>Hormat kami,<br>Unit KMI</p>
</body>

</html>
