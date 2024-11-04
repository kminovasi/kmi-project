<div>
    <select name="theme" id="theme-select" class="form-select form-select-sm">
        <option value="">-- Pilih Tema --</option>
        @foreach ($themes as $theme)
        <option value="{{ $theme->id }}" {{ request('theme')==$theme->theme_name ? 'selected' : '' }}>
            {{ $theme->theme_name }}
        </option>
        @endforeach
    </select>
</div>
