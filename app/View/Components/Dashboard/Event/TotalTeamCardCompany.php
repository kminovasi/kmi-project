<?php

namespace App\View\Components\Dashboard\Event;

use App\Models\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\View\Component;

class TotalTeamCardCompany extends Component
{
    public $totalTeams;

    /**
     * Create a new component instance.
     *
     * @param int $eventId
     */
    public function __construct($eventId)
    {
        // Cari event dengan relasi perusahaan
        $event = Event::with('companies.teams')->find($eventId);

        if ($event) {
            // Hitung total tim per perusahaan yang terhubung dengan event
            $this->totalTeams = $event->companies->map(function ($company) {
                return [
                    'company_name' => $company->company_name,
                    'total_teams' => $company->teams->count(),
                ];
            });
        } else {
            $this->totalTeams = collect(); // Koleksi kosong jika event tidak ditemukan
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        Log::debug($this->totalTeams);
        return view('components.dashboard.event.total-team-card-company', [
            'totalTeams' => $this->totalTeams,
        ]);
    }
}
