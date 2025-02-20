<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Team</th>
            <th>Judul</th>
            <th>Tema</th>
            <th>Event</th>
            <th>Tahun</th>
            <th>Finansial</th>
            <th>Potensi Replikasi</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($papers as $index => $paper)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $paper->team_name }}</td>
            <td>{{ $paper->innovation_title }}</td>
            <td>{{ $paper->theme_name }}</td>
            <td>{{ $paper->event_name }}</td>
            <td>{{ $paper->year }}</td>
            <td>Rp.{{ number_format($paper->financial, 0, ',', '.') }}</td>
            <td>{{ $paper->potensi_replikasi }}</td>
            <td>{{ $paper->is_best_of_the_best ? 'Best of The Best' : 'Juara ' . $index + 1}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
