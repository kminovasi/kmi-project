<!DOCTYPE html>
<html>
<head>
    <title>Request Final Approval Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body>
    <img src="{{ asset('assets/login-frame.jpg') }}" alt="Header Image" style="width: 700px; height: 210px;">

    @if($status == 'accepted benefit by general manager')
        <h2>Request Final Approval Tim {{ $paper->team->team_name }}</h2>
        
        <p></p>
        @if($status == 'accepted benefit by general manager')
        <p>Dengan hormat, kami mohon kepada {{ $adminName }} untuk dapat melakukan pemeriksaan terhadap makalah dan benefit yang telah diajukan dan disetujui oleh Fasilitator dan GM, hal ini dikarenakan tim membutuhkan persetujuan secara final dari Admin.</p>
        @endif

        <p>Terlampir dalam email ini adalah makalah inovasi dan Berita Acara Benefit yang telah disetujui oleh Fasilitator dan General Manager (GM).</p>
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

    <!-- <p></p>
    @if($status == 'accepted benefit by general manager')
    <p>Dengan hormat, kami mohon kepada {{ $adminName }} untuk dapat melakukan pemeriksaan terhadap makalah dan benefit yang telah diajukan dan disetujui oleh Fasilitator dan GM, hal ini dikarenakan tim membutuhkan persetujuan secara final dari Admin.</p>
    @endif -->

    <p>Informasi lebih lanjut silakan kunjungi Portal Inovasi pada url berikut www.example.com</p>
    <p>Terimakasih</p>
    
    <p></p>
    <p>Hormat kami,<br>Unit KMI</p>
</body>
</html>