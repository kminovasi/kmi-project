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
            $fullPaper = $this->paper->full_paper;
            if (!$fullPaper) {
                throw new \Exception("File path for 'full_paper' is missing or invalid in database.");
            }

            // Pastikan tidak ada awalan yang tidak perlu seperti '/'
            $fullPaper = ltrim($fullPaper, '/');

            // Pastikan file ada di disk
            if (!Storage::disk('public')->exists($fullPaper)) {
                throw new \Exception("Attachment file not found in storage path: {$fullPaper}");
            }

            $attachment = storage_path('app/public/' . $fullPaper);
            
            return $this->view('emails.email_paper_approval')
                ->subject('Notification: Paper Accepted')
                ->with(['fileUrl' => $attachment]);
        } elseif ($this->status == 'rejected paper by facilitator') {
            return $this->view('emails.email_paper_approval')
                ->subject('Notification: Paper Rejected');
        } elseif ($this->status == 'revision paper by facilitator') {
            return $this->view('emails.email_paper_approval')
                ->subject('Notification: Paper Rejected');
        }
    }
}