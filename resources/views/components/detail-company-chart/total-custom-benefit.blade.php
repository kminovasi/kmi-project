<div>
    @foreach ($benefits as $benefit)
        <div class="benefit-card row mt-2 mr-2">
            <h3 class="benefit-title">{{ $benefit->name_benefit }}</h3>
            <div class="benefit-stat">
                <span class="benefit-value">Rp {{ $totals[$benefit->id] }}</span>
            </div>
        </div>
    @endforeach
</div>
