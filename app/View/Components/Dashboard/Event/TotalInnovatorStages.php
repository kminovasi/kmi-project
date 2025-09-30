<?php

namespace App\View\Components\Dashboard\Event;

use App\Models\Event;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;

class TotalInnovatorStages extends Component
{
    public $chartData;
    public $event_name;
    public $totalTeams;

    public function __construct($eventId)
    {
        $statuses = [
            'tidak lolos On Desk',
            'On Desk',
            'tidak lolos Presentation',
            'Presentation',
            'Tidak lolos Caucus',
            'Caucus',
            'Presentation BOD',
            'Juara'
        ];

        // Ambil nama event
        $event = Event::findOrFail($eventId);
        $this->event_name = $event->event_name;

        // Ambil data status terakhir semua tim dalam event
        $teams = DB::table('pvt_event_teams')
            ->where('event_id', $eventId)
            ->whereIn('status', $statuses)
            ->select('team_id', 'status')
            ->distinct()
            ->get();

        // Kelompokkan berdasarkan status
        $grouped = $teams->groupBy('status');

        // Susun chart data
        $chartData = [];
        foreach ($statuses as $status) {
            $chartData[] = $grouped->has($status) ? $grouped[$status]->count() : 0;
        }

        // Hitung total tim unik
        $this->totalTeams = $teams->pluck('team_id')->unique()->count();

        $this->chartData = [
            'labels' => $statuses,
            'data' => $chartData
        ];
    }

    public function render()
    {
        return view('components.dashboard.event.total-innovator-stages', [
            'chartData' => $this->chartData,
            'event_name' => $this->event_name,
            'totalTeams' => $this->totalTeams
        ]);
    }
}
