<?php

namespace App\View\Components\assessment;

use App\Models\PvtEventTeam;
use Illuminate\View\Component;

class OndeskChartTeam extends Component
{
    public $eventId;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $totalTeamsPassToOnDesk = PvtEventTeam::where('event_id', $this->eventId)
            ->whereIn('status', ['On Desk', 'Presentation', 'Caucus', 'Presentasi BOD', 'Juara'])
            ->count();
        $totalTeamsNotPassToOnDesk = PvtEventTeam::where('event_id', $this->eventId)
            ->whereNotIn('status', ['On Desk', 'Presentation', 'Caucus', 'Presentasi BOD', 'Juara'])
            ->count();

        $totalTeams = PvtEventTeam::where('event_id', $this->eventId);
        return view('components.assessment.ondesk-chart-team', [
            'totalTeamsPassToOnDesk' => $totalTeamsPassToOnDesk,
            'totalTeamsNotPassToOnDesk' => $totalTeamsNotPassToOnDesk,
            'totalTeams' => $totalTeams->count(),
        ]);
    }
}