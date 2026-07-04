<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AccountConfirmationEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('مرحباً بك في إجلال - تم تأكيد حسابك بنجاح')
                    ->view('mail.account-confirmation')
                    ->with([
                        'userName' => $this->user->name,
                        'userEmail' => $this->user->email,
                    ]);
    }
}
