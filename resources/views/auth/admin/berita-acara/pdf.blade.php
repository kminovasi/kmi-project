<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Berita Acara</title>
</head>
<style>
   body { font-family: Arial; margin:0; padding:0; }
    .header{
        text-align: center;
    }
    .h3{
        font-size: 16px;
        font-weight: bold;
        text-transform: uppercase;
    }
    .h2{
        color: #171515;
        font-size: 20px;
        font-weight: bold;
        text-transform: uppercase;
    }
    .title{
        text-align: left;
        font-weight: bold;
    }
    .opening ol{ 
        margin:6px 0 0 18px; 
        padding-left:18px; 
    }
    .opening ol li{ 
        text-align:justify; 
        margin-bottom:8px; 
    }

    .content{
        text-align: justify; /* Justify text */
    }
    .opening {
        padding-bottom: 28px;
        text-align: justify; /* Justify text */
    }
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
    .ttd{
        text-align: center;
    }
    * {
        box-sizing: border-box;
    }
    .row:after {
        content: "";
        display: table;
        clear: both;
    }

    .justify-text{
        text-align: justify;
    }

    .ranking, .number{
        text-align: center;
    }  

    /* Tabel Kategori */
/* header/footer tabel agar diulang & engine paham layout */
.sig-table{ width:100%; border-collapse:collapse; page-break-inside:auto; }
.sig-table thead{ display:table-header-group; }
.sig-table tfoot{ display:table-footer-group; }

/* JANGAN pecah baris di tengah halaman */
.sig-table tr{ page-break-inside:avoid; }

/* (opsional) pastikan teks boleh membungkus, bukan satu baris panjang */
.sig-table td, .sig-table th{
  border:0.6pt solid #000;
  vertical-align:top;
  white-space: normal;   /* penting kalau ada judul panjang */
  word-wrap: break-word; /* biar bisa membungkus kata panjang */
}

