<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AssessmentReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $filename;

    public function __construct($user, $filename)
    {
        $this->user = $user;
        $this->filename = $filename;
    }

    public function build()
    {
        return $this->subject('Laporan Penilaian Siswa')
            ->view('pages.assesment_report.mail_report')
            ->attach(storage_path('app/public/reports/' . $this->filename));
    }
}
