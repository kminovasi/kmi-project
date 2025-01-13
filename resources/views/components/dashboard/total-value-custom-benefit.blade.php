<!-- resources/views/components/dashboard/total-value-custom-benefit.blade.php -->
<div class="card">
    <div class="card-header bg-gradient-primary text-white">
        <h5 class="card-title mb-0 text-white">Total Non Financial benefit</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Benefit</th>
                        <th class="text-end">Total Value</th>
                        <th class="text-end">Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($benefitTotals as $benefit)
                        <tr>
                            <td>{{ $benefit->name_benefit }}</td>
                            <td class="text-end">
                                Rp {{ number_format($benefit->total_value, 0, ',', '.') }}
                            </td>
                            <td class="text-end">
                                @if ($grandTotal > 0)
                                    {{ number_format(($benefit->total_value / $grandTotal) * 100, 1) }}%
                                @else
                                    0%
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-primary">
                    @if ($grandTotal !== 0)
                        <tr>
                            <th>Total</th>
                            <th class="text-end">
                                Rp {{ number_format($grandTotal, 0, ',', '.') }}
                            </th>
                            <th class="text-end">100%</th>
                        </tr>
                    @else
                        <tr>
                            <th colspan="3">Data tidak ada</th>
                        </tr>
                    @endif
                </tfoot>
            </table>
        </div>
    </div>
</div>
