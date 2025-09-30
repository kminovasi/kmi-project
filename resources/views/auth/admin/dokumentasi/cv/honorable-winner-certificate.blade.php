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
        
        body{ font-family:'Proxima Nova','ProximaNova',Arial,sans-serif !important; }

        .certificate-container {
            position: relative;
            width: 100%;
            height: 100%;
            /* background-image: url('{{ storage_path("app/public/".$template_path) }}'); */
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
            color: #FAEF41;
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
            top: 47%;
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
            top: 52.7%;
            left: 49.8%;
            transform: translate(-50%, -50%);
            text-align: center;
            font-weight: 400;
            color: #FAEF41;
            font-size: .9rem;
        }

        .nominated-container {
            position: absolute;
            top: 56%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .team-name, .innovation-title, .team-rank, .company-name {
            color: #FFFFFF;
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
            color: #8AD6F1 !important;
        }
        
         .event-label{
          position:absolute; left:50%; top:62%;
          top:62%;    
          transform:translate(-50%,-50%);
          width:78%; max-width:920px;
          text-align:center;
          font-family:'Proxima Nova','ProximaNova',Arial,sans-serif;
          font-weight:400;                 
          font-size:.8rem;
          letter-spacing:2px;              
          color:#FAEF41;                   
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
          color:#F3F8F5;                    
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
            color: #F3F8F5;
        }

        .date-footer-container {
            position: absolute;
            top: 70%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: #F3F8F5;
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
             color:#F3F8F5;       
            font-weight:700;
    }
        
    .bod-title {
            position: absolute;
            top: 87.5%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
             color:#F3F8F5;    
            

    }
    
    .company-bod{
        position:absolute; top:89.5%; left:50%;
        transform:translate(-50%,-50%);
        text-align:center; color:#242120;
        font-size:.85rem; letter-spacing:.4px;
        max-width:78%; width:78%;
        color:#F3F8F5;    
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
        <!--<div class="company">-->
        <!--    <div class="company-name fw-600 text-capitalize">-->
        <!--        {{ str_replace([',', '.'], ' ', $company_name) }}-->
        <!--    </div>-->
        <!--</div>-->
        <div class="nominated-as-head">SEBAGAI</div>
        <div class="nominated-container">
            <div class="nominated-as">
                <p>HONOURABLE MENTION</p>
            </div>
        </div>
        <div class="event-label">DALAM KEGIATAN</div>
        <div class="event-name">
          {{ $event_name ? strtoupper(trim($event_name.' '.($year ?? ''))) : '-' }}
        </div>
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
