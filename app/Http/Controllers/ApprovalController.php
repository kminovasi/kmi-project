<?php

namespace App\Http\Controllers;

use App\Models\MetodologiPaper;
use App\Models\Paper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function checkStepNotEmptyOrNullOnPaper($paperId)
    {
        // Temukan data paper berdasarkan ID
        $paper = Paper::findOrFail($paperId);

        // Ambil jumlah step maksimum dari metodologi terkait
        $metodologi = MetodologiPaper::findOrFail($paper->metodologi_paper_id);
        $maxStep = $metodologi->step;

        $steps = [];
        for ($i = 1; $i <= $maxStep; $i++) {
            $stepKey = "step_$i";
            if (!empty($paper->$stepKey) && $paper->$stepKey !== '-') {
                $steps[] = $i; // Tambahkan step yang tidak kosong
            }
        }

        $fullPaperPath = $paper->full_paper;
        if (str_starts_with($fullPaperPath, 'f:')) {
            $fullPaperPath = trim(substr($fullPaperPath, 2)); // Hilangkan "f:"
        }

        // Jika semua langkah kosong, cek full_paper dan file di storage
        if (empty($steps) && !empty($paper->full_paper) && Storage::exists($fullPaperPath)) {
            return response()->json([
                'status' => 'full_paper',
                'message' => 'Pengumpulan dilakukan langsung menggunakan full_paper.',
                'full_paper_path' => $fullPaperPath,
            ]);
        }

        // Jika ada langkah yang tersedia
        if (!empty($steps)) {
            return response()->json([
                'status' => 'success',
                'steps' => $steps,
            ]);
        }

        // Jika langkah kosong dan tidak ada full_paper
        return response()->json([
            'status' => 'error',
            'message' => 'Semua langkah kosong dan tidak ada full_paper yang valid.',
        ]);
    }
}
