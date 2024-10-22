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

        body {
            font-family: Arial, sans-serif;
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
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .user-name {
            font-size: 40px;
            font-weight: bold;
        }

        .team-name,
        .category {
            font-size: 20px;
        }
    </style>
</head>

<body>
    <div class="certificate-container">
        <div class="content">
            <div class="user-name">{{ $user_name }}</div>
            <div class="team-name">{{ $team_name }}</div>
            <div class="category">{{ $category }}</div>
        </div>
    </div>
</body>

</html>
