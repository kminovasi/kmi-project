<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Paper;
use Illuminate\Support\Facades\Storage;
use App\Mail\EmailApprovalBenefit;

class EmailApprovalFinal extends Mailable
{
    use Queueable, SerializesModels;

    public $paper;
    public $status;
    public $innovation_title;
    public $team_name;
    public $gmName;
    public $benefitFinancial;
    public $benefitPotential;
    public $potensiReplikasi;
    public $benefitNonFinancial;
    public $leaderName;
    public $inovasi_lokasi;

    public function __construct(Paper $paper, $status, $innovation_title, $team_name, $gmData, $benefitFinancial, $benefitPotential, $potensiReplikasi, $benefitNonFinancial, $leaderData, $inovasi_lokasi)
    {
        $this->paper = $paper;
        $this->status = $status;
        $this->innovation_title = $innovation_title;
        $this->team_name = $team_name;
        $this->gmName = $gmData->name;
        $this->benefitFinancial = $benefitFinancial;
        $this->benefitPotential = $benefitPotential;
        $this->potensiReplikasi = $potensiReplikasi;
        $this->benefitNonFinancial = $benefitNonFinancial;
        $this->leaderName = $leaderData->name;
        $this->inovasi_lokasi = $inovasi_lokasi;
    }

    public function build()
    {
        $emailApprovalBenefit = new EmailApprovalBenefit(
            $this->paper,
            $this->status,
            $this->innovation_title,
            $this->team_name,
            (object)['name' => $this->gmName],
            $this->benefitFinancial,
            $this->benefitPotential,
            $this->potensiReplikasi,
            $this->benefitNonFinancial,
            (object)['name' => $this->leaderName],
            $this->inovasi_lokasi
        );

        $attachments = $emailApprovalBenefit->getAttachment();

        if ($this->status == 'accepted by innovation admin') {
            $attachmentPath = ltrim($this->paper->full_paper, '/');
            $attachment1 = Storage::disk('public')->path($attachmentPath);
            $email = $this->view('emails.email_final_approval')
                ->subject('Notification: Final Paper Accepted')
                ->attach($attachment1, [
                    'as' => 'Makalah Full Paper.pdf',
                    'mime' => 'application/pdf',
                ]);

            foreach ($attachments as $attachment) {
                if (is_string($attachment)) {
                    $email->attach($attachment, [
                        'as' => basename($attachment),
                        'mime' => 'application/pdf',
                    ]);
                }
            }

            return $email;
        } elseif ($this->status == 'revision paper by innovation admin') {
            return $this->view('emails.email_final_approval')
                ->subject('Notification: Final Paper Revisi');
        } elseif ($this->status == 'revision benefit by innovation admin') {
            return $this->view('emails.email_final_approval')
                ->subject('Notification: Final Paper Revisi Benefit');
        } elseif ($this->status == 'revision paper and benefit by innovation admin') {
            return $this->view('emails.email_final_approval')
                ->subject('Notification: Final Paper Revisi Benefit dan Makalah');
        } elseif ($this->status == 'rejected by innovation admin') {
            $attachmentPath = mb_substr($this->paper->full_paper, 3);
            $attachment1 = Storage::disk('public')->path($attachmentPath);
            $email = $this->view('emails.email_final_approval')
                ->subject('Notification: Final Paper Not Complete')
                ->attach($attachment1, [
                    'as' => 'Makalah Full Paper.pdf',
                    'mime' => 'application/pdf',
                ]);

            foreach ($attachments as $attachment) {
                if (is_string($attachment)) {
                    $email->attach($attachment, [
                        'as' => basename($attachment),
                        'mime' => 'application/pdf',
                    ]);
                }
            }

            return $email;
        }
    }
}