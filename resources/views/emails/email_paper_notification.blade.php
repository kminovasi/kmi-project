<!DOCTYPE html>
<html>
<head>
    <title>Full Paper Uploaded Notification</title>
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
</head>
<body>
    <div class="container">
        <img src="{{ asset('assets/login-frame.jpg') }}" alt="Header Image" style="width: 700px; height: 210px;">

        @if($stage == 'full_paper')
            <h2>Makalah Full Paper telah diupload oleh Tim {{ $paper->team->team_name }}</h2>
            <p>Dengan hormat, kami mohon kepada {{ $fasilName }} sebagai fasilitator untuk dapat melakukan pemeriksaan terhadap makalah yang telah diajukan. Hal ini dikarenakan makalah tersebut membutuhkan persetujuan dari fasilitator sebelum dapat lanjut ke tahap berikutnya.</p>
            <p>Terlampir dalam email ini adalah makalah inovasi full paper yang telah diupload.</p>
                
            <p></p>
            <div class="indented">
                <p>Judul Inovasi: {{ $paper->innovation_title }}</p>
                <p>Nama Team: {{ $paper->team->team_name }}</p>
                
                @if($leaderName)
                    <p>Ketua: {{ $leaderName }}</p>
                @else
                    <p>Ketua: Tidak ada informasi</p>
                @endif

                <p>Lokasi Implementasi Inovasi: {{ $inovasi_lokasi }}</p>
                <p></p>
            </div>

            <div class="button-container">
                <a href="{{ route('paper.approve.paper', $paper->id) }}" class="button button-accept">Accept</a>
                <a href="{{ route('paper.reject.paper', $paper->id) }}" class="button button-reject">Reject</a>
            </div>
        @else
            <p>Tidak ada informasi</p>
        @endif

        <p>Informasi lebih lanjut silakan kunjungi Portal Inovasi pada url berikut www.example.com</p>
        <p>Terimakasih</p>
        
        <p></p>
        <p>Hormat kami,<br>Unit KMI</p>
    </div>
</body>
</html>
