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

        .judge-company {
            position: absolute;
            top: 48.5%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .event {
            position: absolute;
            top: 55%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .event-role {
            position: absolute;
            top: 63%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .user-name, .company-name, .judge {
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

        .event-name {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .judge {
            font-size: 2rem;
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
        <div class="event">
            <div class="event-name">{{ $event_name }}</div>
        </div>
        <div class="event-role">
            <div class="judge">Tim Juri</div> {{-- Sudah Benar Untuk Tim Juri --}}
        </div>
    </div>
</body>

</html>
