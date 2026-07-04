<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendOtpCode extends Mailable
{
    use SerializesModels;

    public $name;
    public $otp;
    public $expiresInMinutes;

    /**
     * Create a new message instance.
     */
    public function __construct($name, $otp, $expiresInMinutes = 10)
    {
        $this->name = $name;
        $this->otp = $otp;
        $this->expiresInMinutes = $expiresInMinutes;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('رمز التحقق من البريد الإلكتروني')
                    ->view('mail.otp')
                    ->with([
                        'name' => $this->name,
                        'otp' => $this->otp,
                        'expiresInMinutes' => $this->expiresInMinutes,
                    ]);
    }
}
