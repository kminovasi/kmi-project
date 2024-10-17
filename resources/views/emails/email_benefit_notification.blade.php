<!DOCTYPE html>
<html>
<head>
    <title>Benefit Uploaded Notification</title>
</head>
<style>
        body {
            text-align: center;
            font-family: Arial, sans-serif;
        }
        .container {
            display: inline-block;
            text-align: left;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
        .indented {
            padding-left: 55px;
            text-align: left;
        }
        .button-container {
            text-align: center;
            margin-top: 20px;
            align-items:center;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .button-accept {
            background-color: #4CAF50;
            color: #ffffff;
        }
        .button-reject {
            background-color: #ff0000;
            color: #ffffff;
        }
    </style>
<body>
<div class="container">
    <img src="{{ asset('assets/login-frame.png') }}" alt="Header Image" style="width: 700px; height: 210px;">

    @if($status == 'upload benefit')
        <h2>Benefit telah diupload oleh Tim {{ $record->team->team_name }}</h2>
        <p>Dengan hormat, kami mohon kepada {{ $fasilName }} sebagai fasilitator untuk dapat melakukan pemeriksaan terhadap benefit yang telah diajukan hal ini dikarenakan benefit membutuhkan persetujuan dari fasilitator sebelum dapat lanjut ke tahap berikutnya.</p>
        <p>Terlampir dalam email ini adalah benefit yang telah diajukan.</p>
    @elseif($status == 'accepted benefit by facilitator')
        <h2>Benefit telah diupload oleh Tim {{ $paper->team->team_name }}</h2>
        <p>Dengan hormat, kami mohon kepada {{ $gmName }} selaku General Manager (GM) untuk dapat melakukan pemeriksaan terhadap benefit yang telah diajukan hal ini dikarenakan benefit membutuhkan persetujuan dari GM sebelum dapat lanjut ke tahap berikutnya.</p>
        <p>Terlampir dalam email ini adalah benefit yang telah diajukan dan disetujui oleh fasilitator.</p>
    @endif

    @if($status == 'upload benefit')
        <p></p>
        <div class="indented">
            <p>Judul Inovasi: {{ $record->innovation_title }}</p>
            <p>Nama Team: {{ $record->team->team_name }}</p>
            @if($leaderName)
                <p>Ketua: {{ $leaderName }}</p>
            @else
                <p>Ketua: Tidak ada informasi</p>
            @endif
            <p>Lokasi Implementasi Inovasi: {{ $inovasi_lokasi }}</p>
            <p>Benefit Financial (Real): {{ $financial }}</p>
            <p>Benefit Potential: {{ $potential_benefit }}</p>
            <p>Potensi Replikasi: {{ $potensi_replikasi }}</p>
            <p>Benefit Non Financial : {{ $non_financial }}</p>

        </div>

    @elseif($status == 'accepted benefit by facilitator')
        <p></p>
        <div class="indented">
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
    @endif

    @if($status == 'upload benefit')
        <div class="button-container">
            <a href="{{ route('paper.approve.benefit', $record->id) }}" class="button button-accept">Accept</a>
            <a href="{{ route('paper.reject.benefit', $record->id) }}" class="button button-reject">Reject</a>
        </div>
    @elseif($status == 'accepted benefit by facilitator')
        <div class="button-container">
            <a href="{{ route('paper.approve.benefitGM', $paper->id) }}" class="button button-accept">Accept</a>
            <a href="{{ route('paper.reject.benefitGM', $paper->id) }}" class="button button-reject">Reject</a>
        </div>
    @endif


    <p>Informasi lebih lanjut silakan kunjungi Portal Inovasi pada url berikut www.example.com</p>
    <p>Terimakasih</p>

    <p></p>
    <p>Hormat kami,<br>Unit KMI</p>
    </div>
</body>
</html>
