<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Paper;
use Illuminate\Support\Facades\Storage;

class EmailNotificationBenefit extends Mailable
{
    use Queueable, SerializesModels;

    public $record;
    public $status;
    public $innovation_title;
    public $team_name;
    public $leaderName;
    public $financial;
    public $potential_benefit;
    public $potensi_replikasi;
    public $non_financial;
    public $fasilName;
    public $inovasi_lokasi;

    public function __construct(Paper $record, $status, $innovation_title, $team_name, $leaderData, $financial, $potential_benefit, $potensi_replikasi, $non_financial, $fasilData, $inovasi_lokasi)
    {
        $this->record = $record;
        $this->status = $status;
        $this->innovation_title = $innovation_title;
        $this->team_name = $team_name;
        $this->leaderName = $leaderData->name;
        $this->financial = $financial;
        $this->potential_benefit = $potential_benefit;
        $this->potensi_replikasi = $potensi_replikasi;
        $this->non_financial = $non_financial;
        $this->fasilName = $fasilData->name;
        $this->inovasi_lokasi = $inovasi_lokasi;
    }

    public function build()
    {
        // $attachment = Storage::path('public/' . $this->record->file_review);
        $attachment = storage_path('app/public/' . $this->record->file_review);

        if ($this->status == 'upload benefit') {
            return $this->view('emails.email_benefit_notification')
                ->subject('Notification: Request Approval Benefit Fasil')
                ->attach($attachment, [
                    'as' => 'Berita Acara Benefit.pdf',
                    'mime' => 'application/pdf',
                ]);
        } else {
            throw new \Exception('Invalid status for sending email');
        }
    }
}