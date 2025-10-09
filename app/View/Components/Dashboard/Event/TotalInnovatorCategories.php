<?php

namespace App\View\Components\Dashboard\Event;

use App\Models\Event;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route as Router;


class TotalInnovatorCategories extends Component
{
    public $eventId;
    public $chartData;
    public $event_name;

    public function __construct($eventId)
    {
        $this->eventId = $eventId;
        $this->chartData = $this->fetchChartData();
        $this->event_name = Event::find($eventId)->event_name;
    }

    private function fetchChartData()
    {
        // Ambil data kategori dengan total inovator yang memiliki status 'accepted by innovation admin'
        $data = DB::table('papers')
            ->join('teams', 'papers.team_id', '=', 'teams.id')
            ->join('categories', 'teams.category_id', '=', 'categories.id')
            ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->select('categories.category_name', DB::raw('COUNT(DISTINCT papers.id) as total_innovators'))
            ->where('pvt_event_teams.event_id', $this->eventId)
            ->where('papers.status', 'accepted by innovation admin')
            ->groupBy('categories.category_name')
            ->orderBy('total_innovators', 'desc')
            ->get();

        // Format data untuk digunakan di Chart.js
        $categories = [];
        $totals = [];
        $colors = [
            'rgba(54, 162, 235, 1)',  // Biru
            'rgba(255, 99, 132, 1)',  // Merah
            'rgba(255, 206, 86, 1)',  // Kuning
            'rgba(75, 192, 192, 1)',  // Hijau Muda
            'rgba(153, 102, 255, 1)', // Ungu
            'rgba(255, 159, 64, 1)'   // Oranye
        ];

        foreach ($data as $index => $row) {
            $categories[] = $row->category_name;
            $totals[] = $row->total_innovators;
        }

        return [
            'labels' => $categories,
            'data' => $totals,
            'colors' => $colors
        ];
    }

    public function render()
    {
        $name = 'dashboard-event.event.category-innovators';
        $categoryRoute = Router::has($name)
            ? route($name, $this->eventId)
            : url("/dashboard-event/event/{$this->eventId}/category-innovators");

        return view('components.dashboard.event.total-innovator-categories', [
            'chartData'     => $this->chartData,
            'event_name'    => $this->event_name,
            'eventId'       => $this->eventId,
            'categoryRoute' => $categoryRoute,
        ]);
    }
}
