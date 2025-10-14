<?php

namespace App\Mail;

use App\Models\LearnShare;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LearnShareSubmitted extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public LearnShare $ls;
    public string $showUrl;

    public function __construct(LearnShare $ls)
    {
        $this->ls = $ls->fresh(); // pastikan data terbaru
        $this->showUrl = route('learnshare.show', $this->ls->id);
    }

    public function build()
    {
        return $this->subject('[L&S] Pengajuan Baru: '.$this->ls->title)
                    ->markdown('emails.learnshare.submitted', [
                        'ls'      => $this->ls,
                        'showUrl' => $this->showUrl,
                    ]);
    }
}
