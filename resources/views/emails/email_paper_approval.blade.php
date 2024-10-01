<!DOCTYPE html>
<html>
<head>
    <title>Paper Approval Notification</title>
</head>
<body>
    <img src="{{ asset('assets/login-frame.png') }}" alt="Header Image" style="width: 700px; height: 210px;">

    @if($status == 'accepted paper by facilitator')
        <h2>Selamat! Paper Anda Telah Disetujui oleh Fasilitator</h2>
        <p>Terlampir dalam email ini adalah makalah inovasi yang telah disetujui.</p>
    @else
        <h2>Mohon Maaf! Paper Anda Belum Disetujui oleh Fasilitator</h2>
        <p>Terlampir dalam email ini adalah makalah inovasi yang telah direject.</p>
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
    </div>

    <p></p>
    @if($status == 'accepted paper by facilitator')
        <p>Selamat, paper Anda telah disetujui oleh Fasilitator. Silakan untuk lanjut ke tahap berikutnya.</p>
    @elseif($status == 'rejected paper by facilitator')
        <p>Maaf, paper Anda belum disetujui oleh Fasilitator sehingga anda masih belum dapat lanjut ke tahap berikutnya.</p>
    @endif

    <p>Informasi lebih lanjut silakan kunjungi Portal Inovasi pada url berikut www.example.com</p>
    <p>Terimakasih</p>
    
    <p></p>
    <p>Hormat kami,<br>Unit KMI</p>
</body>
</html>