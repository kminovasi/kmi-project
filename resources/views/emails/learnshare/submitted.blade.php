@component('mail::message')
# Pengajuan Learn & Share Baru

**Judul**: {{ $ls->title }}

**Dept/Unit Peminta**: {{ $ls->requesting_department }}  
**Waktu Pelaksanaan**: {{ optional($ls->scheduled_at)->format('d M Y H:i') }}  
**Pengaju**: {{ optional($ls->requester)->name }} ({{ $ls->employee_id ?? '-' }})

**Tujuan**  
{{ $ls->objective }}

@php
  $speakerIds = array_filter($ls->speakers ?? []);
  $speakerUsers = $speakerIds ? \App\Models\User::whereIn('employee_id', $speakerIds)->orderBy('name')->get()
                              : collect();
@endphp
**Pembicara**
@forelse($speakerUsers as $u)
- {{ $u->name }} — {{ $u->employee_id }}
@empty
- (belum diisi)
@endforelse

@component('mail::button', ['url' => $showUrl])
Lihat Detail Pengajuan
@endcomponent

Terima kasih,  
{{ config('app.name') }}
@endcomponent
