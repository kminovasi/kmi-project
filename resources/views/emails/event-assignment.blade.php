<!DOCTYPE html>
<html>

<head>
    <title>Notifikasi Penugasan Event</title>
</head>

<body>
    <img src="{{ asset('assets/login-frame.png') }}" alt="Header Image" style="width: 700px; height: 210px;">

    <h2>Halo {{ $userName }},</h2>

    <p></p>
    <div class="indented" style="padding-left: 55px;">
        <p>Team Anda "{{ $teamName }}" telah ditugaskan untuk berpartisipasi dalam event:</p>
        <p>Event: {{ $eventName }}</p>
    </div>

    <p></p>
    <p>Mohon untuk mempersiapkan diri dalam mengikuti event tersebut.</p>

    <p>Informasi lebih lanjut silakan kunjungi Portal Inovasi pada url berikut www.example.com</p>
    <p>Terimakasih</p>

    <p></p>
    <p>Hormat kami,<br>Unit KMI</p>
</body>

</html>
