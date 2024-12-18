<div class="card p-3 mt-3">
    <h5 class="text-center">Total Innovators by Category</h5>
    <canvas id="totalInnovatorChart"></canvas>
</div>

<script type="module">
    import { renderTotalInnovatorChart } from "{{ Vite::asset('resources/js/event/totalInnovatorCategories.js') }}";

    const chartData = @json($chartData);
    renderTotalInnovatorChart('totalInnovatorChart', chartData);
</script>
