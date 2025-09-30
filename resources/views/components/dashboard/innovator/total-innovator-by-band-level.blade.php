<div class="card team-card border-0 shadow-lg mt-3">
    <div class="card-header bg-gradient-primary">
        <h5 class="card-title text-white">Total Inovator Berdasarkan Band Level</h5>
    </div>
    <div class="card-body">
        @php
            $allBands = ['Band 2', 'Band 3', 'Band 4', 'Band 5'];
        @endphp
        
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Band</th>
                    @foreach ($years as $year)
                        <th class="text-center">{{ $year }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($allBands as $band)
                    <tr>
                        <td>{{ $band }}</td>
                        @foreach ($years as $year)
                            <td class="text-center">
                                {{ $innovatorData[$band][$year] ?? 0 }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>