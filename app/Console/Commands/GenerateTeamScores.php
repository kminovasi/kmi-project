<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Models\Judge;
use App\Models\MinimumscoreEvent;
use App\Models\NewSofi;
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
            $this->evaluateOndeskStage($team, 'on desk', 'total_score_on_desk');
            $this->evaluatePresentationStage($team, 'presentation', 'total_score_presentation');
            $this->evaluateCaucusStage($team, 'caucus', 'total_score_caucus');
        }

        $this->info("Penilaian semua tim selesai.");
    }

    /**
     * Method untuk mengevaluasi dan menghitung skor dari juri pada stage tertentu.
     */
    private function evaluateOndeskStage($team, $stage, $scoreColumn)
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

                // Jika tidak ada nilai juri, berikan nilai acak antara 0 dan score_max
                $randomScore = rand(60, $assessmentEvent->score_max);
                $totalScore += $randomScore;

                PvtAssesmentTeamJudge::where('judge_id', $judge->id)
                    ->where('assessment_event_id', $assessmentEvent->id)
                    ->where('event_team_id', $team->id)->update([
                        'judge_id' => $judge->id,
                        'score' => $randomScore,
                        'event_team_id' => $team->id, // ID tim yang dinilai
                        'assessment_event_id' => $assessmentEvent->id,
                        'stage' => 'on desk',
                    ]);

                $this->info("Juri " . $judge->id . " Memberikan nilai acak $randomScore untuk Tim ID {$team->id} pada assessment_event_id: {$assessmentEvent->id}");
            }
        }


        if ($stage === 'on desk') {
            if ($judgeCount > 0) {
                $averageScore = $totalScore / $judgeCount;
                $getMinimumScoreOda = MinimumscoreEvent::where('event_id', $team->event_id)->select('score_minimum_oda')->first();
                $team->$scoreColumn = $averageScore;
                if ($averageScore >= $getMinimumScoreOda->score_minimum_oda) {
                    $team->status = "Presentation";
                } else {
                    $team->status = "tidak lolos On Desk";
                }
                $team->save();


                $this->info("Total rata-rata skor $stage untuk Tim ID {$team->id} adalah $averageScore.");
                $this->averageScoreOnDesk = $averageScore;
            } else {
                $this->warn("Tidak ada nilai untuk stage $stage pada Tim ID {$team->id}.");
            }
        }
        $this->setNewSofi($team->id);
    }

    private function evaluatePresentationStage($team, $stage, $scoreColumn)
    {
        // Dapatkan semua assessment events dengan stage tertentu untuk event ini
        $assessmentEvents = PvtAssessmentEvent::where('event_id', $team->event_id)
            ->whereIn('stage', ['on desk', 'presentation'])
            ->get();

        $totalScore = 0;

        $judges = Judge::where('event_id', $team->event_id)->get();
        $judgeCount = Judge::where('event_id', $team->event_id)->count();
        foreach ($judges as $judge) {
            foreach ($assessmentEvents as $assessmentEvent) {
                // Jika tidak ada nilai juri, berikan nilai acak antara 0 dan score_max
                $randomScore = rand(75, $assessmentEvent->score_max);
                $totalScore += $randomScore;

                PvtAssesmentTeamJudge::where('judge_id', $judge->id)
                    ->where('assessment_event_id', $assessmentEvent->id)
                    ->where('event_team_id', $team->id)->create([
                        'judge_id' => $judge->id,
                        'score' => $randomScore,
                        'event_team_id' => $team->id, // ID tim yang dinilai
                        'assessment_event_id' => $assessmentEvent->id,
                        'stage' => 'presentation',
                    ]);

                $this->info("Juri " . $judge->id . " Memberikan nilai acak $randomScore untuk Tim ID {$team->id} pada assessment_event_id: {$assessmentEvent->id}");
            }
        }


        if ($judgeCount > 0) {
            $averageScore = $totalScore / $judgeCount;
            $getMinimumScorePa = MinimumscoreEvent::where('event_id', $team->event_id)->select('score_minimum_pa')->first();
            if ($team->status !== 'tidak lolos On Desk') {
                if ($averageScore >= $getMinimumScorePa->score_minimum_pa) {
                    $team->status = "Caucus";
                } else {
                    $team->status = "tidak lolos Presentation";
                }
            }
            $team->$scoreColumn = $averageScore;
            $this->presentationScore = $averageScore;
            $team->save();

            $this->info("Total rata-rata skor $stage untuk Tim ID {$team->id} adalah $averageScore.");
        } else {
            $this->warn("Tidak ada nilai untuk stage $stage pada Tim ID {$team->id}.");
        }

        $this->info("Total rata-rata skor $stage untuk Tim ID {$team->id} adalah $this->presentationScore.");
        $this->setNewSofi($team->id);
    }
    private function evaluateCaucusStage($team, $stage, $scoreColumn)
    {
        // Dapatkan semua assessment events dengan stage tertentu untuk event ini
        $assessmentEvents = PvtAssessmentEvent::where('event_id', $team->event_id)
            ->whereIn('stage', ['on desk', 'presentation'])
            ->get();

        $totalScore = 0;

        $judges = Judge::where('event_id', $team->event_id)->get();
        $judgeCount = Judge::where('event_id', $team->event_id)->count();
        foreach ($judges as $judge) {
            foreach ($assessmentEvents as $assessmentEvent) {

                // Jika tidak ada nilai juri, berikan nilai acak antara 0 dan score_max
                $randomScore = rand(75, $assessmentEvent->score_max);
                $totalScore += $randomScore;

                PvtAssesmentTeamJudge::where('judge_id', $judge->id)
                    ->where('assessment_event_id', $assessmentEvent->id)
                    ->where('event_team_id', $team->id)->create([
                        'judge_id' => $judge->id,
                        'score' => $randomScore,
                        'event_team_id' => $team->id, // ID tim yang dinilai
                        'assessment_event_id' => $assessmentEvent->id,
                        'stage' => 'caucus',
                    ]);

                $this->info("Juri caucus" . $judge->id . " Memberikan nilai acak $randomScore untuk Tim ID {$team->id} pada assessment_event_id: {$assessmentEvent->id}");
            }
        }


        if ($judgeCount > 0) {
            $averageScore = $totalScore / $judgeCount;
            $getMinimumScorePa = MinimumscoreEvent::where('event_id', $team->event_id)->select('score_minimum_pa')->first();
            if ($team->status !== 'tidak lolos On Desk') {
                if ($averageScore >= $getMinimumScorePa->score_minimum_pa) {
                    $team->status = "Presentation BOD";
                } else {
                    $team->status = "Tidak lolos Caucus";
                }
            }
            $team->$scoreColumn = $averageScore;
            $team->final_score = $averageScore;
            $team->save();

            $this->info("Total rata-rata skor $stage untuk Tim ID {$team->id} adalah $averageScore.");
        } else {
            $this->warn("Tidak ada nilai untuk stage $stage pada Tim ID {$team->id}.");
        }

        $this->info("Total rata-rata skor $stage untuk Tim ID {$team->id} adalah $this->presentationScore.");

        $team->save();
        $this->setNewSofi($team->id);
    }

    private function setNewSofi($event_team_id)
    {
        // Periksa apakah data dengan event_team_id yang dimaksud sudah ada
        $exists = NewSofi::where('event_team_id', $event_team_id)->exists();

        if ($exists) {
            // Jika data ada, lakukan update
            NewSofi::where('event_team_id', $event_team_id)
                ->update([
                    'strength' => 'tes',
                    'opportunity_for_improvement' => 'tes',
                    'recommend_category' => 'tes',
                    'suggestion_for_benefit' => 'tes'
                ]);
        } else {
            // Jika data tidak ada, Anda dapat memilih untuk menambahkannya
            NewSofi::create([
                'event_team_id' => $event_team_id,
                'strength' => 'tes',
                'opportunity_for_improvement' => 'tes',
                'recommend_category' => 'tes',
                'suggestion_for_benefit' => 'tes'
            ]);
        }
    }
}
