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
            font-family: 'Open Sansf', sans-serif;
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
            top: 43%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .team {
            position: absolute;
            top: 48.5%;
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
        
        .user-role {
            position: absolute;
            top: 54%;
            left: 50%;
            font-size: 1rem;
            transform: translate(-50%, -50%);
            text-align: center;
            color: #483C36;
        }

        .result {
            position: absolute;
            top: 56.5%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }
        
        .category-head {
            position: absolute;
            top: 60%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            font-weight: lighter;
            color: #483C36;
        }

        .category-container {
            position: absolute;
            top: 63%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: 2px solid #E2CF97;
            width: 26rem;
            padding: .3rem;
            text-align: center;
            border-radius: 4px;
            padding-top: 5px;
            padding-bottom: 5px;
        }
        
        .company-footer-container {
            position: absolute;
            top: 71%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: #483C36;
        }

        .date-footer-container {
            position: absolute;
            top: 69%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: #483C36;
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
        
        .user-name, .team-name, .category, .event-result, .company-name {
            color: #6E5948;
        }

        .company-name {
            font-size: .8rem;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .user-name {
            font-size: 34px;
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
            font-weight: bold;
            font-style: italic;
            text-transform: capitalize;
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
        <div class="header-gived-to uppercase">DIBERIKAN KEPADA</div>
        
        <div class="content">
            <div class="user-name mt-0">{{ $user_name }}</div>
        </div>
        
        <div class="team">
            <div class="team-name">{{ $team_name }}</div>
        </div>
        
        <div class="company">
            <div class="company-name text-capitalize">
                {{ str_replace([',', '.'], ' ', $company_name) }}
            </div>
        </div>
        
        <div class="user-role">SEBAGAI</div>
        
        <div class="result">
            <div class="event-result text-uppercase">PESERTA</div>
        </div>
        
        <div class="category-head">KATEGORI</div>
        
        <div class="category-container">
            <div class="category text-capitalize">{{ $category_name }}</div>
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
