<div>
    <input type="text" id="userSearch" class="form-control form-control-sm" wire:model="query" placeholder="Cari Karyawan..." />

    @if(!empty($users))
        <ul class="list-group mt-2 text-sm">
            @foreach($users as $user)
                <li class="list-group-item list-group-item-action" wire:click="selectUser('{{ $user->employee_id }}')">
                    {{ $user->name }} - {{ $user->company_name }}
                </li>
            @endforeach
        </ul>
    @endif

    @if($selectedUser)
        <input type="hidden" name="employee_id" value="{{ $selectedUser }}">
    @endif
</div>
