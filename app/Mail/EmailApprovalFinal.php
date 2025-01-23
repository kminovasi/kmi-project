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
        // $attachment1 = Storage::path('public/' . mb_substr(Paper::where('id', '=', $this->paper->id)->pluck('full_paper')[0], 3));

        // $attachment1 = public_path("storage/" . mb_substr(Paper::where('id', '=', $this->paper->id)->pluck('full_paper')[0], 3));
        //$attachment2 = Storage::path('public/' . $this->paper->file_review);

        // if ($this->status == 'accept' || $this->status == 'reject') {
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

        $attachment2 = $emailApprovalBenefit->getAttachment();
        // }
        if ($this->status == 'accepted by innovation admin') {
            $attachmentPath = mb_substr($this->paper->full_paper, 3);
            $attachment1 = Storage::disk('public')->path($attachmentPath);
            return $this->view('emails.email_final_approval')
                ->subject('Notification: Final Paper Accepted')
                ->attach($attachment1, [
                    'as' => 'Makalah Full Paper.pdf',
                    'mime' => 'application/pdf',
                ])
                ->attach($attachment2, [
                    'as' => 'Berita Acara Benefit.pdf',
                    'mime' => 'application/pdf',
                ]);
        } elseif ($this->status == 'revision paper by innovation admin') {
            return $this->view('emails.email_final_approval')
                ->subject('Notification: Final Paper Revisi');
        } elseif ($this->status == 'revision benefit by innovation admin') {
            return $this->view('emails.email_final_approval')
                ->subject('Notification: Final Paper Revisi Benefit');
        } elseif ($this->status == 'revision paper and benefit by innovation admin') {
            return $this->view('emails.email_final_approval')
                ->subject('Notification: Final Paper Revisi Benefit dan Makalah');
            // ->attach($attachment2, [
            //     'as' => 'Berita Acara Benefit.pdf',
            //     'mime' => 'application/pdf',
            // ]);
        } elseif ($this->status == 'rejected by innovation admin') {
            $attachmentPath = mb_substr($this->paper->full_paper, 3);
            $attachment1 = Storage::disk('public')->path($attachmentPath);
            return $this->view('emails.email_final_approval')
                ->subject('Notification: Final Paper Not Complete')
                ->attach($attachment1, [
                    'as' => 'Makalah Full Paper.pdf',
                    'mime' => 'application/pdf',
                ]);
            // ->attach($attachment2, [
            //     'as' => 'Berita Acara Benefit.pdf',
            //     'mime' => 'application/pdf',
            // ]);
        }
    }
}
