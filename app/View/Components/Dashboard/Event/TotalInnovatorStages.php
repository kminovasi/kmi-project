<?php

namespace App\View\Components\Dashboard\Event;

use App\Models\PvtEventTeam;
use Illuminate\View\Component;

class TotalInnovatorStages extends Component
{
    public $chartData;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($eventId)
    {
        // Ambil jumlah peserta berdasarkan status per event
        $statuses = [
            'On Desk',
            'Presentation',
            'tidak lolos On Desk',
            'tidak lolos Presentation',
            'Lolos Presentation',
            'Tidak lolos Caucus',
            'Caucus',
            'Presentation BOD',
            'Juara'
        ];

        $data = PvtEventTeam::where('event_id', $eventId)
            ->selectRaw('status, COUNT(*) as total')
            ->whereIn('status', $statuses)
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        // Inisialisasi data untuk setiap status agar tidak ada yang kosong
        $chartData = [];
        foreach ($statuses as $status) {
            $chartData[] = $data[$status] ?? 0;
        }

        $this->chartData = [
            'labels' => $statuses,
            'data' => $chartData
        ];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dashboard.event.total-innovator-stages', [
            'chartData' => $this->chartData
        ]);
    }
}
