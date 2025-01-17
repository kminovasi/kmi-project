<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Team;
use App\Models\Paper;
use App\Models\User;
use App\Models\Category;
use App\Models\Theme;
use App\Models\Company;
use App\Models\Event;
use App\Models\Judge;
use App\Models\MetodologiPaper;
use App\Models\pvtAssesmentTeamJudge;
use App\Models\PvtAssessmentEvent;
use App\Models\PvtEventTeam;
use App\Models\PvtMember;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Log;

class GenerateTeamPaperUntilGM extends Command
{
    protected $signature = 'generate:team-paper-gm';
    protected $description = 'Generate a team and associated paper with specified data';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $createdAt = $this->askWithValidation(
            'Masukkan tanggal created_at untuk semua entri (format: YYYY-MM-DD):',
            'Y-m-d'
        );
        // Step 1: Nama Team
        $teamName = $this->ask('Masukkan nama tim:');

        // Step 2: Pilih Perusahaan
        $companies = Company::pluck('company_name', 'company_code')->toArray();
        $companyCode = $this->choice('Pilih perusahaan:', $companies);

        // Step 3: Pilih Kategori
        $categories = Category::pluck('id', 'category_name')->toArray();
        $categoryName = $this->choice('Pilih kategori:', array_keys($categories));
        $categoryId = $categories[$categoryName]; // Mengambil ID berdasarkan nama yang dipilih

        // Step 4: Pilih Tema
        $themes = Theme::pluck('id', 'theme_name')->toArray();
        $themeName = $this->choice('Pilih tema:', array_keys($themes));
        $themeId = $themes[$themeName]; // Mengambil ID berdasarkan nama yang dipilih

        // Step 5: Judul Inovasi
        $innovationTitle = $this->ask('Masukkan judul inovasi:');

        // Generate Ketua Tim
        $leader = User::where('company_code', $companyCode)->inRandomOrder()->first();
        if (!$leader) {
            $this->error('Tidak ada user tersedia untuk dijadikan ketua tim di perusahaan ini.');
            return;
        }

        // Generate anggota tim dari perusahaan yang sama
        $teamMembers = User::where('company_code', $companyCode)
            ->where('id', '<>', $leader->id)
            ->inRandomOrder()
            ->take(3)  // Misalnya ambil 3 anggota
            ->get();

        // Fasilitator dari direktorat yang sama
        $facilitator = User::where('directorate_name', $leader->directorate_name)
            ->where('id', '<>', $leader->id)
            ->inRandomOrder()
            ->first();

        // General Manager secara acak
        $generalManager = User::inRandomOrder()->first();

        // Insert data ke tabel teams
        $team = Team::create([
            'team_name' => $teamName,
            'company_code' => $companyCode,
            'category_id' => $categoryId,
            'theme_id' => $themeId,
            'status_lomba' => 'AP',
            'phone_number' => $leader->phone_number,
            'created_at' => $createdAt, // Tambahkan ini
        ]);


        // Simpan anggota tim ke dalam pvt_members
        PvtMember::create([
            'team_id' => $team->id,
            'employee_id' => $leader->employee_id,
            'status' => 'leader',
            'created_at' => $createdAt, // Tambahkan ini
        ]);


        foreach ($teamMembers as $member) {
            PvtMember::create([
                'team_id' => $team->id,
                'employee_id' => $member->employee_id,
                'status' => 'member',
                'created_at' => $createdAt, // Tambahkan ini
            ]);
        }


        // Tambahkan fasilitator dan GM ke dalam pvt_members
        PvtMember::create([
            'team_id' => $team->id,
            'employee_id' => $facilitator->employee_id,
            'status' => 'facilitator',
            'created_at' => $createdAt,
        ]);

        PvtMember::create([
            'team_id' => $team->id,
            'employee_id' => $generalManager->employee_id,
            'status' => 'gm',
            'created_at' => $createdAt,
        ]);

        // Membuat folder untuk menyimpan file berdasarkan status lomba dan nama tim

        $events = Event::pluck('event_name', 'id')->toArray();
        $eventName = $this->choice('Pilih event yang akan diikuti:', array_values($events));

