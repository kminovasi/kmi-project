<?php

namespace App\View\Components\Dashboard\Event;

use App\Models\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\View\Component;

class TotalTeamCompanyChart extends Component
{
    public $chartData;
    public $eventId;
    public $eventName;

    /**
     * Create a new component instance.
     *
     * @param int $eventId
     * @return void
     */
    public function __construct($eventId)
    {
        $this->eventId = $eventId;

        $event = Event::with(['companies.teams'])->find($eventId);

        if (!$event) {
            Log::error("Event with ID $eventId not found.");
            $this->chartData = [];
            $this->eventName = 'Unknown Event';
            return;
        }

        $this->eventName = $event->event_name;

        $this->chartData = $event->companies->map(function ($company) {
            return [
                'company_name' => $company->company_name,
                'total_teams' => $company->teams->count(),
            ];
        });
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dashboard.event.total-team-company-chart', [
            'chartData' => $this->chartData,
            'eventName' => $this->eventName
        ]);
    }
}
