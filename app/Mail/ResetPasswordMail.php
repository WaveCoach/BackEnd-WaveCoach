<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $email;

    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    public function build()
    {
        $resetUrl = url("https://wavecoach.cintaramayanti.com/reset-password?email={$this->email}&token={$this->token}");

        return $this->subject('Reset Your Password')
                    ->view('pages.profile.reset_password')
                    ->with([
                        'resetUrl' => $resetUrl,
                    ]);
    }
}
