<?php

namespace App\Http\Livewire;

use App\Models\Paper;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class EvidenceTable extends Component
{
    use WithPagination;

    public $search = '';
    public $company = '';
    public $event = '';
    public $theme = '';
    public $categoryId;
    public $perPage = 10;

    protected $queryString = ['search', 'company', 'event', 'theme', 'perPage'];
    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'eventSelected' => 'updateEvent',
        'companySelected' => 'updateCompany',
        'themeSelected' => 'updateTheme'
    ];

    public function updateTheme($themeId)
    {
        $this->theme = $themeId;
        $this->resetPage();
    }

    public function updateEvent($eventId)
    {
        $this->event = $eventId;
        $this->resetPage();
    }

    public function updateCompany($selectedCompany)
    {
        $this->company = $selectedCompany;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {

        $papers = \DB::table('teams')
            ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->join('papers', 'teams.id', '=', 'papers.team_id')
            ->join('events', 'pvt_event_teams.event_id', '=', 'events.id')
            ->join('themes', 'teams.theme_id', '=', 'themes.id')
            ->where('teams.category_id', $this->categoryId)
            ->where('events.status', 'finish')
            ->select(
                'papers.*',
                'teams.team_name',
                'teams.company_code',
                'pvt_event_teams.*',
                'events.event_name',
                'events.year',
                'themes.theme_name',
                'papers.id as paper_id'
            )
            ->orderBy('pvt_event_teams.final_score', 'desc');

        // Filter berdasarkan judul paper (pencarian)
        if ($this->search) {
            $papers->where('papers.innovation_title', 'ILIKE', '%' . $this->search . '%');
        }

        // Filter berdasarkan company code
        if ($this->company) {
            $papers->where('teams.company_code', '=', $this->company);
        }

        // Filter berdasarkan theme
        if ($this->theme) {
            $papers->where('teams.theme_id', '=', $this->theme);
        }

        // filter berdasarkan event
        if ($this->event) {
            $papers->where('pvt_event_teams.event_id', '=', $this->event);
        }

        $papers = $papers->paginate($this->perPage);
        $currentPage = $papers->currentPage();
        $rank = 1;
        foreach ($papers as $paper) {
            $paper->rank = $rank++;
        }

        return view('livewire.evidence-table', compact('papers', 'currentPage'));
    }
}