<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Mpdf\Mpdf;

class CertificateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $templateNum;
    public $base64Image;
    public $personalMessage;

    public function __construct($student, $templateNum, $base64Image, $personalMessage = '')
    {
        $this->student = $student;
        $this->templateNum = $templateNum;
        $this->base64Image = $base64Image;
        $this->personalMessage = $personalMessage;
    }

    public function content(): Content
    {
        return new Content(
            view: 'teacher.certificates.emails.certificate_notification',
            with: ['personalMessage' => $this->personalMessage],
        );
    }

    public function attachments(): array
    {
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
            'tempDir' => sys_get_temp_dir(),
        ]);

        $mpdf->SetDirectionality('rtl');
        $html = view('teacher.certificates.pdf.download', [
            'student' => $this->student,
            'backgroundImage' => $this->base64Image,
        ])->render();
        $mpdf->WriteHTML($html);

        $pdfOutput = $mpdf->Output('', 'S');

        return [
            Attachment::fromData(fn () => $pdfOutput, "Certificate_{$this->student->name}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
