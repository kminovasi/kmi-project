<?php

namespace App\Http\Controllers;

use App\Models\Timeline;
use App\Models\Event;
use Illuminate\Http\Request;

class TimelineController extends Controller
{
    public function index()
    {
        $timelines = Timeline::with('event')->get();
        $events = Event::all();
        return view('admin.timeline.index', compact('timelines', 'events'));
    }

    public function create()
    {
        $events = Event::all();
        return view('admin.timeline.create', compact('events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'judul_kegiatan' => 'required|string|max:255',
            'deskripsi' => 'required|string',
        ]);

        $event = Event::find($request->event_id);

        if ($request->tanggal_mulai < $event->date_start || $request->tanggal_mulai > $event->date_end) {
            return back()->withErrors(['tanggal_mulai' => 'Tanggal mulai harus berada dalam rentang tanggal event.']);
        }

        if ($request->tanggal_selesai < $event->date_start || $request->tanggal_selesai > $event->date_end) {
            return back()->withErrors(['tanggal_selesai' => 'Tanggal selesai harus berada dalam rentang tanggal event.']);
        }

        Timeline::create($request->all());

        return redirect()->route('timeline.index')->with('success', 'Timeline created successfully.');
    }

    public function edit($id)
    {
        $timeline = Timeline::findOrFail($id);
        $events = Event::all();
        return view('admin.timeline.edit', compact('timeline', 'events'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'judul_kegiatan' => 'required|string|max:255',
            'deskripsi' => 'required|string',
        ]);

        $event = Event::find($request->event_id);

        if ($request->tanggal_mulai < $event->date_start || $request->tanggal_mulai > $event->date_end) {
            return back()->withErrors(['tanggal_mulai' => 'Tanggal mulai harus berada dalam rentang tanggal event.']);
        }

        if ($request->tanggal_selesai < $event->date_start || $request->tanggal_selesai > $event->date_end) {
            return back()->withErrors(['tanggal_selesai' => 'Tanggal selesai harus berada dalam rentang tanggal event.']);
        }

        $timeline = Timeline::findOrFail($id);
        $timeline->update($request->all());

        return redirect()->route('timeline.index')->with('success', 'Timeline updated successfully.');
    }

    public function destroy($id)
    {
        $timeline = Timeline::findOrFail($id);
        $timeline->delete();

        return redirect()->route('timeline.index')->with('success', 'Timeline deleted successfully.');
    }
}
