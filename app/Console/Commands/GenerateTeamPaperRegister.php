<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Team;
use App\Models\Paper;
use App\Models\Category;
use App\Models\Theme;
use App\Models\Company;
use App\Models\MetodologiPaper;
use App\Models\PvtMember;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class GenerateTeamPaperRegister extends Command
{
    protected $signature = 'generate:team-paper-register';
    protected $description = 'Generate a team and associated paper with specified data';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Step 1: Input tanggal pembuatan
        $createdAt = $this->askWithValidation(
            'Masukkan tanggal created_at untuk semua entri (format: YYYY-MM-DD):',
            'Y-m-d'
        );

        // Step 2: Nama Team
        $teamName = $this->ask('Masukkan nama tim:');

        // Step 3: Pilih Perusahaan
        $companies = Company::pluck('company_name', 'company_code')->toArray();
        $companyCode = $this->choice('Pilih perusahaan:', $companies);

        // Step 4: Pilih Kategori
        $categories = Category::pluck('id', 'category_name')->toArray();
        $categoryName = $this->choice('Pilih kategori:', array_keys($categories));
        $categoryId = $categories[$categoryName];

        // Step 5: Pilih Tema
        $themes = Theme::pluck('id', 'theme_name')->toArray();
        $themeName = $this->choice('Pilih tema:', array_keys($themes));
        $themeId = $themes[$themeName];

        // Step 6: Judul Inovasi
        $innovationTitle = $this->ask('Masukkan judul inovasi:');

        // Step 7: Buat data tim
        $team = Team::create([
            'team_name' => $teamName,
            'company_code' => $companyCode,
            'category_id' => $categoryId,
            'theme_id' => $themeId,
            'status_lomba' => 'AP',
            'phone_number' => null, // Nomor telepon opsional
            'created_at' => $createdAt,
        ]);

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

        $randomMetodologi = MetodologiPaper::inRandomOrder()->first();
        Paper::create([
            'team_id' => $team->id,
            'innovation_title' => $innovationTitle,
            'financial' => 1000000,
            'potential_benefit' => 1000000,
            'status_inovasi' => 'Not Implemented',
            'abstract' => 'Ini adalah abstrak contoh',
            'problem' => 'Masalah yang diatasi oleh inovasi ini',
            'main_cause' => 'Penyebab utama dari masalah',
            'solution' => 'Solusi inovasi yang diterapkan',
            'inovasi_lokasi' => 'Lokasi',
            'status' => 'upload full paper',
            'created_at' => $createdAt,
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
