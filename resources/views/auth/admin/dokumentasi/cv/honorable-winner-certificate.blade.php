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
        
        .header-gived-to {
            position: absolute;
            top: 39%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            font-size: 1rem;
            color: #483C36;
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
            top: 54%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: 2px solid #E2CF97;
            width: 22rem;
            padding: .3rem;
            text-align: center;
            border-radius: 4px;
            padding-top: 5px;
            padding-bottom: 5px;
        }

        .rank {
            position: absolute;
            top: 78%;
            left: 78%;
            transform: translate(-50%, -50%);
            text-align: center;
        }
        
        .nominated-as-head {
            position: absolute;
            top: 60%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            font-weight: bold;
            color: #483C36;
            font-size: .9rem;
        }

        .nominated-container {
            position: absolute;
            top: 64%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .team-name, .innovation-title, .team-rank, .company-name {
            color: #6E5948;
        }
        
        .company-name {
            font-size: 1rem;
            font-weight: bolder;
            letter-spacing: 2px;
        }

        .team-name {
            font-size: 34px;
            font-weight: bold;
        }

        .innovation-title {
            font-size: .7rem;
            font-weight: 400;
            margin-top: 5px;
        }

        .nominated-as p {
            font-size: 2.5rem;
            font-weight: bold;
            font-style: italic;
            display: inline-block;
            color: #00c9a7;
        }

        .bod-name {
            position: absolute;
            top: 84.6%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: #483C36; 
            font-weight: bold;
        }
        
        .bod-title {
            position: absolute;
            top: 88.6%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: #483C36;
        }
        
        .company-footer-container {
            position: absolute;
            top: 72%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: #483C36;
        }

        .date-footer-container {
            position: absolute;
            top: 70%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: #483C36;
        }
        
        .company-footer, .date-footer {
            font-size: .7rem;
            font-weight: lighter;
            letter-spacing: 1px;
        }
    </style>
</head>

<body>
    <div class="certificate-container">
        <div class="header-gived-to uppercase">DIBERIKAN KEPADA TIM</div>
        <div class="content">
            <div class="team-name">{{ $team_name }}</div>
        </div>
        <div class="title">
            <div class="innovation-title">{{ $innovation_title }}</div>
        </div>
        <div class="company">
            <div class="company-name fw-600 text-capitalize">
                {{ str_replace([',', '.'], ' ', $company_name) }}
            </div>
        </div>
        <div class="nominated-as-head">Sebagai</div>
        <div class="nominated-container">
            <div class="nominated-as">
                <p>Juara Harapan</p>
            </div>
        </div>
        <div class="date-footer-container">
            <div class="date-footer">Pada Tanggal {{ \Carbon\Carbon::parse($event_end_date)->format('d F Y') }}</div>
        </div>
        
        <div class="company-footer-container">
            <div class="company-footer">
                {{ str_replace([',', '.'], ' ', $company_name) }}
            </div>
        </div>
        
        <div class="bod-name">{{ $bodName }}</div>
        
        <div class="bod-title">{{ $bodTitle }}</div>
    </div>
</body>

</html>
