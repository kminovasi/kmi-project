<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Paper;
use Illuminate\Support\Facades\Storage;

class EmailNotificationPaperFasil extends Mailable
{
use Queueable, SerializesModels;

public $paper;
public $stage;
public $innovation_title;
public $team_name;
public $leaderName;
public $fasilName;
public $inovasi_lokasi;

public function __construct(Paper $paper, $stage, $innovation_title, $team_name, $leaderName, $fasilName, $inovasi_lokasi)
{
    $this->paper = $paper;
    $this->stage = $stage;
    $this->innovation_title = $innovation_title;
    $this->team_name = $team_name;
    $this->leaderName = $leaderName;
    $this->fasilName = $fasilName;
    $this->inovasi_lokasi = $inovasi_lokasi;
}

public function build()
{
    $email = $this->view('emails.email_paper_notification')
        ->subject('Notification: Request Approval Full Paper');

    if ($this->stage == 'full_paper' && !empty($this->paper->full_paper)) {
        $attachmentPath = storage_path('app/public/' . ltrim($this->paper->full_paper, '/'));

        if (file_exists($attachmentPath)) {
            $email->attach($attachmentPath, [
                'as' => 'Makalah Full Paper.pdf',
                'mime' => 'application/pdf',
            ]);
        }
    }

    return $email;
    }
}