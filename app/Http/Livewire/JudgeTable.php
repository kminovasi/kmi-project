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
    public $event_id = '';
    public $perPage = 10;

    protected $queryString = ['search'];
    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'eventSelected' => 'updateEvent',
        'companySelected' => 'updateCompany'];

    public function updateEvent($eventId)
    {
        $this->event_id = $eventId;
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

    public function resetPage()
    {
        $this->gotoPage(1);
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

            if ($this->event_id) {
                $query->where('event_id', $this->event_id);
            }

            if ($this->search) {
                $query->where('users.name', 'ILIKE', '%' . $this->search . '%');
            }
        }

        $judges = $query->orderBy('judges.updated_at', 'desc')->paginate($this->perPage);


        return view('livewire.judge-table', [
            'judges' => $judges,
            'currentPage' => $judges->currentPage()
        ]);
    }
}
