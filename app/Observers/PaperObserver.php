<?php

namespace App\Observers;

use App\Models\MetodologiPaper;
use App\Models\Paper;
use App\Models\Team;
use Mpdf\Mpdf;
use PDFMerger;

class PaperObserver
{
    protected $models = [
        Paper::class,
    ];

    public function updating(Paper $paper)
    {
        $metodologiPaper = MetodologiPaper::findOrFail($paper->metodologi_paper_id);
        $maxStep = $metodologiPaper->step;

        $isComplete = true;

        for ($i = 1; $i <= $maxStep; $i++) {
            $stepField = "step_$i";
            if ($paper->$stepField === '-' || is_null($paper->$stepField)) {
                $isComplete = false;
                break;
            }
        }

        if ($isComplete) {
            $team = Team::findOrFail($paper->team_id);
            $pdfMerger = PdfMerger::init();

            for ($i = 1; $i <= $maxStep; $i++) {
                $stepField = "step_$i";
                $pdfPath = public_path('storage/internal/' . $team->status_lomba . '/' . $team->team_name . '/' . $stepField . '.pdf');

                // Cek apakah file PDF ada sebelum menambahkannya
                if (file_exists($pdfPath)) {
                    $pdfMerger->addPDF($pdfPath, 'all');
                }
            }

            // Tentukan lokasi penyimpanan file gabungan
            $mergedPdfPath = public_path('storage/internal/' . $team->status_lomba . '/' . $team->team_name . "/full_paper.pdf");
            $pdfMerger->merge();
            $pdfMerger->save($mergedPdfPath);

            // Simpan path gabungan ke kolom `full_paper`
            $paper->full_paper = 'w: internal/' . $team->status_lomba . '/' . $team->team_name . "/full_paper.pdf";
        }

        if (((($paper->step_1 == "" || $paper->step_2 == "" || $paper->step_3 == "" ||
            $paper->step_4 == "" || $paper->step_5 == "" || $paper->step_6 == "" ||
            $paper->step_7 == "" || $paper->step_8 == "") && $paper->full_paper == null)) && $paper->status == "upload full paper") {
            $paper->status = "not finish";
        }
    }
}
