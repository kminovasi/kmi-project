<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Models\AssessmentPoint;
use App\Models\PvtAssessmentEvent;
use App\Models\Judge;
use Log;

class RegisterAssessmentTemplate extends Command
{
    protected $signature = 'register:assessment-template';
    protected $description = 'Register assessment template to an event';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Step 1: Pilih Event
        $events = Event::all();
        if ($events->isEmpty()) {
            $this->error("Tidak ada event yang tersedia.");
            return;
        }

        // Menyiapkan opsi event dengan tampilan lengkap
        $eventOptions = $events->mapWithKeys(function ($event) {
            return [$event->id => $event->id . ' - ' . $event->event_name];
        })->toArray();

        // Mengambil ID event yang dipilih
        $eventIdLabel = $this->choice(
            'Pilih event yang ingin didaftarkan template penilaian:',
            $eventOptions
        );

        // Menyimpan hanya `id` (angka) dari `eventIdLabel` yang dipilih
        $eventId = array_search($eventIdLabel, $eventOptions);

        // Step 2: Pilih Poin-Poin Penilaian
        $assessmentPoints = AssessmentPoint::all();
        if ($assessmentPoints->isEmpty()) {
            $this->error("Tidak ada poin penilaian yang tersedia.");
            return;
        }

        $allPointsOption = $this->choice(
            'Apakah ingin mendaftarkan semua poin penilaian?',
            ['Ya', 'Tidak'],
            1
        );

        $selectedPoints = [];
        if ($allPointsOption === 'Ya') {
            $selectedPoints = $assessmentPoints->pluck('id')->toArray();
        } else {
            $pointOptions = $assessmentPoints->pluck('point', 'id')->toArray();
            $selectedPoints = $this->choice(
                'Pilih poin-poin yang akan ditetapkan sebagai penilaian (pisahkan dengan koma jika lebih dari satu):',
                $pointOptions,
                null,
                null,
                true
            );
        }

        // Step 3: Pilih Poin-Poin yang Diaktifkan
        $activeAllOption = $this->choice(
            'Apakah ingin mengaktifkan semua poin yang dipilih?',
            ['Ya', 'Tidak'],
            1
        );

        $activePoints = [];
        if ($activeAllOption === 'Ya') {
            $activePoints = $selectedPoints;
        } else {
            $pointOptions = $assessmentPoints->whereIn('id', $selectedPoints)->pluck('point', 'id')->toArray();
            $activePoints = $this->choice(
                'Pilih poin-poin yang akan diaktifkan statusnya (pisahkan dengan koma jika lebih dari satu):',
                $pointOptions,
                null,
                null,
                true
            );
        }

        // Memasukkan data ke dalam `pvt_assessment_events`
        foreach ($selectedPoints as $pointId) {
            $point = $assessmentPoints->where('id', $pointId)->first();
            if ($point) {
                PvtAssessmentEvent::create([
                    'event_id' => $eventId,
                    'point' => $point->point,
                    'detail_point' => $point->detail_point,
                    'pdca' => $point->pdca,
                    'score_max' => $point->score_max,  // Menggunakan nilai `score_max` dari AssessmentPoint
                    'stage' => $point->stage,
                    'category' => $point->category,
                    'status_point' => in_array($pointId, $activePoints) ? 'active' : 'inactive',
                ]);
            }
        }

        $this->info("Template penilaian berhasil didaftarkan untuk event yang dipilih.");

        // Step 4: Menanyakan apakah ingin menambahkan juri ke event
        $addJudgesOption = $this->choice(
            'Apakah Anda ingin mendaftarkan juri untuk event ini?',
            ['Ya', 'Tidak'],
            0
        );

        if ($addJudgesOption === 'Ya') {
            // Daftar employee_id juri yang telah ditentukan
            $judges = [
                ['employee_id' => 6768, 'event_id' => $eventId],
                ['employee_id' => 743, 'event_id' => $eventId],
            ];

            foreach ($judges as $judgeData) {
                Judge::create([
                    'employee_id' => $judgeData['employee_id'],
                    'event_id' => $judgeData['event_id'],
                    'letter_path' => null,  // Nilai default jika tidak ada letter_path
                    'status' => 'active',   // Status default
                ]);
            }

            $this->info("Juri berhasil didaftarkan untuk event yang dipilih.");
        }
    }
}