/* TTD Juri */
  .ttd-table{ width:100%; border-collapse:collapse; }
  .ttd-table, .ttd-table tr, .ttd-table th, .ttd-table td{ border:0 !important; }
  .ttd-table td{ width:50%; padding:18px 18px 22px; vertical-align:bottom; }

  .ttd-item{ position:relative; min-height:150px; }
  .sign-dash{ position:absolute; left:0; right:0; bottom:0; border-bottom:2px dashed #7a7a7a; }
  .ttd-name{ margin:0; }
  .ttd-job{ margin:2px 0 0; font-size:13px; color:#333; }
  .ttd-num{ font-weight:bold; margin-right:6px; }
 
</style>
<body>
    <div class="header">
        <p>
            <span class="h3">BERITA ACARA PENETAPAN JUARA </span><br>
            <span class="H2">{{$data->event_name}} Tahun {{$data->year}}</span><br>
            <span class="h6">Nomor: {{$data->no_surat}}</span>
        </p>
    </div>
    <div class="opening content">
  <p class="title">Berdasarkan:</p>
    <ol>
        <li>
        Proses seleksi kompetisi Inovasi {{$data->event_name}} Tahun {{$data->year}} yang dilaksanakan
        secara sistematis dan terstruktur melalui tiga tahapan penilaian, yaitu
        <i>On Desk Assessment, Presentation Assessment &amp; Caucus Jury</i>, yang berlangsung
        mulai tanggal {{$carbonInstance_startDate->isoFormat('D MMMM YYYY')}} hingga
        {{$carbonInstance_endDate->isoFormat('D MMMM YYYY')}}.
        </li>
        <li>
        Rapat dewan juri dalam penentuan juara dari setiap kategori dan penetapan kandidat
        <i>Best of the Best Innovation</i> untuk ditentukan oleh direksi, yang berlangsung
        pada tanggal {{$carbonInstance_rapatJuri->isoFormat('D MMMM YYYY')}}.
        </li>
        <li>
        Rapat dengan Direktur Human Capital dalam penentuan
        <i>Best of the Best Innovation &amp; Honourable Mention</i> pada tanggal {{$carbonInstance_rapatDirektur->isoFormat('D MMMM YYYY')}}.
        </li>
    </ol>

  <p style="margin-top:12px;">
    Dengan memperhatikan seluruh kriteria penilaian dan mengacu pada hasil evaluasi komprehensif
    dari tim juri yang independen dan berkompeten di bidangnya, maka :
  </p>

  <p class="title">Menetapkan:</p>
  <ol>
    <li>Juara {{$data->event_name}} Tahun {{$data->year}} untuk setiap kategori.</li>
    <li>
      <i>Best of the Best Innovation &amp; Honourable Mention</i>
      {{$data->event_name}} Tahun {{$data->year}} sebagai berikut:
    </li>
  </ol>
</div>

<table class="sig-table">
  <thead>
    <tr>
      <th>Kategori</th>
      <th>Peringkat</th>
      <th>Nama Tim</th>
      <th>Judul Inovasi</th>
    </tr>
  </thead>
  <tbody>
  @foreach($juara as $category => $rows)
    @php $rowCount = count($rows ?? []); @endphp
    @if($rowCount > 0)
      @foreach($rows as $i => $dt)
        <tr>
          @if($i === 0)
            <td class="col-kategori" rowspan="{{ $rowCount }}">{{ strtoupper($category) }}</td>
          @endif

          {{-- Peringkat: JUARA n / BEST OF THE BEST / HONOURABLE MENTION --}}
          @php
            $keputusan = isset($dt['keputusan_bod']) ? trim((string)$dt['keputusan_bod']) : '';

            if ($keputusan !== '') {
                // tampilkan nilai keputusan dari BOD apa adanya
                $peringkat = strtoupper($keputusan);
            } elseif (!empty($dt['is_best_of_the_best'])) {
                $peringkat = 'BEST OF THE BEST';
            } elseif (!empty($dt['is_honorable_winner'])) {
                $peringkat = 'HONOURABLE MENTION';
            } else {
                $r = $dt['ranking'] ?? '';
                $r = is_numeric($r) ? (int)$r : preg_replace('/\D+/', '', (string)$r);
                $peringkat = $r ? 'JUARA '.$r : '-';
            }
          @endphp
          <td class="col-peringkat">{{ $peringkat }}</td>
          <td class="col-team"><b>{{ strtoupper($dt['teamname'] ?? '-') }}</b></td>
          <td class="col-judul">{{ $dt['innovation_title'] ?? '-' }}</td>
        </tr>
      @endforeach
    @endif
  @endforeach
  </tbody>
</table>

<p style="margin-top:12px;">
    Demikian Berita Acara ini dibuat dengan sebenarnya dan ditandangani di Jakarta pada hari {{ optional($carbonInstance_penetapanJuara)->isoFormat('dddd, D MMMM YYYY') }}. untuk dapat dipergunakan sebagaimana mestinya. <br><br>
</p>

<p class="ttd-title">Tim Juri,</p>

@php
  $list  = ($judges instanceof \Illuminate\Support\Collection) ? $judges->values() : collect($judges);
  $count = $list->count();
  $rows  = (int) ceil($count / 2);
@endphp

<table class="ttd-table">
  @for ($r = 0; $r < $rows; $r++)
    @php
      $i1 = $r * 2;  $i2 = $i1 + 1;
      $x1 = $list->get($i1);
      $x2 = $list->get($i2);
    @endphp
    <tr>
      <td>
        @if($x1)
          <div class="ttd-item">
            <div class="sign-dash"></div>
            <p class="ttd-name"><span class="ttd-num">{{ $i1 + 1 }}.</span><b>{{ $x1->name }}</b></p>
            <p class="ttd-job">{{ $x1->position_title }}</p>
          </div>
        @endif
      </td>
      <td>
        @if($x2)
          <div class="ttd-item">
            <div class="sign-dash"></div>
            <p class="ttd-name"><span class="ttd-num">{{ $i2 + 1 }}.</span><b>{{ $x2->name }}</b></p>
            <p class="ttd-job">{{ $x2->position_title }}</p>
          </div>
        @endif
      </td>
    </tr>
  @endfor
</table>

    <div class="ttd" style="margin-top:18px;">
        <p>
            Ditetapkan oleh : <br>
            {{-- <br>Jakarta, {{ $carbonInstance->isoFormat('D MMMM YYYY') }} --}}
            PT Semen Indonesia (Perserro) Tbk
        </p>
        <div class="row">
            <?php
                if(count($bods) == 4)
                    $column_ke = 2;
                elseif(count($bods) >= 5)
                    $column_ke = 3;
                else
                    $column_ke = count($bods);

                $no = 0;
            ?>
            @foreach($bods as $bod)
                @if($no == 5)
                    @break
                @endif
                <div class="column-{{$column_ke}}">
                    <p><b>{{ $bod['name'] }}</b>
                        <br>{{ $bod['position_title'] }}
                    </p>
                </div>
                <?php $no++; ?>
            @endforeach
        </div>
    </div>

</body>
</html>
