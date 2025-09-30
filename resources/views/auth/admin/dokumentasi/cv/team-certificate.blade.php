<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate</title>
    <style>
        @page {
            margin: 0;
        }
        
        @font-face{
          font-family:'Proxima Nova';
          src:url('file://{{ public_path('assets/fonts/ProximaNova/ProximaNova-Regular.ttf') }}') format('truetype');
          font-weight:400; font-style:normal;
        }
        @font-face{
          font-family:'Proxima Nova';
          src:url('file://{{ public_path('assets/fonts/ProximaNova/ProximaNova-Bold.ttf') }}') format('truetype');
          font-weight:700; font-style:normal;
        }
        /* >>> Tambahkan ini <<< */
        @font-face{
          font-family:'Proxima Nova';
          src:url('file://{{ public_path('assets/fonts/ProximaNova/ProximaNova-Italic.ttf') }}') format('truetype');
          font-weight:400; font-style:italic;
        }
        @font-face{
          font-family:'Proxima Nova';
          src:url('file://{{ public_path('assets/fonts/ProximaNova/ProximaNova-BoldItalic.ttf') }}') format('truetype');
          font-weight:700; font-style:italic;
        }

        body{ font-family:'Proxima Nova', Arial, sans-serif !important; }

        .certificate-container {
            position:relative; width:100%; height:100%;
            width: 100%;
            height: 100%;
            background-image: url('{{ $bg_abs }}');
            background-size: cover;
        }
        
        .header-gived-to {
            position: absolute;
            top: 35.5%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            font-size: 1rem;
            color: #483C36;
        }

        .content {
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .title {
            position: absolute;
            top: 44%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }
        
        .company{
            position:absolute; top:52%; left:50%;
            transform:translate(-50%,-50%); text-align:center;
            font-weight:400 !important;
          }

        .rank{
          position:absolute; top:72%; left:78%;
          transform:translate(-50%, -50%);
          text-align:center;
        }
        .rank-title{
          position:absolute; top:61%; left:78%;
          transform:translate(-50%, -50%);
          text-align:center;
          font-weight:400;
          font-size:.92rem;
          letter-spacing:.5px;
        }

        .category-head {
            position: absolute;
            top: 56%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            font-weight: 400;
            color: #483C36;
            font-size: .9rem;
        }

        .footer {
            position: absolute;
            top: 59%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            font-weight: 800;
        }

        .team-name, .innovation-title, .category, .team-rank {
            color: #6E5948;
        }
        
        .company-name {
            font-size: 1rem;
            font-weight: 400;
            letter-spacing: 2px;
        }

        .team-name {
            font-size: 34px;
            font-weight: bold;
        }

        .innovation-title {
            font-size: .7rem;
            font-weight: 400;
            margin-top: 3px;
        }

        .category {
            font-size: 16px;
           font-weight: 700 !important;
        }
            
        .event-label{
          position:absolute; left:50%; top:62%;
          top:62.5%;    
          transform:translate(-50%,-50%);
          width:78%; max-width:920px;
          text-align:center;
          font-family:'Proxima Nova','ProximaNova',Arial,sans-serif;
          font-weight:400;                 
          font-size:.9rem;
          letter-spacing:2px;              
          color:#2f2f2f;                   
          text-transform:uppercase;
        }
        
        .event-name{
          position:absolute;
          left:0; right:0;               
          top:64%;                       
          width:78%; max-width:920px;
          margin:0 auto;
          text-align:center;
          font-family:'Proxima Nova','ProximaNova',Arial,sans-serif;
          font-weight:700;
          font-size:1.2rem;
          letter-spacing:.6px;
          color:#222;                    
          text-transform:uppercase;
          line-height:1.25;
          z-index:11;                    
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
            top: 69.5%;
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
        
         .bod-name {
            position: absolute;
            top: 84.5%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color:#222;      
            font-weight:700;
    }
        
    .bod-title {
            position: absolute;
            top: 87.5%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: #242120;

    }
    
    .company-bod{
        position:absolute; top:89.5%; left:50%;
        transform:translate(-50%,-50%);
        text-align:center; color:#242120;
        font-size:.85rem; letter-spacing:.4px;
        max-width:78%; width:78%;
       
      }
        
        .certificate-container, .certificate-container *{ color:#000 !important; }
         
        .content         { top: 40% !important; } 
        .title           { top: 45.7% !important; }  
        

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
          <div class="company-name text-capitalize">
            {{ str_replace([',', '.'], ' ', $company_name) }}
          </div>
        </div>
        
        <div class="category-head">KATEGORI</div>
        <div class="footer">
            <div class="category">{{ $category_name }}</div>
        </div>
        
        <div class="event-label">DALAM KEGIATAN</div>
        <div class="event-name">
          {{ $event_name ? strtoupper(trim($event_name.' '.($year ?? ''))) : '-' }}
        </div>
        
         @php
          $badgeMap = [1 => $badge1_abs ?? null, 2 => $badge2_abs ?? null, 3 => $badge3_abs ?? null];
          $badgeAbs = $badgeMap[(int)($team_rank ?? 0)] ?? null;
        @endphp
    
        @if ($badgeAbs && file_exists($badgeAbs))
          <div class="rank">
            <img src="file://{{ $badgeAbs }}" width="200" alt="Badge Juara">
          </div>
          <div class="rank-title">SEBAGAI JUARA</div>
        @endif
        <div class="date-footer-container">
            <div class="date-footer">Pada tanggal {{ ($certificate_date ?? $event_end_date) ? \Carbon\Carbon::parse($certificate_date ?? $event_end_date)->locale('id')->isoFormat('D MMMM YYYY') : '-' }}</div>
        </div>
        
        <div class="company-footer-container">
         <div class="company-footer">PT Semen Indonesia (Persero) Tbk</div>
        </div>
        
          <div class="bod-name">{{ $bodName }}</div>
        
        <div class="bod-title">{{ $bodTitle }}</div>
        <div class="company-bod">PT Semen Indonesia (Persero) Tbk</div>

    </div>
</body>

</html>
