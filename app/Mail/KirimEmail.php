<?php

namespace App\Mail;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class KirimEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data; // berisi array: assessments, nilai, student, user

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $pdf = Pdf::loadView('emails.pdf-view', $this->data);

        return $this->subject('Email dengan PDF Dinamis')
                    ->view('emails.kirim-email', $this->data)
                    ->attachData($pdf->output(), 'document.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
