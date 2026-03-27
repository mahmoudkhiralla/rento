<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $titleText;
    public string $messageText;

    public function __construct(string $titleText, string $messageText)
    {
        $this->titleText = $titleText;
        $this->messageText = $messageText;
    }

    public function build()
    {
        return $this->subject($this->titleText)
            ->view('emails.admin-notification')
            ->with([
                'title' => $this->titleText,
                'message' => $this->messageText,
            ]);
    }
}