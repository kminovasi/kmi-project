<?php

namespace App\Http\Controllers;

use App\Models\BodEvent;
use Auth;
use Illuminate\Http\Request;
use Log;

class BodEventController extends Controller
{
    public function index()
    {
        // Cek apakah user yang sedang login adalah Superadmin
        $isSuperadmin = Auth::user()->role === 'Superadmin';
        $company_code = Auth::user()->company_code;

        // Jika Superadmin, tampilkan semua data
        if ($isSuperadmin) {
            $bodEvents = BodEvent::with(['user', 'event'])->get();
        } else {
            // Jika bukan Superadmin, filter berdasarkan company_code yang sama dengan user yang login
            $bodEvents = BodEvent::with(['user', 'event'])
                ->whereHas('event', function ($query) use ($company_code) {
                    $query->where('company_code', $company_code);
                })
                ->get();
        }

        return response()->json([
            'data' => $bodEvents->map(function ($bodEvent) {
                return [
                    'id' => $bodEvent->id,
                    'bod_name' => $bodEvent->user->name ?? '-',
                    'company_name' => $bodEvent->user->company_name ?? '-',
                    'position' => $bodEvent->user->position_title ?? '-',
                    'event_name' => $bodEvent->event->event_name ?? '-', // Nama event
                    'event_type' => $bodEvent->event->type ?? '-',      // Tipe event
                    'job_level' => $bodEvent->user->job_level ?? '-',
                    'status' => $bodEvent->status,
                    'action' => '
                    <button class="btn btn-sm btn-primary toggle-status-btn" data-id="' . $bodEvent->id . '" data-status="' . $bodEvent->status . '">
                        ' . ($bodEvent->isActive() ? 'Non Aktifkan' : 'Aktifkan') . '
                    </button>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="' . $bodEvent->id . '">Delete</button>
                ',
                ];
            }),
        ]);
    }




    public function destroy($id)
    {
        if (!is_numeric($id)) {
            return response()->json(['message' => 'Invalid ID'], 400);
        }

        $bodEvent = BodEvent::find($id);

        if (!$bodEvent) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        $bodEvent->delete();

        return response()->json(['message' => 'Data deleted successfully']);
    }

    public function toggleStatus($id)
    {
        $bodEvent = BodEvent::find($id);

        if (!$bodEvent) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        $bodEvent->toggleActive(); // Mengubah status aktif/nonaktif

        return response()->json([
            'message' => 'Status updated successfully',
            'status' => $bodEvent->status, // Kembalikan status terbaru
        ]);
    }
}
