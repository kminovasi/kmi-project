<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventAssignmentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $teamName;
    public $eventName;
    public $userName;

    public function __construct($teamName, $eventName, $userName)
    {
        $this->teamName = $teamName;
        $this->eventName = $eventName;
        $this->userName = $userName;
    }

    public function envelope()
    {
        return new Envelope(
            subject: 'Notifikasi Penugasan Event',
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.event-assignment',
        );
    }

    public function attachments()
    {
        return [];
    }
}
