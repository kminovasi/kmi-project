<?php

namespace App\Mail;

use App\Models\Paper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailNotificationBenefitGM extends Mailable
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
        // ambil attachment dari EmailApprovalBenefit
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

        if ($this->status == 'accepted benefit by facilitator') {
            $email = $this->view('emails.email_benefit_notification')
                ->subject('Notification: Request Approval Benefit GM');

            foreach ($attachments as $attachment) {
                if (is_string($attachment)) {
                    $email->attach($attachment, [
                        'as' => 'Berita Acara Benefit.pdf',
                        'mime' => 'application/pdf',
                    ]);
                } else {
                    Log::error('Attachment is not a string: ' . json_encode($attachment));
                }
            }

            return $email;
        } else {
            throw new \Exception('Invalid status for sending email');
        }
    }
}