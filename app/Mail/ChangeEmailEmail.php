<?php

namespace App\Mail;

class ChangeEmailEmail extends BaseEmail
{
    /**
     * @return ChangeEmailEmail
     */
    public function build()
    {
        return $this->subject($this->mailData['subject'])
            ->view('emails.change-email')
            ->with([
                'mailData' => $this->mailData,
            ]);
    }
}
