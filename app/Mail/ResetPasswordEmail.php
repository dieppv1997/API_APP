<?php

namespace App\Mail;

class ResetPasswordEmail extends BaseEmail
{
    /**
     * @return ResetPasswordEmail
     */
    public function build()
    {
        return $this->subject($this->mailData['subject'])
            ->view('emails.reset-password')
            ->with([
                'mailData' => $this->mailData,
            ]);
    }
}
