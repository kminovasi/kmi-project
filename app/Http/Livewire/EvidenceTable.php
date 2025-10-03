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
        $eligibleRanks = \DB::table('teams as t2')
            ->join('pvt_event_teams as pet2', 't2.id', '=', 'pet2.team_id')
            ->join('events as e2', 'pet2.event_id', '=', 'e2.id')
            ->where('e2.status', 'finish')
            ->whereNotNull('pet2.final_score')
            ->whereRaw('LOWER(pet2.status) = ?', ['juara'])
            ->selectRaw("
                pet2.team_id,
                pet2.event_id,
                t2.category_id,
                pet2.final_score,
                DENSE_RANK() OVER (
                    PARTITION BY pet2.event_id, t2.category_id
                    ORDER BY pet2.final_score DESC
                ) as final_rank
            ");

        $base = \DB::table('teams as t')
            ->join('pvt_event_teams as pet', 't.id', '=', 'pet.team_id')
            ->join('papers as p', 't.id', '=', 'p.team_id')
            ->join('events as e', 'pet.event_id', '=', 'e.id')
            ->join('themes as th', 't.theme_id', '=', 'th.id')
            ->leftJoinSub($eligibleRanks, 'rk', function ($join) {
                $join->on('rk.team_id', '=', 't.id')
                    ->on('rk.event_id', '=', 'pet.event_id')
                    ->on('rk.category_id', '=', 't.category_id');
            })
            ->where('t.category_id', $this->categoryId)
            ->where('e.status', 'finish')
            ->selectRaw("
                p.*,
                p.id as paper_id,
                t.id as team_id,
                t.team_name,
                t.company_code,
                t.category_id,
                th.id as theme_id,
                th.theme_name,
                e.id as event_id,
                e.event_name,
                e.year,
                pet.final_score,
                pet.status as pet_status,
                pet.is_best_of_the_best,
                pet.is_honorable_winner,
                rk.final_rank
            ");

        if ($this->search) {
            $term = trim($this->search);
            $base->where(function ($qq) use ($term) {
                $qq->where('p.innovation_title', 'LIKE', "%{$term}%")
                ->orWhere('t.team_name',        'LIKE', "%{$term}%");
            });
        }
        if ($this->company) $base->where('t.company_code', $this->company);
        if ($this->theme)   $base->where('t.theme_id', $this->theme);
        if ($this->event)   $base->where('pet.event_id', $this->event);

        $base->orderByDesc('pet.final_score')->orderBy('t.team_name');
        $base->selectRaw("
            CASE
                WHEN pet.is_best_of_the_best = 1 THEN 'Best Of The Best'
                WHEN pet.is_honorable_winner = 1 THEN 'Juara Harapan'
                WHEN LOWER(pet.status) = 'juara' AND rk.final_rank = 1 THEN 'Juara 1'
                WHEN LOWER(pet.status) = 'juara' AND rk.final_rank = 2 THEN 'Juara 2'
                WHEN LOWER(pet.status) = 'juara' AND rk.final_rank = 3 THEN 'Juara 3'
                ELSE 'Peserta'
            END AS status_label
        ");

        $papers = $base->paginate($this->perPage);

        return view('livewire.evidence-table', [
            'papers'      => $papers,
            'currentPage' => $papers->currentPage(),
            'perPage'     => $papers->perPage(),
            'company'     => $this->company,
            'event'       => $this->event,
            'theme'       => $this->theme,
        ]);
    }
}