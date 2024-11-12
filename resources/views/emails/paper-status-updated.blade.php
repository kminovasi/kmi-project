<!DOCTYPE html>
<html>

<head>
    <title>Paper Approval Notification</title>
</head>

<body>
    <img src="{{ asset('assets/login-frame.png') }}" alt="Header Image" style="width: 700px; height: 210px;">

    @if ($paper->status_event == 'accept_group')
        <h2>Selamat! Paper Anda Telah Disetujui oleh Super Admin</h2>
        <p>Terlampir dalam email ini adalah makalah inovasi yang telah disetujui.</p>
    @elseif($paper->status_event == 'reject_group')
        <h2>Mohon Maaf! Paper Anda Belum Disetujui oleh Super Admin</h2>
        <p>Terlampir dalam email ini adalah makalah inovasi yang telah direject.</p>
    @endif

    <p></p>
    <div class="indented" style="padding-left: 55px;">
        <p>Judul Inovasi: {{ $paper->innovation_title }}</p>
        <p>Nama Team: {{ $paper->team->team_name }}</p>
    </div>

    <p></p>
    @if ($paper->status_event == 'accepted_group')
        <p>Selamat, paper Anda telah disetujui oleh Super Admin. Silakan untuk lanjut ke tahap berikutnya.</p>
    @elseif($paper->status_event == 'reject_group')
        <p>Maaf, paper Anda belum disetujui oleh Super Admin sehingga anda masih belum dapat lanjut ke tahap berikutnya.
        </p>
        @if ($paper->rejection_comments)
            <p>Catatan Penolakan:</p>
            <p>{{ $paper->rejection_comments }}</p>
        @endif
    @endif

    <p>Informasi lebih lanjut silakan kunjungi Portal Inovasi pada url berikut www.example.com</p>
    <p>Terimakasih</p>

    <p></p>
    <p>Hormat kami,<br>Unit KMI</p>
</body>

</html>
