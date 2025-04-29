<?php

namespace App\Http\Controllers;

use App\Mail\AssessmentReportMail;
use App\Mail\KirimEmail;
use App\Models\Assessment;
use App\Models\AssessmentDetail;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class AssesmentReportController extends Controller
{

    public function index()
    {
        $users = User::where('role_id', 4)->whereHas('assessments')->get();

        return view('pages.assesment_report.index', compact('users'));
    }

    public function show($id){
        $assesment = Assessment::with(['student', 'category', 'coach', 'details'])
        ->where('student_id', $id)
        ->get();
        $student = User::where('id', $id)->first();

        return view('pages.assesment_report.show', compact('assesment', 'student'));
    }

    public function showPdf($id){
        $nilai = AssessmentDetail::with(['assessment', 'aspect'])->where('assessment_id', $id)->get();
        $assessment = Assessment::with(['student', 'category', 'coach', 'schedule.location'])->where('id', $id)->first();
        $student = Student::where('user_id', $assessment->student_id)->first();
        $user = User::where('id', $assessment->student_id)->first();
        return view('pages.assesment_report.raport', compact('assessment', 'student', 'user', 'nilai'));
    }

    public function sendReportToEmail($assessmentId)
    {
        $assessment = Assessment::with(['student', 'category', 'coach', 'schedule.location'])->findOrFail($assessmentId);

        $nilai = AssessmentDetail::with(['assessment', 'aspect'])
                    ->where('assessment_id', $assessmentId)
                    ->get();

        $student = Student::where('user_id', $assessment->student_id)->firstOrFail();

        $sanitizedName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $student->name);
        $filename = 'laporan_' . $sanitizedName . '_' . time() . '.pdf';

        Storage::makeDirectory('public/reports');

        $pdf = Pdf::loadView('pages.assesment_report.raport', compact('assessment', 'student', 'nilai'))->setPaper('a4', 'portrait');

        $path = 'public/reports/' . $filename;
        Storage::put($path, $pdf->output());

        $fullPath = storage_path('app/' . $path);
        if (!file_exists($fullPath)) {
            return back()->with('error', 'Gagal membuat PDF laporan.');
        }

        Mail::to($assessment->student->email)
            ->send(new AssessmentReportMail($student->name, $filename));

        return back()->with('success', 'Laporan berhasil dikirim ke email!');
    }

    public function kirim()
    {
        $data = [
            'nama' => 'Cinta Ramayanti',
            'pesan' => 'Selamat, kamu berhasil membuat email otomatis di Laravel!'
        ];

        Mail::to('ramayanticinta@gmail.com')->send(new KirimEmail($data));

        return 'Email berhasil dikirim!';
    }

}
