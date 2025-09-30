<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Berita Acara</title>
  <style>
    @page { size: A4; margin: 2cm; }
    body { margin: 0; font-family: Arial, Helvetica, sans-serif; }

    .header{ text-align:center; }
    .h3{ font-size:16px; font-weight:bold; text-transform:uppercase; }
    .H2{ color:#171515; font-size:20px; font-weight:bold; text-transform:uppercase; }
    .title{ text-align:left; font-weight:bold; }
    .content{ text-align:justify; }
    .opening{ padding-bottom:18px; text-align:justify; }
    .opening ol{ margin:6px 0 0 18px; padding-left:18px; }
    .opening ol li{ text-align:justify; margin-bottom:8px; }

    /* tabel juara */
    .sig-table{ width:100%; border-collapse:collapse; }
    .sig-table th, .sig-table td{ border:1px solid #000; padding:10px; vertical-align:top; }
    .sig-table thead th{ font-weight:bold; text-transform:uppercase; background:#f2f2f2; }
    .sig-table .col-kategori{ width:190px; text-transform:uppercase; font-weight:bold; }
    .sig-table .col-peringkat{ width:120px; text-align:center; }
    .sig-table .col-team{ width:220px; }

    /* TTD Juri */
    .ttd-title{ margin:10px 0 6px; font-weight:bold; }
    .ttd-table{ width:100%; border-collapse:collapse; }
    .ttd-table, .ttd-table tr, .ttd-table td{ border:0; }
    .ttd-table td{ width:50%; padding:10px 24px 22px; vertical-align:top; }
    .ttd-card{ position:relative; height:165px; }
    .ttd-name{ margin:0 0 2px 0; font-weight:700; }
    .ttd-job{ margin:0 0 10px 0; font-size:13px; color:#333; }
    .ttd-num{ font-weight:700; margin-right:6px; }
    .sign-pad{ position:absolute; left:0; right:0; bottom:0; height:80px; }
    .sign-dash{ width:70%; margin:0 auto; border-bottom:2px dashed #7a7a7a; height:0; }

    .ttd{
        text-align: center;
    }
  </style>
</head>
<body>

  <div class="header">
    <p>
      <span class="h3">BERITA ACARA PENETAPAN JUARA</span><br>
      <span class="H2">{{ $data->event_name }} Tahun {{ $data->year }}</span><br>
      <span class="h6">Nomor: {{ $data->no_surat }}</span>
    </p>
  </div>

  <div class="opening content">
    <p class="title">Berdasarkan:</p>
    <ol>
      <li>Proses seleksi kompetisi Inovasi {{ $data->event_name }} Tahun {{ $data->year }} yang dilaksanakan secara sistematis dan terstruktur melalui tiga tahapan penilaian, yaitu <i>On Desk Assessment, Presentation Assessment &amp; Caucus Jury</i>, yang berlangsung mulai tanggal {{ $carbonInstance_startDate->isoFormat('D MMMM YYYY') }} hingga {{ $carbonInstance_endDate->isoFormat('D MMMM YYYY') }}.</li>
      <li>Rapat dewan juri dalam penentuan juara dari setiap kategori dan penetapan kandidat <i>Best of the Best Innovation</i> untuk ditentukan oleh direksi, yang berlangsung pada tanggal {{$carbonInstance_rapatJuri->isoFormat('D MMMM YYYY')}}.</li>
      <li>Rapat dengan Direktur Human Capital dalam penentuan <i>Best of the Best Innovation &amp; Honourable Mention</i> pada tanggal {{$carbonInstance_rapatDirektur->isoFormat('D MMMM YYYY')}}.</li>
    </ol>

    <p style="margin-top:12px;">
      Dengan memperhatikan seluruh kriteria penilaian dan mengacu pada hasil evaluasi komprehensif dari tim juri yang independen dan berkompeten di bidangnya, maka:
    </p>

    <p class="title">Menetapkan:</p>
    <ol>
      <li>Juara {{ $data->event_name }} Tahun {{ $data->year }} untuk setiap kategori.</li>
      <li><i>Best of the Best Innovation &amp; Honourable Mention</i> {{ $data->event_name }} Tahun {{ $data->year }} sebagai berikut:</li>
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
              @php
                if (!empty($dt['is_best_of_the_best']))      $peringkat = 'BEST OF THE BEST';
                elseif (!empty($dt['is_honorable_winner']))  $peringkat = 'HONOURABLE MENTION';
                else { $r = $dt['ranking'] ?? ''; $r = is_numeric($r) ? (int)$r : preg_replace('/\D+/', '', (string)$r); $peringkat = $r ? 'JUARA '.$r : '-'; }
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
    Demikian Berita Acara ini dibuat dengan sebenarnya dan ditandatangani di Jakarta pada hari {{ optional($carbonInstance_penetapanJuara)->isoFormat('dddd, D MMMM YYYY') }} untuk dapat dipergunakan sebagaimana mestinya.
  </p>

  <p class="ttd-title">Tim Juri,</p>

  @php
    $list = ($judges instanceof \Illuminate\Support\Collection) ? $judges->values() : collect($judges);
    $n    = $list->count();
  @endphp

  <table class="ttd-table">
    @for ($i = 0; $i < $n; $i += 2)
      <tr>
        <td>
          @if($p = $list->get($i))
            <div class="ttd-card">
              <p class="ttd-name"><span class="ttd-num">{{ $i+1 }}.</span><b>{{ strtoupper($p->name) }}</b></p>
              <p class="ttd-job">{{ $p->position_title }}</p>
              <div class="sign-pad"><div class="sign-dash"></div></div>
            </div>
          @endif
        </td>
        <td>
          @if($q = $list->get($i+1))
            <div class="ttd-card">
              <p class="ttd-name"><span class="ttd-num">{{ $i+2 }}.</span><b>{{ strtoupper($q->name) }}</b></p>
              <p class="ttd-job">{{ $q->position_title }}</p>
              <div class="sign-pad"><div class="sign-dash"></div></div>
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
