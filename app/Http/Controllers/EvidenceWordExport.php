<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\PvtEventTeam;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;

class EvidenceWordExport extends Controller
{
    public function downloadWord($teamId)
    {
        // Ambil data tim + anggota + papers
        $team = Team::with(['papers', 'pvtMembers.user'])->findOrFail($teamId);
        
        // Ambil data PvtEventTeam terkait jika ada
        $eventTeam = PvtEventTeam::where('team_id', $teamId)->first();
        
        // Render Blade menjadi HTML
        $html = View::make('auth.admin.dokumentasi.evidence.export-detail', compact('team', 'eventTeam'))->render();

        // Konversi HTML ke format Word
        $fileName = "team_data_{$team->team_name}.doc";
        $storagePath = storage_path("app/public/download-detail-evidence");

        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0777, true);
        }

        $filePath = "$storagePath/$fileName";
        file_put_contents($filePath, $html);

        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }
}