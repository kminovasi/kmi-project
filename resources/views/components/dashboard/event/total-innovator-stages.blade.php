<div class="card p-3 mt-3">
    <h5 class="text-center">Total Inovator per Tahap</h5>
    <canvas id="totalInnovatorStagesChart"></canvas>
</div>

<script type="module">
    import { renderTotalInnovatorStagesChart } from "{{ Vite::asset('resources/js/event/totalInnovatorStages.js') }}";

    const chartData = @json($chartData);
    renderTotalInnovatorStagesChart('totalInnovatorStagesChart', chartData);
</script>
