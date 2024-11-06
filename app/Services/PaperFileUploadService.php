<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaperFileUploadService
{
    public function uploadPaperFile(UploadedFile $file, string $type, string $statusLomba, string $teamName, ?string $oldFilePath = null): string
    {
        // Delete old file if it exists
        if ($oldFilePath) {
            $this->deleteFile($oldFilePath);
        }

        $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . "." . $file->getClientOriginalExtension();

        $path = match ($type) {
            'full_paper' => "internal/{$statusLomba}/{$teamName}/full_paper.{$file->getClientOriginalExtension()}",
            'innovation_photo' => "internal/{$statusLomba}/{$teamName}/innovation_photo/{$fileName}",
            'proof_idea' => "internal/{$statusLomba}/{$teamName}/proof_idea/{$fileName}",
            default => "internal/{$statusLomba}/{$teamName}/others/{$fileName}",
        };

        return Storage::disk('public')->putFileAs(dirname($path), $file, basename($path));
    }

    public function deleteFile(?string $path): bool
    {
        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        return false;
    }
}
