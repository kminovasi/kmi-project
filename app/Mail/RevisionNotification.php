<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RevisionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $team;

    /**
     * Create a new message instance.
     */
    public function __construct($team)
    {
        $this->team = $team;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Revisi Makalah Berhasil')
            ->view('emails.revision_notification')
            ->with(['team' => $this->team]);
    }
}
