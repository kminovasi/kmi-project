<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DashboardEventController extends Controller
{
    public function getActiveEvent()
    {
        $query = Event::query();

        // Jika user BUKAN superadmin, filter berdasarkan company_code
        if (Auth::user()->role !== 'Superadmin') {
            $companyCode = Auth::user()->company_code;

            $query->whereHas('companies', function ($q) use ($companyCode) {
                $q->where('company_code', $companyCode);
            });
        }

        $events = $query->orderByRaw("
                CASE
                    WHEN status = 'active' THEN 1
                    ELSE 2
                END
            ")
            ->orderBy('date_start', 'asc')
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