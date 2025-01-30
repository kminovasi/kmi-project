<?php

namespace App\View\Components\Profile;

use App\Models\Team;
use Illuminate\View\Component;

class ListPaper extends Component
{
    public $teamIds;
    public $teams;
    public $pvtEventTeams;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($teamIds)
    {
        $this->teamIds = $teamIds;
        $this->teams = Team::with(['paper', 'pvtEventTeams'])->whereIn('id', $teamIds)->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.profile.list-paper', [
            'teams' => $this->teams,
        ]);
    }
}
