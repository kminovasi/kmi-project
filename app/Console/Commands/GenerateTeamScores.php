<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Models\PvtAssessmentEvent;
use App\Models\PvtEventTeam;
use App\Models\PvtAssesmentTeamJudge;

class GenerateTeamScores extends Command
{
    protected $signature = 'generate:team-scores';
    protected $description = 'Generate team scores for each stage of the selected event';

    public function handle()
    {
        $events = Event::pluck('id', 'event_name')->toArray(); // Perbaiki urutan pluck
        $eventId = $this->choice('Pilih event yang akan dinilai:', $events);

        // Pastikan $eventId dikonversi ke integer
        $eventId = (int) $eventId;

        // 2. Ambil semua tim dalam event ini
        $teams = PvtEventTeam::where('event_id', $eventId)->get();

        foreach ($teams as $team) {
            $this->info("Menilai Tim ID: {$team->id}");

            // Dapatkan penilaian dari tiap stage untuk tiap juri
            $this->evaluateStage($team, 'ondesk', 'total_score_on_desk');
            $this->evaluateStage($team, 'presentation', 'total_score_presentation');
            $this->evaluateStage($team, 'caucus', 'total_score_caucus');

            // Nilai Final untuk presentasi BOD langsung mengisi `final_score`
            $this->evaluateBODStage($team);
        }

        $this->info("Penilaian semua tim selesai.");
    }

    /**
     * Method untuk mengevaluasi dan menghitung skor dari juri pada stage tertentu.
     */
    private function evaluateStage($team, $stage, $scoreColumn)
    {
        // Dapatkan semua assessment events dengan stage tertentu untuk event dan team ini
        $assessmentEvents = PvtAssessmentEvent::where('event_id', $team->event_id)
            ->where('stage', $stage)
            ->get();

        $totalScore = 0;
        $judgeCount = 0;

        foreach ($assessmentEvents as $assessmentEvent) {
            // Ambil nilai dari tiap juri untuk event ini
            $judgesScores = PvtAssesmentTeamJudge::where('event_team_id', $team->id)
                ->where('assessment_event_id', $assessmentEvent->id)
                ->where('stage', $stage)
                ->get();

            foreach ($judgesScores as $score) {
                // Pastikan nilai tidak melebihi score_max
                if ($score->score !== null && $score->score <= $assessmentEvent->score_max) {
                    $totalScore += $score->score;
                    $judgeCount++;
                } else {
                    $this->warn("Nilai juri melebihi score_max untuk assessment_event_id: {$assessmentEvent->id}");
                }
            }
        }

        // Hitung rata-rata dan simpan di kolom yang sesuai di PvtEventTeam
        if ($judgeCount > 0) {
            $averageScore = $totalScore / $judgeCount;
            $team->$scoreColumn = $averageScore;
            $team->save();

            $this->info("Total rata-rata skor $stage untuk Tim ID {$team->id} adalah $averageScore.");
        } else {
            $this->warn("Tidak ada nilai untuk stage $stage pada Tim ID {$team->id}.");
        }
    }

    /**
     * Method untuk menilai tahap presentasi BOD secara langsung pada kolom final_score.
     */
    private function evaluateBODStage($team)
    {
        $finalScore = 0;

        // Ambil semua nilai dari BOD pada tahap "bod_presentation" untuk tim ini
        $bodScores = PvtAssesmentTeamJudge::where('event_team_id', $team->id)
            ->where('stage', 'bod_presentation')
            ->get();

        foreach ($bodScores as $score) {
            $finalScore += $score->score ?? 0; // Skor final mutlak dari BOD
        }

        // Simpan ke kolom final_score di PvtEventTeam
        $team->final_score = $finalScore;
        $team->save();

        $this->info("Nilai akhir (final_score) untuk Tim ID {$team->id} adalah $finalScore.");
    }
}
