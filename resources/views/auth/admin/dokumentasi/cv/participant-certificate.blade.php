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

        .team {
            position: absolute;
            top: 48%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .company {
            position: absolute;
            top: 52%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .result {
            position: absolute;
            top: 56.5%;
            left: 50%;
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

        .user-name, .team-name, .category, .event-result, .company-name {
            color: rgb(95, 70, 64);
        }

        .company-name {
            font-size: .75rem;
            font-weight: bold
        }

        .user-name {
            font-size: 30px;
            font-weight: bold;
        }

        .team-name {
            font-size: 1.2rem;
            font-weight: bold;
            margin-top: 5px;
        }

        .event-result {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .category {
            font-size: 18px;
            font-weight: lighter;
            font-style: italic;
        }
    </style>
</head>

<body>
    <div class="certificate-container">
        <div class="content">
            <div class="user-name">{{ $user_name }}</div>
        </div>
        <div class="team">
            <div class="team-name">{{ $team_name }}</div>
        </div>
        <div class="company">
            <div class="company-name text-capitalize">{{ $company_name }}</div> {{-- Tambahkan Variable Company Name --}}
        </div>
        <div class="result">
            @if($member_status == 'facilitator')
            <div class="event-result text-uppercase">facilitator</div>
            @elseif($team_rank <= 3)
            <div class="event-result text-uppercase">{{ 'Juara ' . $team_rank }}</div>
            @else
            <div class="event-result text-uppercase">Peserta</div>
            @endif
        </div>
        <div class="footer">
            <div class="category">{{ $category_name }}</div>
        </div>
    </div>
</body>

</html>
