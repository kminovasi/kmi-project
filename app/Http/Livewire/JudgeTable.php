<?php

namespace App\Http\Livewire;

use App\Models\Judge;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

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

    public function updateStatus($id)
    {

        $judge = Judge::findOrFail($id);

        if ($judge->status == 'active') {
            $judge->status = 'nonactive';
        } else {
            $judge->status = 'active';
        }

        $judge->save();

    }

    public function render()
    {
        $user = Auth::user();

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

            if ($user->role !== 'Superadmin') {
                $query->where('company_code', $user->company_code);
            }

            if ($this->event) {
                $query->where('event_id', $this->event);
            }

            if ($this->search) {
                $query->where('users.name', 'like', '%' . $this->search . '%');
            }

        $judges = $query->orderBy('users.name', 'asc')->paginate($this->perPage);
        $currentPage = $judges->currentPage();

        return view('livewire.judge-table', compact('judges', 'currentPage'));
    }
}