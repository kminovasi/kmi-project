<div class="chart-container">
    <h2 class="chart-title">Distribusi Ide dan Inovasi per
        @php
            $labels = [
                'unit_name' => 'Unit',
                'directorate_name' => 'Direktorat',
                'group_function_name' => 'Group',
                'department_name' => 'Departemen',
                'section_name' => 'Seksi',
                'sub_section_of' => 'Sub Seksi',
            ];
        @endphp

        {{ $labels[$organizationUnit] ?? 'Unit Organisasi' }}
    </h2>
    <div class="chart-wrapper">
        <canvas id="directorateChart"></canvas>
    </div>
    <div class="chart-legend" id="chartLegend"></div>
</div>



<script>
    window.directorateData = @json($directorateData ?? []);
</script>

<style>
    .chart-container {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        padding: 30px;
        margin: 20px 0;
        overflow-y: auto;
        /* Tambahkan scroll jika terlalu tinggi */
        max-height: 800px;
        /* Batasi tinggi maksimum container */
    }

    .chart-title {
        font-size: 1.3rem;
        color: #333;
        margin-bottom: 25px;
        text-align: center;
        font-weight: 500;
        position: sticky;
        /* Buat title tetap terlihat saat scroll */
        top: 0;
        background: #ffffff;
        padding: 10px 0;
        z-index: 1;
    }

    .chart-wrapper {
        position: relative;
        height: 400px;
        /* Height ini akan di-override oleh JavaScript */
        transition: height 0.3s ease;
        /* Animasi perubahan height */
    }

    .chart-legend {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 25px;
        position: sticky;
        /* Buat legend tetap terlihat saat scroll */
        bottom: 0;
        background: #ffffff;
        padding: 10px 0;
        z-index: 1;
    }

    .legend-item {
        display: flex;
        align-items: center;
        margin: 5px 15px;
    }

    .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        margin-right: 8px;
    }

    .legend-label {
        font-size: 0.9rem;
        color: #555;
    }

    /* Media Queries untuk responsivitas */
    @media screen and (max-width: 768px) {
        .chart-container {
            padding: 15px;
            max-height: 600px;
        }

        .chart-title {
            font-size: 1.1rem;
        }

        .legend-label {
            font-size: 0.8rem;
        }
    }
</style>
