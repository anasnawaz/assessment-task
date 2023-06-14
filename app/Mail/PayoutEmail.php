<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PayoutEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $email,$amount;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email,$amount)
    {
        $this->amount=$amount;
        $this->email=$email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $amount=$this->amount;
        $email=$this->email;
        return $this->view('payout',compact('email','amount'));
    }



}
