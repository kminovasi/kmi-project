@extends('layouts.app')
@section('title', 'Edit Benefit Inovasi | ' . $paper->innovation_title)

@section('content')
    <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
        <div class="container-xl px-4">
            <div class="page-header-content">
                <div class="row align-items-center justify-content-between pt-3">
                    <div class="col-auto mb-3">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="edit"></i></div>
                            Edit Benefit Inovasi
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="{{ route('event-team.show', $eventId) }}">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>


    <div class="container-xl px-4 mt-4">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-xl-12">
                <div class="card mb-4">
                    <div class="card-header">Informasi Benefit Inovasi</div>
                    <div class="card-body">
                        <form action="{{ route('event-team.benefit.update', ['id' => $paper->id, 'eventId' => $eventId]) }}"
                            method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="small mb-1 fw-600" for="financial">Benefit Finansial (IDR)</label>
                                <input class="form-control @error('financial') is-invalid @enderror" id="financial"
                                    name="financial" type="text"
                                    value="{{ old('financial', $paper->financial_formatted) }}" required>
                                @error('financial')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="small mb-1 fw-600" for="potential_benefit">Benefit Potensial (IDR)</label>
                                <input class="form-control @error('potential_benefit') is-invalid @enderror"
                                    id="potential_benefit" name="potential_benefit" type="text"
                                    value="{{ old('potential_benefit', $paper->potential_benefit_formatted) }}" required>
                                @error('potential_benefit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="small mb-1 fw-600" for="non_financial">Benefit Non-Finasial</label>
                                <textarea class="form-control @error('non_financial') is-invalid @enderror" id="non_financial" name="non_financial"
                                    rows="4" required>{{ old('non_financial', $paper->non_financial) }}</textarea>
                                @error('non_financial')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if (isset($customBenefits) && $customBenefits->count() > 0)
                                <div class="card mt-4 mb-4">
                                    <div class="card-header">
                                        Custom Benefits for {{ $paper->team->company->company_name }}
                                    </div>
                                    <div class="card-body">
                                        @foreach ($customBenefits as $benefit)
                                            <div class="mb-3">
                                                <label class="small mb-1" for="custom_benefit_{{ $benefit->id }}">
                                                    {{ $benefit->name_benefit }} (IDR)
                                                </label>
                                                <input
                                                    class="form-control custom-benefit @error('custom_benefit.' . $benefit->id) is-invalid @enderror"
                                                    id="custom_benefit_{{ $benefit->id }}"
                                                    name="custom_benefit[{{ $benefit->id }}]" type="text"
                                                    value="{{ old(
                                                        'custom_benefit.' . $benefit->id,
                                                        isset($existingCustomBenefits[$benefit->id])
                                                            ? number_format($existingCustomBenefits[$benefit->id], 0, ',', '.')
                                                            : '',
                                                    ) }}">
                                                @error('custom_benefit.' . $benefit->id)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Format untuk financial dan potential benefit
            new Cleave('#financial', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                numeralDecimalMark: ',',
                delimiter: '.'
            });

            new Cleave('#potential_benefit', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                numeralDecimalMark: ',',
                delimiter: '.'
            });

            // Format untuk custom benefits
            document.querySelectorAll('.custom-benefit').forEach(function(element) {
                new Cleave(element, {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand',
                    numeralDecimalMark: ',',
                    delimiter: '.'
                });
            });
        });
    </script>
@endpush
