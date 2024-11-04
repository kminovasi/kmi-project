<?php

namespace App\Http\Livewire;

use App\Models\Judge;
use Livewire\Component;
use Livewire\WithPagination;

class JudgeTable extends Component
{
    use WithPagination;

    public $search = '';
    public $company = '';
    public $event = '';
    public $perPage = 10;

    protected $queryString = ['search', 'company', 'event', 'perPage'];
    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'eventSelected' => 'updateEvent',
        'companySelected' => 'updateCompany'];

    public function updateEvent($eventId)
    {
        $this->event = $eventId;
        $this->resetPage();
    }

    public function updateCompany($selectedCompany){
        $this->company = $selectedCompany;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();
        $query = Judge::with('event')
            ->join('users', 'judges.employee_id', '=', 'users.employee_id')
            ->select(
                'judges.*',
                'users.name',
                'users.employee_id',
                'users.company_name',
                'users.company_code',
                'users.unit_name',
            );

        if ($user->role == 'Admin') {
            $query->where('users.company_code', $user->company_code);
        } else {
            if ($this->company) {
                $query->where('company_code', $this->company);
            }

            if ($this->event) {
                $query->where('event_id', $this->event);
            }

            if ($this->search) {
                $query->where('users.name', 'ILIKE', '%' . $this->search . '%');
            }
        }

        $judges = $query->orderBy('users.name', 'asc')->paginate($this->perPage);
        $currentPage = $judges->currentPage();

        return view('livewire.judge-table', compact('judges', 'currentPage'));
    }
}
