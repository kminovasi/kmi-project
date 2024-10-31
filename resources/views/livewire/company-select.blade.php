<div>
    <select name="company" class="form-select form-select-sm">
        <option value="">-- Pilih Perusahaan --</option>
        @foreach ( $companies as $company )
        <option value="{{ $company->company_code }}">{{ $company->company_name }}</option>
        @endforeach
    </select>
</div>
