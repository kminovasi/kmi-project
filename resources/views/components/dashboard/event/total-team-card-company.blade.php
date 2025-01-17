<div class="card">
    <div class="card-header">
        <h4>Total Team per Perusahaan</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <?php
            // Define a set of light colors
            $lightColors = [
                '#F8D7DA', '#D1ECF1', '#D4EDDA', '#FFF3CD', '#C3E6CB', '#F9F9F9'
            ];
            ?>
            @foreach ($totalTeams as $teamData)
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm" style="background-color: {{ $lightColors[array_rand($lightColors)] }};">
                        <div class="card-body text-center">
                            <h5 class="card-title">{{ $teamData['company_name'] }}</h5>
                            <p class="card-text">
                                <strong>Total Team:</strong>
                                <span style="font-size: 1.9rem; font-weight: bold;">{{ $teamData['total_teams'] }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
