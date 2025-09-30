<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Certificate</title>
  <style>
  @page { margin: 0; }

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
    
      .certificate-container{
        position:relative; width:100%; height:100%;
        background-image:url('{{ storage_path("app/public/".$template_path) }}');
        background-size:cover;
      }

  .content,
  .team,
  .company,
  .user-role,
  .result,
  .event-label,
  .event-name,
  .date-footer-container,
  .company-footer-container{
    width:78%;
    max-width:920px;
  }

  .header-gived-to{
    position:absolute; top:35.5%; left:50%;
    transform:translate(-50%,-50%); text-align:center;
    font-size:1rem; color:#242120;
    font-weight:400;
  }

  .content{
    position:absolute; top:40%; left:50%;
    transform:translate(-50%,-50%); text-align:center;
  }

  .name-rule{
    margin:8px auto 0; height:0; 
    width:min(540px,64%);
  }

  .team{
    position:absolute; top:47%; left:50%;
    transform:translate(-50%,-50%); text-align:center;
  }

  .company{
    position:absolute; top:49.5%; left:50%;
    transform:translate(-50%,-50%); text-align:center;
  }

  .user-role{
    position:absolute; top:54%; left:50%;
    font-size:1.0rem; transform:translate(-50%,-50%);
    text-align:center; color:#242120;
  }
  
  .judge-title{
  position:absolute; left:50%;
  transform:translate(-50%, -50%);   
  top: 46.5%;                        
  
  font-size: 1.05rem;
  font-weight: 700;
  color:#2f2f2f;
  letter-spacing:.2px;
}

  .result{
    position:absolute; top:57%; left:50%;
    transform:translate(-50%,-50%); text-align:center;
  }


    .event-label{
      position:absolute; left:50%; top:62%;
      top:61.5%;    
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
      top:63.5%;                       
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


  .date-footer-container{
    position:absolute; top:69.5%; left:50%;
    transform:translate(-50%,-50%); text-align:center; color:#242120;
  }

  .company-footer-container{
    position:absolute; top:72%; left:50%;
    transform:translate(-50%,-50%); text-align:center; color:#242120;
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
        
  .user-name{ font-size:34px; font-weight:bold; line-height:1.2; word-wrap:break-word; overflow-wrap:break-word; }
  .company-head{ font-size:.9rem; margin-top:5px; }
  .company-name{ font-size:.9rem; font-weight:400; letter-spacing:1px; word-wrap:break-word; overflow-wrap:break-word; }
  .event-result{ font-size:37px; font-weight:700; text-transform:uppercase; margin-top:9px; color:#c82127 !important;}
  .category{ font-size:18px; font-weight:bold; font-style:italic; text-transform:capitalize; }
  .company-footer, .date-footer{ font-size:.7rem; font-weight:lighter; letter-spacing:1px; }

</style>

</head>
<body>
  <div class="certificate-container">
    <div class="header-gived-to uppercase">DIBERIKAN KEPADA</div>

    <div class="content">
      <div class="user-name mt-0">{{ $user_name }}</div>
      <!--<div class="name-rule"></div>-->
    </div>

    <div class="judge-title">
      {{ trim($position_title ?? '') !== '' ? $position_title : '-' }}
    </div>


    <div class="company">
      <div class="company-name text-capitalize">
        {{ str_replace([',', '.'], ' ', $company_name) }}
      </div>
    </div>

    <div class="user-role">SEBAGAI</div>

    <div class="result">
      <div class="event-result text-uppercase" style="color:red;">Juri</div>
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

    <div class="bod-name">{{ mb_convert_case(trim($bodName ?? ''), MB_CASE_TITLE, 'UTF-8') }}</div>
    <div class="bod-title">{{ $bodTitle }}</div> 
    <div class="company-bod">PT Semen Indonesia (Persero) Tbk</div>
  </div>
</body>
</html>
