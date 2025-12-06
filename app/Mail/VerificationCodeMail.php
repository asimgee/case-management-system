<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $verificationCode;

    public function __construct($verificationCode)
    {
        $this->verificationCode = $verificationCode;
    }

    public function envelope()
    {
        return new Envelope(
            subject: 'Your Two-Factor Verification Code - AI Legal Assistant',
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.verification-code',
            with: [
                'verificationCode' => $this->verificationCode,
            ],
        );
    }

    public function attachments()
    {
        return [];
    }
}