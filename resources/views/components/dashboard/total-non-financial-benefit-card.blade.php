<div>
    @push('css')
        <style>
            .bg-gradient-primary {
                background: linear-gradient(135deg, #eb4a3a 0%, #ff6b6b 100%);
            }
            .table-benefit {
                width: 100%;
                border-collapse: collapse;
                margin: 0;
                padding: 0;
            }
            .table-benefit th, .table-benefit td {
                text-align: left;
                padding: 10px;
                border: 1px solid #ddd;
            }
            .table-benefit th {
                background-color: #f8f9fa;
                font-weight: bold;
            }
        </style>
    @endpush
    <div class="card team-card border-0 shadow-lg mt-3">
        <div class="card-header bg-gradient-primary">
            <h5 class="card-title text-white">Total Non Finansial Benefit</h5>
        </div>
        <div class="card-body">
            <table class="table-benefit">
                <thead>
                    <tr>
                        <th class="text-center">Nama Benefit</th>
                        <th class="text-center">Jumlah Makalah</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $benefit)
                        <tr>
                            <td class="text-center">{{ $benefit->name_benefit }}</td>
                            <td class="text-center">{{ $benefit->papers_count }}</td>
                            <td class="text-center"><a href="{{route('dashboard.showAllBenefit', ['customBenefitPotentialId' => $benefit->id])}}" class="btn btn-success btn-sm">detail</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
