<?php

// app/Mail/PaperStatusUpdated.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaperStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $paper;
    public $user;

    public function __construct($paper, $user)
    {
        $this->paper = $paper;
        $this->user = $user;
    }

    public function build()
    {
        $subject = str_contains($this->paper->status_event, 'accept')
            ? 'Paper anda disetujui'
            : 'Paper anda tidak disetujui';

        return $this->view('emails.paper-status-updated')
            ->subject($subject);
    }
}
