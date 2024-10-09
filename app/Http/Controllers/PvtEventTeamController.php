<?php

namespace App\Http\Controllers;

use App\Models\PvtEventTeam;
use Illuminate\Http\Request;
use Log;

class PvtEventTeamController extends Controller
{
    public function updateScoreKeputusanBOD(Request $request)
    {
        // Ambil string JSON dan bersihkan spasi atau karakter whitespace
        $json_data = trim($request->input('selected_data_team'));

        // Decode string JSON menjadi array PHP
        $selected_data_team = json_decode($json_data, true);

        // Akses team_id dari array yang sudah didecode
        $team_id = $selected_data_team['team_id(removed)'] ?? null;
        $event_team_id = $selected_data_team['event_team_id(removed)'] ?? null;

        $pvtEventTeamItem = PvtEventTeam::findOrFail($event_team_id);
        $pvtEventTeamItem->update([
            'final_score' => $request->val_peringkat
        ]);
        return redirect()->route('assessment.presentasiBOD')->with('success', 'keputusan score berhasil di ubah');
    }

}
