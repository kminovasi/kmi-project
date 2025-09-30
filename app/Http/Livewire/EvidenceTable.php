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
    $q = \DB::table('teams')
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

    // filter
    if ($this->search) {
    $term = trim($this->search);

    $q->where(function ($qq) use ($term) {
        $qq->where('papers.innovation_title', 'LIKE', '%'.$term.'%')
           ->orWhere('teams.team_name',        'LIKE', '%'.$term.'%');
    });
    }
    if ($this->company) $q->where('teams.company_code', $this->company);
    if ($this->theme)   $q->where('teams.theme_id', $this->theme);
    if ($this->event)   $q->where('pvt_event_teams.event_id', $this->event);

    $papers = $q->paginate($this->perPage);

    $startRank = ($papers->currentPage() - 1) * $papers->perPage() + 1;

    foreach ($papers as $i => $paper) {
        $paper->rank = $startRank + $i;
        $paper->status_label = match (true) {
            $paper->rank === 1 => 'Juara 1',
            $paper->rank === 2 => 'Juara 2',
            $paper->rank === 3 => 'Juara 3',
            default            => 'Peserta',
        };
    }

    return view('livewire.evidence-table', [
        'papers' => $papers,
        'currentPage' => $papers->currentPage(),
    ]);
}}