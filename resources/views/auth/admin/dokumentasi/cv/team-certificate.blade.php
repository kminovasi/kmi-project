<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate</title>
    <style>
        @page {
            margin: 0;
            /* Menghilangkan margin di halaman PDF */
        }

        @font-face {
            font-family: 'Open Sans';
            src: url('{{ public_path(' assets/fonts/Open_Sans/OpenSans-Regular.ttf') }}') format('truetype');
            font-weight: normal;
        }

        body {
            font-family: 'Open Sans', sans-serif;
        }

        .certificate-container {
            position: relative;
            width: 100%;
            height: 100%;
            background-image: url('{{ storage_path("app/public/".$template_path) }}');
            background-size: cover;
        }

        .content {
            position: absolute;
            top: 44%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .title {
            position: absolute;
            top: 49%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .company {
            position: absolute;
            top: 55%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .rank {
            position: absolute;
            top: 25%;
            left: 80%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .footer {
            position: absolute;
            top: 64%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .team-name, .innovation-title, .category, .team-rank, .company-name {
            color: rgb(95, 70, 64);
        }

        .team-name {
            font-size: 30px;
            font-weight: bold;
        }

        .innovation-title {
            font-size: .7rem;
            font-weight: 400;
            margin-top: 5px;
        }

        .team-rank {
            font-size: 4rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        .category {
            font-size: 18px;
            font-weight: bold;
            font-style: italic;
        }
    </style>
</head>

<body>
    <div class="certificate-container">
        <div class="content">
            <div class="team-name">{{ $team_name }}</div>
        </div>
        <div class="title">
            <div class="innovation-title">{{ $innovation_title }}</div>
        </div>
        <div class="company">
            <div class="company-name fw-600 text-capitalize">{{ $company_name }}</div>
        </div>
        <div class="footer">
            <div class="category text-capitalize">{{ $category_name }}</div>
        </div>
        <div class="rank">
            <div class="team-rank text-uppercase">{{ $team_rank }}</div> {{-- Tambahkan Variable Status Peserta --}}
        </div>
    </div>
</body>

</html>
