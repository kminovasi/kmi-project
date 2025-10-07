<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReplicationSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public array $payload;

    /**
     * @param array $payload
     *  keys: innovation_title, team_name, leader_name, leader_email,
     *        pic_name, pic_phone, unit_name, superior_name, plant_name,
     *        area_location, planned_date, submitted_by_name, submitted_by_email
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function build()
    {
        return $this->subject('[Portal Inovasi] Pengajuan Replikasi Dikirim')
            ->markdown('emails.replications.submitted', [
                'p' => $this->payload
            ]);
    }
}
