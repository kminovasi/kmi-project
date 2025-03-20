<!DOCTYPE html>
<html>

<head>
    <title>Paper Approval Notification</title>
</head>

<body>
    <img src="{{ asset('assets/login-frame.jpg') }}" alt="Header Image" style="width: 700px; height: 210px;">

    @if ($status == 'accepted paper by facilitator')
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
        @if ($leaderName)
            <p>Ketua: {{ $leaderName }}</p>
        @else
            <p>Ketua: Tidak ada informasi</p>
        @endif
        <p>Lokasi Implementasi Inovasi: {{ $paper->inovasi_lokasi }}</p>
    </div>

    <p></p>
    @if ($status == 'accepted paper by facilitator')
        <p>Selamat, paper Anda telah disetujui oleh Fasilitator. Silakan untuk lanjut ke tahap berikutnya.</p>
        <p>Informasi lebih lanjut silakan kunjungi Portal Inovasi pada url berikut www.example.com</p>
        <p>Terimakasih</p>
        <p>Anda bisa download makalah full paper <a href="{{ $fileUrl }}">disini</a>.</p>
    @elseif($status == 'rejected paper by facilitator')
        <p>Maaf, paper Anda belum disetujui oleh Fasilitator sehingga Anda masih belum dapat lanjut ke tahap berikutnya.
        </p>
        <p>Informasi lebih lanjut silakan kunjungi Portal Inovasi pada url berikut www.example.com</p>
    <p>Terimakasih</p>
    @elseif($status == 'revision paper by facilitator')
        <p>Paper Anda memerlukan revisi sesuai dengan masukan dari Fasilitator. Mohon untuk memperhatikan langkah revisi
            yang telah diberikan dan mengunggah ulang paper Anda setelah revisi selesai dilakukan.</p>
        <p>Informasi lebih lanjut silakan kunjungi Portal Inovasi pada url berikut www.example.com</p>
        <p>Terimakasih</p>
    @endif

    <p>Hormat kami,<br>Unit KMI</p>
</body>

</html>
