<div>
    <div id="switch" class="form-check form-switch">
        <input type="hidden" name="status" value="nonactive">

        <input class="form-check-input" name="status" type="checkbox" role="switch" id="flexSwitchCheckDefault"
            wire:model="status" value="active">
        <label class="form-check-label text-sm" for="flexSwitchCheckDefault">
            {{ $status ? 'Aktif' : 'Nonactive' }}
        </label>
    </div>
</div>
