<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Judul</th>
            <th>Tema</th>
            <th>Abstrak</th>
            <th>Status Inovasi</th>
            <th>Permasalahan</th>
            <th>Permasalahan Utama</th>
            <th>Solusi</th>
            <th>On Desk</th>
            <th>Presentation</th>
            <th>Caucus</th>
            <th>Final Score</th>
            <th>Best of The Best</th>
            <th>Financial</th>
            <th>Potential Benefit</th>
            <th>Non-Financial Impact</th>
            <th>Potensi Replikasi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($papers as $index => $paper)
        <tr>
            <!-- Data Inovasi -->
            <td>{{ $index + 1 }}</td>
            <td>{{ $paper->innovation_title }}</td>
            <td>{{ $paper->theme_name }}</td>
            <td>{{ $paper->abstract }}</td>
            <td>{{ $paper->status_inovasi }}</td>
            <td>{{ $paper->problem }}</td>
            <td>{{ $paper->main_cause }}</td>
            <td>{{ $paper->solution }}</td>
            <td>{{ $paper->total_score_on_desk }}</td>
            <td>{{ $paper->total_score_presentation }}</td>
            <td>{{ $paper->total_score_caucus }}</td>
            <td>{{ $paper->final_score }}</td>
            <td>{{ $paper->is_best_of_the_best ? 'Yes' : 'No'}}</td>
            <td>Rp.{{ number_format($paper->financial, 0, ',', '.') }}</td>
            <td>Rp.{{ number_format($paper->potential_benefit, 0, ',', '.') }}</td>
            <td>{{ $paper->non_financial }}</td>
            <td>{{ $paper->potensi_replikasi }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <th scope="col">NIK</th>
            <th scope="col">Nama</th>
            <th scope="col">Status</th>
            <th scope="col">Email</th>
            <th scope="col">Perusahaan</th>
            <th scope="col">Kode</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($teamMember as $member)
        <tr>
            <td>{{ $member->user->employee_id }}</td>
            <td>{{ $member->user->name }}</td>
            <td>{{ $member->status }}</td>
            <td>{{ $member->user->email }}</td>
            <td>{{ $member->user->company_name }}</td>
            <td>{{ $member->user->company_code }}</td>
        </tr>
        @endforeach
    </tbody>
</table>