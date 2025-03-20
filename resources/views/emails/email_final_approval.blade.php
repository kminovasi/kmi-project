<!DOCTYPE html>
<html>
<head>
    <title>Final Paper Approval Notification</title>

</head>
<body>
    <img src="{{ asset('assets/login-frame.jpg') }}" alt="Header Image" style="width: 700px; height: 210px;">

    @if($status == 'accept')
        <h2>Selamat! Paper Anda Telah Disetujui Secara Final oleh Pengelola Inovasi</h2>
        <p>Terlampir dalam email ini adalah makalah inovasi yang telah disetujui.</p>
    @elseif($status == 'reject')
        <h2>Mohon Maaf! Paper Anda Tidak Disetujui Secara Final oleh Pengelola Inovasi</h2>
        <p>Terlampir dalam email ini adalah makalah inovasi yang telah direject.</p>
    @elseif($status == 'replicate')
        <h2>Mohon Maaf! Paper Anda Terindikasi Replikasi oleh Pengelola Inovasi</h2>
        <p>Terlampir dalam email ini adalah makalah inovasi yang telah direplikasi.</p>
    @elseif($status == 'not complete')
        <h2>Mohon Maaf! Paper Anda Belum Disetujui Secara Final oleh Pengelola Inovasi</h2>
        <p>Terlampir dalam email ini adalah makalah inovasi yang belum lengkap.</p>
    @endif

    <p></p>
    <div class="indented" style="padding-left: 55px;">
    <p>Judul Inovasi: {{ $paper->innovation_title }}</p>
    <p>Nama Team: {{ $paper->team->team_name }}</p>
    @if($leaderName)
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
    @if($status == 'accept')
        <p>Selamat, paper Anda telah disetujui secara final oleh Pengelola Inovasi. Silakan untuk lanjut ke event.</p>
    @elseif($status == 'reject')
        <p>Maaf, paper Anda tidak disetujui secara final oleh Pengelola Inovasi sehingga anda belum dapat lanjut ke tahap berikutnya.</p>
    @elseif($status == 'replicate')
        <p>Maaf, paper Anda telah terindikasi replikasi oleh Pengelola Inovasi sehingga anda belum dapat lanjut ke tahap berikutnya.</p>
    @elseif($status == 'not complete')
        <p>Maaf, paper Anda belum lengkap dan belum disetujui secara final oleh Pengelola Inovasi.</p>
    @endif

    <p>Informasi lebih lanjut silakan kunjungi Portal Inovasi pada url berikut www.example.com</p>
    <p>Terimakasih</p>
    
    <p></p>
    <p>Hormat kami,<br>Unit KMI</p>
</body>
</html>