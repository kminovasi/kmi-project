<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Paper;
use Illuminate\Support\Facades\Storage;

class EmailApprovalPaperFasil extends Mailable
{
    use Queueable, SerializesModels;

    public $paper;
    public $status;
    public $innovation_title;
    public $team_name;
    public $leaderName;
    public $inovasi_lokasi;

    public function __construct(Paper $paper, $status, $innovation_title, $team_name, $leaderData, $inovasi_lokasi)
    {
        $this->paper = $paper;
        $this->status = $status;
        $this->innovation_title = $innovation_title;
        $this->team_name = $team_name;
        $this->leaderName = $leaderData->name;
        $this->inovasi_lokasi = $paper->inovasi_lokasi;
    }

    public function build()
    {

        if ($this->status == 'accepted paper by facilitator') {
            $fullPaper = Paper::where('id', '=', $this->paper->id)->value('full_paper');
            if (!$fullPaper) {
                throw new \Exception("File path for 'full_paper' is missing or invalid in database.");
            }

            // Hapus awalan jalur jika perlu
            $attachmentPath = mb_substr($fullPaper, 3);

            // Pastikan file ada di disk
            if (!Storage::disk('public')->exists($attachmentPath)) {
                throw new \Exception("Attachment file not found in storage path: {$attachmentPath}");
            }

            $attachment = Storage::disk('public')->path($attachmentPath);
            $fileUrl = Storage::disk('public')->url($attachmentPath);
            return $this->view('emails.email_paper_approval')
                ->subject('Notification: Paper Accepted')
                ->with(['fileUrl' => $fileUrl]);
        } elseif ($this->status == 'rejected paper by facilitator') {
            return $this->view('emails.email_paper_approval')
                ->subject('Notification: Paper Rejected');
        } elseif ($this->status == 'revision paper by facilitator') {
            return $this->view('emails.email_paper_approval')
                ->subject('Notification: Paper Rejected');
        }
    }
}
