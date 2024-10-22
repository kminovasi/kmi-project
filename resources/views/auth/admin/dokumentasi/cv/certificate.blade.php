<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sertifikat</title>
    {{-- <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet"> --}}
    <style>
        @page {
            margin: 0;
            /* Menghilangkan margin di halaman PDF */
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'DejaVu Sans', sans-serif;
        }

        .certificate-container {
            position: relative;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .certificate-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .certificate-name {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -120%);
            text-align: center;
            color: rgb(128, 51, 0);
        }
    </style>
</head>

<body>
    <div class="certificate-container">
        <img src="{{ asset('storage/'. $data->certificate) }}" class="certificate-background"
            alt="Certificate Background">
        <div class="certificate-name">
            <h3><strong>{{ $data->employee_name }}</strong></h3>
        </div>
    </div>
</body>

</html>
