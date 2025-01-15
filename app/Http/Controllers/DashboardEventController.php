<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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


    public function statistics(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $organizationUnit = $request->input('organization-unit'); // Ambil filter dari request
        $event_type = $event->type;
        $companies = $event->companies()->pluck('company_code', 'company_name');


        return view('dashboard.event.statistics', [
            'eventId' => $event->id,
            'eventName' => $event->event_name,
            'organizationUnit' => $organizationUnit,
            'event_type' => $event_type,
            'companies' => $companies, // Kirim daftar perusahaan ke view,
        ]);
    }
}
