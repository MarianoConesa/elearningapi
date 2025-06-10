<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct($newPassword, $user)
{
    $this->newPassword = $newPassword;
    $this->user = $user;
}

public function build()
{
    return $this->subject('Tu nueva contraseña')->mailer('elearning')
        ->view('emails.password_reset', [
            'newPassword' => $this->newPassword,
            'user' => $this->user,
        ]);
}


    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
