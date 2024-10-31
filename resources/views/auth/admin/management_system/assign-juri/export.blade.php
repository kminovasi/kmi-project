<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Perusahaan</th>
            <th>Event</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>

        @if ($judges->count() > 0)
        @foreach ($judges as $index => $j)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $j->name }}</td>
            <td>{{ $j->company_name }}</td>
            <td>{{ $j->event->event_name }} {{ $j->event->year }}</td>
            <td>
                {{$j->status}}
            </td>
        </tr>
        @endforeach
        @else
        <tr>
            <td colspan="5" class="text-center">Tidak ada data</td>
        </tr>
        @endif

    </tbody>
</table>
