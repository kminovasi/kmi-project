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
            top: 43%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .team, .judge-company {
            position: absolute;
            top: 48%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .company {
            position: absolute;
            top: 51%;
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

        .user-name {
            font-size: 30px;
            font-weight: bold;
        }

        .team-name {
            font-size: 20px;
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
        <div class="judge-company">
            <div class="company-name fw-400 text-capitalize">{{ $company_name }}</div>
        </div>
        <div class="event-role">
            <div class="judge">Tim Juri</div> {{-- Sudah Benar Untuk Tim Juri --}}
        </div>
        <div class="footer">
            <div class="event">{{ $event_name }}</div>
        </div>
    </div>
</body>

</html>
