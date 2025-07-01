<?php

namespace App\View\Components\Dashboard\Event;

use App\Models\Event;
use Illuminate\View\Component;
use DB;
use Log;

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
            ->distinct('teams.id') // hanya hitung tim unik
            ->count('teams.id');   // pastikan count-nya berdasarkan id tim
        
        $this->statistics['incrementalInnovation'] = DB::table('pvt_event_teams')
            ->join('teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            ->join('categories', 'teams.category_id', '=', 'categories.id')
            ->where('pvt_event_teams.event_id', $this->eventId)
            ->where('categories.category_parent', 'INCREMENTAL INNOVATION')
            ->distinct('teams.id')
            ->count('teams.id');

        // Gabungkan jumlah Breakthrough dan Incremental Innovation
        $this->statistics['totalInnovation'] = $this->statistics['breakthroughInnovation'] + $this->statistics['incrementalInnovation'];

        $this->statistics['ideaBox'] = DB::table('pvt_event_teams')
            ->join('teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            ->join('categories', 'teams.category_id', '=', 'categories.id')
            ->where('pvt_event_teams.event_id', $this->eventId)
            ->where('categories.category_parent', 'IDEA BOX')
            ->count();

       $inovatorByGender = DB::table('pvt_members')
            ->join('teams', 'pvt_members.team_id', '=', 'teams.id')
            ->join('papers', 'papers.team_id', '=', 'teams.id')
            ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            ->leftJoin('users', 'pvt_members.employee_id', '=', 'users.employee_id')
            ->where('pvt_event_teams.event_id', $this->eventId)
            ->where('pvt_members.status', '!=', 'gm')
            ->where('papers.status', '=', 'accepted by innovation admin')
            ->whereNotNull('users.gender')
            ->select('users.gender', DB::raw('CONCAT(pvt_members.employee_id, "-", teams.id) as unique_participation'))
            ->distinct()
            ->get()
            ->groupBy('gender');
        
        $outsourceInnovatorData = DB::table('ph2_members')
            ->join('teams', 'teams.id', '=', 'ph2_members.team_id')
            ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            ->join('papers', 'papers.team_id', '=', 'teams.id')
            ->where('pvt_event_teams.event_id', $this->eventId)
            ->where('papers.status', 'accepted by innovation admin')
            ->distinct()
            ->count(DB::raw('CONCAT(ph2_members.name, teams.id)'));
        
        // Gunakan null-safe untuk menghitung total per gender
        $this->statistics['totalInnovatorsOutsource'] = $outsourceInnovatorData;
        $this->statistics['totalInnovatorsMale'] = isset($inovatorByGender['Male']) ? $inovatorByGender['Male']->count() : 0;
        $this->statistics['totalInnovatorsFemale'] = isset($inovatorByGender['Female']) ? $inovatorByGender['Female']->count() : 0;

        $this->statistics['totalInnovators'] = $this->statistics['totalInnovatorsMale'] + $this->statistics['totalInnovatorsFemale'] + $this->statistics['totalInnovatorsOutsource'];
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