<?php

namespace App\View\Components\Dashboard\Event;

use App\Models\Event;
use Illuminate\View\Component;
use DB;

class InnovatorCard extends Component
{
    public $eventId;

    public $statistics;

    /**
     * Create a new component instance.
     *
     * @param int $eventId
     */
    public function __construct($eventId)
    {
        $this->eventId = $eventId;

        // Menghitung data statistik berdasarkan event tertentu
        $this->calculateStatistics();
    }

    /**
     * Menghitung statistik inovator berdasarkan event.
     */
    private function calculateStatistics()
    {
        // Jumlah tim berdasarkan kategori utama
        $this->statistics['breakthroughInnovation'] = DB::table('pvt_event_teams')
            ->join('teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            ->join('categories', 'teams.category_id', '=', 'categories.id')
            ->where('pvt_event_teams.event_id', $this->eventId)
            ->where('categories.category_parent', 'BREAKTHROUGH INNOVATION')
            ->count();

        $this->statistics['incrementalInnovation'] = DB::table('pvt_event_teams')
            ->join('teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            ->join('categories', 'teams.category_id', '=', 'categories.id')
            ->where('pvt_event_teams.event_id', $this->eventId)
            ->where('categories.category_parent', 'INCREMENTAL INNOVATION')
            ->count();

        $this->statistics['ideaBox'] = DB::table('pvt_event_teams')
            ->join('teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            ->join('categories', 'teams.category_id', '=', 'categories.id')
            ->where('pvt_event_teams.event_id', $this->eventId)
            ->where('categories.category_parent', 'IDEA BOX')
            ->count();

        // Total inovator berdasarkan gender
        $this->statistics['totalInnovatorsMale'] = DB::table('pvt_members')
            ->join('teams', 'pvt_members.team_id', '=', 'teams.id')
            ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            ->join('users', 'pvt_members.employee_id', '=', 'users.employee_id')
            ->where('pvt_event_teams.event_id', $this->eventId)
            ->where('users.gender', 'Male')
            ->distinct('pvt_members.employee_id')
            ->count();

        $this->statistics['totalInnovatorsFemale'] = DB::table('pvt_members')
            ->join('teams', 'pvt_members.team_id', '=', 'teams.id')
            ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            ->join('users', 'pvt_members.employee_id', '=', 'users.employee_id')
            ->where('pvt_event_teams.event_id', $this->eventId)
            ->where('users.gender', 'Female')
            ->distinct('pvt_members.employee_id')
            ->count();

        $this->statistics['totalInnovators'] =
            $this->statistics['totalInnovatorsMale'] + $this->statistics['totalInnovatorsFemale'];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dashboard.event.innovator-card', [
            'statistics' => $this->statistics,
        ]);
    }
}
