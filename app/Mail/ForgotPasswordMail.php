<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $token;

    public $resetUrl;

    public function __construct($user, $token, $resetUrl)
    {
        $this->user = $user;
        $this->token = $token;
        $this->resetUrl = $resetUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Redefinição de Senha',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'login.email.forgot-password-email',
            with: [
                'user' => $this->user,
                'token' => $this->token,
                'resetUrl' => $this->resetUrl,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
