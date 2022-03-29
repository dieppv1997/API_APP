<?php

namespace App\Mail;

class RegisterEmail extends BaseEmail
{
    /**
     * @return RegisterEmail
     */
    public function build()
    {
        return $this->subject($this->mailData['subject'])
            ->view('emails.register')
            ->with([
                'mailData' => $this->mailData,
            ]);
    }
}