        // Cari ID event berdasarkan nama yang dipilih
        $eventId = array_search($eventName, $events);
        $eventData = Event::findOrFail($eventId);
        $eventType = $eventData->type; // atau gunakan variabel lain jika ini bisa dinamis
        $storagePath = "internal/{$eventType}/{$teamName}";
        $team->update([
            'status_lomba' => $eventType
        ]);

        // Pastikan folder tersedia, jika tidak, buat foldernya
        Storage::makeDirectory($storagePath);

        // Pastikan disk yang digunakan adalah 'local'
        $localDisk = Storage::disk('local');
        $publicDisk = Storage::disk('public');

        // Tentukan path lengkap untuk setiap file
        $fullPaperPath = "{$storagePath}/full_paper.pdf";
        $innovationPhotoPath = "{$storagePath}/innovation_photo/innovation_photo.jpg";
        $proofIdeaPath = "{$storagePath}/proof_idea/proof_idea.jpg";

        // Buat folder untuk innovation_photo dan proof_idea
        $publicDisk->makeDirectory("{$storagePath}/innovation_photo");
        $publicDisk->makeDirectory("{$storagePath}/proof_idea");

        // Path sementara untuk file testing
        $localTestFullPaper = 'test-files/full-paper-test.pdf';
        $localTestImage = 'test-files/image-test.jpg';

        // Salin file full paper
        if ($localDisk->exists($localTestFullPaper)) {
            $publicDisk->put($fullPaperPath, $localDisk->get($localTestFullPaper));
            $this->info("File full paper berhasil disalin ke {$fullPaperPath}");
        } else {
            $this->error("File full paper tidak ditemukan di {$localTestFullPaper}");
        }

        // Salin file innovation photo
        if ($localDisk->exists($localTestImage)) {
            $publicDisk->put($innovationPhotoPath, $localDisk->get($localTestImage));
            $this->info("File innovation photo berhasil disalin ke {$innovationPhotoPath}");
        } else {
            $this->error("File innovation photo tidak ditemukan di {$localTestImage}");
        }

        // Salin file proof idea
        if ($localDisk->exists($localTestImage)) {
            $publicDisk->put($proofIdeaPath, $localDisk->get($localTestImage));
            $this->info("File proof idea berhasil disalin ke {$proofIdeaPath}");
        } else {
            $this->error("File proof idea tidak ditemukan di {$localTestImage}");
        }
        $randomMetodologi = MetodologiPaper::inRandomOrder()->first();


        // Simpan file paper ke dalam tabel papers
        Paper::create([
            'team_id' => $team->id,
            'step_1' => '-',
            'step_2' => '-',
            'step_3' => '-',
            'step_4' => '-',
            'step_5' => '-',
            'step_6' => '-',
            'step_7' => '-',
            'step_8' => '-',
            'financial' => 1000000,
            'potential_benefit' => 1000000,
            'innovation_title' => $innovationTitle,
            'full_paper' => $publicDisk->exists($fullPaperPath) ? 'f: ' . $fullPaperPath : null,
            'innovation_photo' => $publicDisk->exists($innovationPhotoPath) ? $innovationPhotoPath : null,
            'proof_idea' => $publicDisk->exists($proofIdeaPath) ? $proofIdeaPath : null,
            'status_inovasi' => 'Not Implemented',
            'abstract' => 'Ini adalah abstrak contoh',  // Contoh data
            'problem' => 'Masalah yang diatasi oleh inovasi ini',
            'main_cause' => 'Penyebab utama dari masalah',
            'solution' => 'Solusi inovasi yang diterapkan',
            'potensi_replikasi' => 'Bisa Direplikasi',
            'inovasi_lokasi' => 'Lokasi',
            'status' => 'accepted benefit by general manager',
            'created_at' => $createdAt, // Tambahkan ini
            'metodologi_paper_id' => $randomMetodologi->id
        ]);

        $this->info('Tim dan paper berhasil di-generate!');
    }
    /**
     * Prompt for input with date validation.
     *
     * @param string $question
     * @param string $format
     * @return string
     */
    private function askWithValidation(string $question, string $format): string
    {
        while (true) {
            $input = $this->ask($question);
            try {
                $date = Carbon::createFromFormat($format, $input);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                $this->warn("Format tanggal tidak valid. Gunakan format: {$format}");
            }
        }
    }
}
