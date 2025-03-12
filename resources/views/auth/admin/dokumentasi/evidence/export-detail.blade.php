<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Tim</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Detail Tim {{ $team->team_name }}</h2>

    <div class="card" id="team-member">
        <h4>Anggota Tim:</h4>
        <table>
            <tr>
                <th>NIK</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Status</th>
                <th>Perusahaan</th>
                <th>Kode Perusahaan</th>
            </tr>
            @foreach ($team->pvtMembers as $member)
                <tr>
                    <td>{{ $member->user->employee_id }}</td>
                    <td>{{ $member->user->name }}</td>
                    <td>{{ $member->user->email }}</td>
                    <td>{{ $member->status }}</td>
                    <td>{{ $member->user->company_name }}</td>
                    <td>{{ $member->user->company_code }}</td>
                </tr>
            @endforeach
        </table>
    </div>
    <div class="card" id="team-paper">
        <h4>Informasi Paper:</h4>
        @foreach ($team->papers as $paper)
        <div class="row mb-1">
            <div class="col-3">
                <p><strong>Judul:</strong></p>
            </div>
            <div class="col-9">
                <p>{{ $paper->innovation_title }}</p>
            </div>
        </div>
        <div class="row mb-1">
            <div class="col-3">
                <p><strong>Lokasi:</strong></p>
            </div>
            <div class="col-9">
                <p>{{ $paper->inovasi_lokasi }}</p>
            </div>
        </div>
        <div class="row mb-1">
            <div class="col-3">
                <p><strong>Abstrak:</strong></p>
            </div>
            <div class="col-9">
                <p>{{ $paper->abstract }}</p>
            </div>
        </div>
        <div class="row mb-1">
            <div class="col-3">
                <p><strong>Masalah:</strong></p>
            </div>
            <div class="col-9">
                <p>{{ $paper->problem }}</p>
            </div>
        </div>
        <div class="row mb-1">
            <div class="col-3">
                <p><strong>Penyebab Utama:</strong></p>
            </div>
            <div class="col-9">
                <p>{{ $paper->main_cause }}</p>
            </div>
        </div>
        <div class="row mb-1">
            <div class="col-3">
                <p><strong>Solusi:</strong></p>
            </div>
            <div class="col-9">
                <p>{{ $paper->solution }}</p>
            </div>
        </div>
        <div class="row mb-1 text-center">
            <h5>Foto Tim</h5>
            <img src="{{ storage_path('app/public/' . $paper->proof_idea) }}" alt="Foto Tim" width="100px">
        </div>
        <div class="row mb-1 text-center">
            <h5>Foto Inovasi</h5>
            <img src="{{ storage_path('app/public/' . $paper->innovation_photo) }}" alt="Foto Inovasi"  width="100px">
        </div>
        <div class="row mb-1">
            <h5>Nilai Yang Didapatkan:</h5>
            <div class="col-3">
                <p><strong>On Desk:</strong> {{ $eventTeam->total_score_on_desk }}</p>
            </div>
            <div class="col-3">
                <p><strong>Presentasi:</strong> {{ $eventTeam->total_score_presentation }}</p>
            </div>
            <div class="col-3">
                <p><strong>Caucus:</strong> {{ $eventTeam->total_score_caucus }}</p>
            </div>
            @if($eventTeam->is_best_of_the_best)
            <div class="col-3">
                <p><strong>Best Of The Best</strong></p>
            </div>
            @else
            <div class="col-3">
                <p><strong>Bukan Best Of The Best</strong></p>
            </div>
            @endif
        </div>
        <div class="row mb-1">
            <h5>Benefit Inovasi:</h5>
            <div class="col-3">
                <p><strong>Benefit Finansial (Real):</strong> {{ $paper->financial }}</p>
            </div>
            <div class="col-3">
                <p><strong>Benefit Finansial (Potensial):</strong> {{ $paper->potential_benefit}}</p>
            </div>
            <div class="col-3">
                <p><strong>Benefit Non-Finansial</strong> {{ $paper->non_financial }}</p>
            </div>
        </div>
        @endforeach
    </div>
</body>
</html>