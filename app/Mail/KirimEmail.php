<?php

namespace App\Mail;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class KirimEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $assessment;
    public $nilai;
    public $student;
    public $user;

    public function __construct($assessment, $nilai, $student, $user)
    {
        $this->assessment = $assessment;
        $this->nilai = $nilai;
        $this->student = $student;
        $this->user = $user;
    }

    public function build()
    {
        $pdf = Pdf::loadView('emails.pdf-view', [
            'assessment' => $this->assessment,
            'nilai' => $this->nilai,
            'student' => $this->student,
            'user' => $this->user,
        ]);

        return $this->subject('Email dengan PDF Dinamis')
                    ->view('emails.kirim-email', [
                        'assessment' => $this->assessment,
                        'nilai' => $this->nilai,
                        'student' => $this->student,
                        'user' => $this->user,
                    ])
                    ->attachData($pdf->output(), 'document.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
