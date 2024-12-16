<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class DashboardEventController extends Controller
{
    public function getActiveEvent()
    {
        $events = Event::select('*')
            ->orderByRaw("
            CASE
                WHEN status = 'active' THEN 1
                ELSE 2
            END
        ") // Status 'active' prioritas utama
            ->orderBy('date_start', 'asc') // Urutkan berdasarkan tanggal mulai setelah status
            ->get();

        return view('dashboard.event.index', compact('events'));
    }

    public function show($id)
    {
        // Cari event berdasarkan ID
        $event = Event::findOrFail($id);

        // Kirim data event ke view
        return view('dashboard.event.show', compact('event'));
    }


    public function statistics($id)
    {
        $event = Event::findOrFail($id);

        return view('dashboard.event.statistics', [
            'eventId' => $event->id,
            'eventName' => $event->event_name,
        ]);
    }
}
