<div class="card mt-3">
    <div class="card-header" style="background-color: #eb4a3a">
        <h5 class="text-white">Total Potensial Benefit per Perusahaan</h5>
    </div>
    <div class="card-body">
        {{-- boleh pakai wrapper biar responsif, atau biarkan height inline seperti punyamu --}}
        <div style="position:relative; width:100%; height:35rem;">
            <canvas id="total-potential-benefit-chart" style="width:100%; height:100%; display:block;"></canvas>
        </div>
    </div>
</div>

<script>
    window.chartDataTotalPotentialBenefit = @json($chartDataTotalPotentialBenefit);
</script>

<script src="{{ asset('/build/assets/totalBenefitChart-556469cb.js') }}" type="module"></script>
