<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Models\Judge;
use App\Models\PvtAssessmentEvent;
use App\Models\PvtEventTeam;
use App\Models\PvtAssesmentTeamJudge;
use Log;

class GenerateTeamScores extends Command
{
    protected $signature = 'generate:team-scores';
    protected $description = 'Generate team scores for each stage of the selected event';
    protected $averageScoreOnDesk = 0;
    protected $presentationScore = 0;

    public function handle()
    {
        // Ambil daftar events dengan id sebagai nilai dan event_name sebagai kunci
        $events = Event::pluck('id', 'event_name')->toArray(); // Kunci adalah nama event, nilai adalah id

        // Tampilkan pilihan kepada pengguna dan ambil ID yang dipilih
        $eventName = $this->choice('Pilih event yang akan dinilai:', array_keys($events));

        // Mendapatkan ID dari nama event yang dipilih
        $eventId = $events[$eventName]; // Mengambil ID berdasarkan nama event yang dipilih


        // Pastikan eventId adalah integer
        $eventId = (int) $eventId;

        // 2. Ambil semua tim dalam event ini
        $teams = PvtEventTeam::where('event_id', $eventId)->get();

        foreach ($teams as $team) {
            $this->info("Menilai Tim ID: {$team->id}");

            // Dapatkan penilaian dari tiap stage untuk tiap juri
            $this->evaluateStage($team, 'on desk', 'total_score_on_desk');
            $this->evaluateStage($team, 'presentation', 'total_score_presentation');
            $this->evaluateStage($team, 'caucus', 'total_score_caucus');
        }

        $this->info("Penilaian semua tim selesai.");
    }

    /**
     * Method untuk mengevaluasi dan menghitung skor dari juri pada stage tertentu.
     */
    private function evaluateStage($team, $stage, $scoreColumn)
    {
        // Dapatkan semua assessment events dengan stage tertentu untuk event ini
        $assessmentEvents = PvtAssessmentEvent::where('event_id', $team->event_id)
            ->where('stage', $stage)
            ->get();

        $totalScore = 0;

        $judges = Judge::where('event_id', $team->event_id)->get();
        $judgeCount = Judge::where('event_id', $team->event_id)->count();
        foreach ($judges as $judge) {
            foreach ($assessmentEvents as $assessmentEvent) {
                // Ambil nilai dari tiap juri untuk event ini
                PvtAssesmentTeamJudge::where('assessment_event_id', $assessmentEvent->id)
                    ->where('stage', $stage)
                    ->get();

                // Jika tidak ada nilai juri, berikan nilai acak antara 0 dan score_max
                $randomScore = rand(0, $assessmentEvent->score_max);
                $totalScore += $randomScore;

                PvtAssesmentTeamJudge::where('judge_id', $judge->id)
                    ->where('assessment_event_id', $assessmentEvent->id)
                    ->where('event_team_id', $team->id)->update([
                        'judge_id' => $judge->id,
                        'score' => $randomScore,
                        'event_team_id' => $team->id, // ID tim yang dinilai
                        'assessment_event_id' => $assessmentEvent->id,
                        'stage' => $stage,
                    ]);

                $this->info("Juri " . $judge->id . " Memberikan nilai acak $randomScore untuk Tim ID {$team->id} pada assessment_event_id: {$assessmentEvent->id}");
            }
        }


        if ($stage === 'on desk') {
            if ($judgeCount > 0) {
                $averageScore = $totalScore / $judgeCount;
                $team->$scoreColumn = $averageScore;
                $team->save();

                $this->info("Total rata-rata skor $stage untuk Tim ID {$team->id} adalah $averageScore.");
                $this->averageScoreOnDesk = $averageScore;
            } else {
                $this->warn("Tidak ada nilai untuk stage $stage pada Tim ID {$team->id}.");
            }
        } else if ($stage === 'presentation') {
            if ($judgeCount > 0) {
                $averageScore = $totalScore / $judgeCount;
                $team->$scoreColumn = $averageScore + $this->averageScoreOnDesk;
                $this->presentationScore = $averageScore + $this->averageScoreOnDesk;
                $team->save();

                $this->info("Total rata-rata skor $stage untuk Tim ID {$team->id} adalah $averageScore.");
            } else {
                $this->warn("Tidak ada nilai untuk stage $stage pada Tim ID {$team->id}.");
            }
        } else if ($stage === 'caucus') {
            $team->$scoreColumn = $this->presentationScore;
            $team->final_score = $this->presentationScore;
            $team->save();
            $this->info("Total rata-rata skor $stage untuk Tim ID {$team->id} adalah $this->presentationScore.");
        }
    }
}
