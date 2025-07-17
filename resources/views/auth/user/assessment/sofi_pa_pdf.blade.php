<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Show SOFI Presentation Assessment</title>
</head>
<style>
    .header{
        text-align: center;
        font-size: 16px;
        margin-bottom: 10px;
        font-weight: bold;
    }
    table {
        border-collapse: collapse;
        width: 100%;
    }
    h4, h5{
        margin-bottom: 5px;
    }
    hr{
        margin-bottom: 3px;
    }
    .fw-700{
        font-weight:bold;
    }
    .info-tim, .hasil-nilai, .sofi{
        margin-bottom: 10px;
    }
    .hasil-nilai th, .hasil-nilai td {
        border: 1px solid black;
        padding: 5px;
        text-align: left;
    }
     .hasil-nilai th {
        background-color:darkgrey;
    }
    .td-title{
        width: 30%;
    }
    .col-total{
        background-color: #D7E0DA;
    }
    .text-danger{
        color: red;
    }
    .text-success{
        color: green;
    }
    
</style>
<body>
    <div class="header">SOFI PRESENTATION
        <h3 class="mt-0">{{$data['dataTeam']->event_name . ' Tahun ' . $data['dataTeam']->year}}</h3>
    </div>
    <div class="info-tim">
        <h4>INFORMASI TIM</h4>
        <table>
            <tr>
                <td class="td-title">Nama Tim</td>
                <td>: {{$data['dataTeam']->team_name}}</td>
            </tr>
            <tr>
                <td class="td-title">Judul Inovasi</td>
                <td>: {{$data['dataTeam']->innovation_title}}</td>
            </tr>
        </table>
    </div>
    <div class="hasil-nilai">
        <h4>Hasil Penilaian</h4>
        <table>
            <tr>
                <th>No</th>
                <th>Item Penilaian</th>
                <th>Final Score</th>
            </tr>
             <tr>
                <?php
                    $no=1;
                ?>
                @foreach ($data['individualResults'] as $item)
                    <tr>
                        <td>{{$no++}}</td>
                        <td>{{$item->point}}</td>
                        <td>{{$item->average_score}}</td>
                    </tr>
                @endforeach
                    <tr>
                        <td class="col-total" colspan="2"><div class="fw-700 text-monospace">TOTAL</div></td>
                        <td class="col-total">
                            <div class="fw-700">{{ $data['overallTotal'] }}</div>
                        </td>
                    </tr>
            </tr>
        </table>
    </div>
    <div class="sofi">
        <h4>Strength Point Opportunity For Improvement (SOFI)</h4>
        <div>
            <h5>SOFI : 1. Strength Point</h5>
            {!! nl2br(e($data['dataTeam']->strength)) !!}
        </div>   
            <hr>
        <div>
            <h5>SOFI : 2. Opportunity For Improvement</h5>
            {!! nl2br(e($data['dataTeam']->opportunity_for_improvement)) !!}
        </div>
            <hr>
        <div>
            <h5>Real Benefit (Rp)</h5>
            Rp. {{ number_format($data['dataTeam']->financial, 2, ',', '.') }}
        </div>
            <hr>
        <div>
            <h5>Potensi Benefit (Rp)</h5>
            Rp. {{ number_format($data['dataTeam']->potential_benefit, 2, ',', '.') }}     
        </div>
            <hr>
            <div class="ms-4">
                <h6 class="mb-1">Potensi Replikasi</h6>
                @if($data['dataTeam']->potensi_replikasi == 'Bisa Direplikasi')
                    <p>Bisa Direplikasi</p>
                @elseif($data['dataTeam']->potensi_replikasi == 'Tidak Bisa Direplikasi')
                    <p>Tidak Bisa Direplikasi</p>
                @else
                    <p>Nilai tidak valid</p>
                @endif
            </div>
            
            <hr>
        <div>
            <h5>Detil Penghitungan Benefit (Real & Potensial)</h5>
            {!! nl2br(e($data['dataTeam']->suggestion_for_benefit)) !!}
        </div>
            <hr>
        <div>
            <h5>Rekomendasi Juri Terhadap Kategori Makalah Inovasi</h5>
            {{$data['dataTeam']->recommend_category}}
        </div>
    </div>
</body>
</html>