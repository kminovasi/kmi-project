<div class="card team-card border-0 shadow-lg mt-3">
    <div class="card-header bg-gradient-primary">
        <h5 class="card-title text-white">Total Inovator Berdasarkan Band Level</h5>
    </div>
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Band Level</th>
                    @foreach ($years as $year)
                        <th class="text-center">{{ $year }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($innovatorData as $band => $yearCount)
                    <tr>
                        <td>{{ $band }}</td>
                        @foreach ($yearCount as $count)
                            <td class="text-center">{{ $count }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>