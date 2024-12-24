<?php

namespace App\View\Components\Dashboard\Innovator;

use Illuminate\View\Component;
use App\Models\Event;
use App\Models\PvtMember;
use App\Models\Timeline;

class ScheduleEvent extends Component
{
    public $activeEvents;

    public function __construct()
    {
        $user = auth()->user();
        $teamIds = PvtMember::where('employee_id', $user->employee_id)
                            ->whereIn('status', ['member', 'leader'])
                            ->pluck('team_id');

        $this->activeEvents = Event::whereHas('pvtEventTeams', function ($query) use ($teamIds) {
            $query->whereIn('team_id', $teamIds);
        })->where('status', 'active')->get();
    }

    public function render()
    {
        return view('components.dashboard.innovator.schedule-event', [
            'activeEvents' => $this->activeEvents
        ]);
    }
}
