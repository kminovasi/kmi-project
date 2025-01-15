<?php

namespace App\View\Components\Dashboard\Event;

use App\Models\Event;
use App\Models\PvtMember;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TotalInnovatorOrganization extends Component
{
    public $chartData;
    public $eventId;
    public $organizationUnit;
    public $eventName;
    public $companyName;
    public $canvasId;

    /**
     * Create a new component instance.
     *
     * @param int $eventId
     * @param string|null $organizationUnit
     * @param string|null $companyCode
     */
    public function __construct($eventId, $organizationUnit = null, $companyCode = null, $companyName = null, $canvasId = null)
    {
        $this->eventId = $eventId;
        $this->organizationUnit = $organizationUnit;

        // Ambil nama acara hanya sekali
        $event = Event::findOrFail($eventId);
        $this->eventName = $event->event_name;
        $this->companyName = $companyName;
        $this->canvasId = $canvasId;
        // Validasi kolom organisasi yang diperbolehkan
        $validOrganizationUnits = [
            'directorate_name',
            'group_function_name',
            'department_name',
            'unit_name',
            'section_name',
            'sub_section_of',
        ];

        // Default ke 'directorate_name' jika tidak ada filter
        $organizationColumn = in_array($organizationUnit, $validOrganizationUnits)
            ? $organizationUnit
            : 'directorate_name';

        // Query data dengan filter event dan perusahaan (jika diberikan)
        $this->chartData = PvtMember::select(
            DB::raw("COALESCE(users.$organizationColumn, '-') as organization_unit"),
            DB::raw('COUNT(DISTINCT pvt_members.employee_id) as total_innovators')
        )
            ->join('teams', 'pvt_members.team_id', '=', 'teams.id')
            ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->join('users', 'pvt_members.employee_id', '=', 'users.employee_id')
            ->where('pvt_event_teams.event_id', $this->eventId)
            ->whereIn('pvt_members.status', ['leader', 'member'])
            ->when($companyCode, function ($query, $companyCode) {
                // Tambahkan filter untuk kode perusahaan jika diberikan
                $query->where('teams.company_code', $companyCode);
            })
            ->groupBy("users.$organizationColumn")
            ->orderBy('total_innovators', 'desc')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->organization_unit => $item->total_innovators];
            });
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dashboard.event.total-innovator-organization', [
            'chartData' => $this->chartData,
            'eventId' => $this->eventId,
            'event_name' => $this->eventName,
            'organizationUnit' => $this->organizationUnit,
            'companyName' => $this->companyName,
            'canvasId' => $this->canvasId,
        ]);
    }
}
