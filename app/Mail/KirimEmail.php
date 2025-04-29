<?php

namespace App\Mail;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class KirimEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data; // Kirim data ke view

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        // Menghasilkan PDF dari view
        $pdf = Pdf::loadView('emails.pdf-view', $this->data);

        // Mengirim email dengan PDF sebagai attachment
        return $this->subject('Email dengan PDF Dinamis')
                    ->view('emails.kirim-email')
                    ->attachData($pdf->output(), 'document.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
