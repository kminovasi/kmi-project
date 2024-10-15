<?php

namespace App\Observers;

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
        $category_name = Team::join('categories', 'categories.id', '=', 'teams.category_id')
            ->join('papers', 'papers.team_id', '=', 'teams.id')
            ->where('papers.id', $paper->id)
            ->pluck('categories.category_name')
            ->toArray()[0];
        if (
            $paper->step_1 != null && $paper->step_2 != null && $paper->step_3 != null &&
            $paper->step_4 != null && $paper->step_5 != null && $paper->step_6 != null &&
            $paper->step_7 != null && ($paper->full_paper != null ? $paper->full_paper[0] == 'w' : true) &&
            (($paper->step_8 == '-' && (strpos($category_name, "GKM") === false)) || ($paper->step_8 != null && (strpos($category_name, "GKM") !== false)))
        ) {
            if ($paper->step_8 == '-' && (strpos($category_name, "GKM") === false))
                $stage = ['step_1', 'step_2', 'step_3', 'step_4', 'step_5', 'step_6', 'step_7'];
            else
                $stage = ['step_1', 'step_2', 'step_3', 'step_4', 'step_5', 'step_6', 'step_7', 'step_8'];
            $item = $paper->only($stage);


            $mpdf = new Mpdf();

            $team = Team::findOrFail($paper->team_id);

            $pdfMerger = PDFMerger::init();

            foreach ($item as $name_column => $column) {
                if ($column == null) {
                    continue;
                }

                $pdfMerger->addPDF(public_path('storage/internal/' . $team->status_lomba . '/' . $team->team_name . '/' . $name_column . '.pdf'), 'all');
            }

            $filepath = public_path('storage/internal/' . $team->status_lomba . '/' . $team->team_name . "/full_paper.pdf");
            $pdfMerger->merge();
            $pdfMerger->save($filepath);
            $paper->full_paper = 'w: internal/' . $team->status_lomba . '/' . $team->team_name . "/full_paper.pdf";
        }

        if ((($paper->step_1 != null && $paper->step_2 != null && $paper->step_3 != null &&
        $paper->step_4 != null && $paper->step_5 != null && $paper->step_6 != null &&
        $paper->step_7 != null && $paper->step_8 != null) || $paper->full_paper != null) && $paper->status === "not finish") {
            $paper->status = "upload full paper";
        } elseif (((($paper->step_1 == "" || $paper->step_2 == "" || $paper->step_3 == "" ||
        $paper->step_4 == "" || $paper->step_5 == "" || $paper->step_6 == "" ||
        $paper->step_7 == "" || $paper->step_8 == "") && $paper->full_paper == null)) && $paper->status == "upload full paper") {
            $paper->status = "not finish";
        }
    }
}
