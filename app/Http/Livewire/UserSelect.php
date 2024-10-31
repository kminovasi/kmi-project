<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;

class UserSelect extends Component
{
    public $query = '';
    public $users = [];
    public $selectedUser = null;

    public function updatedQuery()
    {
        $this->users = User::where('name', 'ILIKE', '%' . $this->query . '%')
            ->select('employee_id', 'name', 'company_name')
            ->limit(5)
            ->get();
    }

    public function selectUser($userId)
    {
        $user = User::where('employee_id', $userId)->first();
        $this->selectedUser = $userId;
        $this->query = $user->name . ' - ' . $user->company_name;
        $this->users = [];
    }


    public function render()
    {
        return view('livewire.user-select');
    }
}
