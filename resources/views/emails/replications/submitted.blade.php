@component('mail::message')
# Pengajuan Replikasi Dikirim

Permintaan replikasi untuk inovasi **{{ $p['innovation_title'] }}** telah dikirim.

**Tim Inovasi:** {{ $p['team_name'] }}  
**Ketua Tim:** {{ $p['leader_name'] ?? '-' }} ({{ $p['leader_email'] ?? '-' }})

**Replikator (PIC):** {{ $p['pic_name'] }}  
**HP PIC:** {{ $p['pic_phone'] }}  
**Unit Kerja:** {{ $p['unit_name'] ?? '-' }}  
**Atasan:** {{ $p['superior_name'] ?? '-' }}  
**Plant:** {{ $p['plant_name'] ?? '-' }}  
**Area Lokasi:** {{ $p['area_location'] ?? '-' }}  
**Rencana Tanggal Replikasi:** {{ $p['planned_date'] ? \Carbon\Carbon::parse($p['planned_date'])->format('d M Y') : '-' }}

Diajukan oleh: **{{ $p['submitted_by_name'] }}** ({{ $p['submitted_by_email'] }})

@component('mail::button', ['url' => $p['return_to'] ?? url('/')])
Lihat Detail
@endcomponent

Terima kasih,  
Portal Inovasi SIG
@endcomponent
