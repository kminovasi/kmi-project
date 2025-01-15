<div class="card">
    <div class="card-header">
        <h4>Total Team per Perusahaan</h4>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach ($totalTeams as $teamData)
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="card-title">{{ $teamData['company_name'] }}</h5>
                            <p class="card-text">
                                <strong>Total Team:</strong> {{ $teamData['total_teams'] }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
