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

        .team {
            position: absolute;
            top: 48%;
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

        .user-name {
            font-size: 30px;
            font-weight: bold;
            color: rgb(95, 70, 64);

        }

        .team-name {
            font-size: 20px;
            font-weight: bold;
            margin-top: 5px;
            color: rgb(95, 70, 64);
        }


        .category {
            font-size: 18px;
            font-weight: lighter;
            font-style: italic;
            color: rgb(95, 70, 64);
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
        <div class="footer">
            <div class="category">{{ $category }}</div>
        </div>
    </div>
</body>

</html>
