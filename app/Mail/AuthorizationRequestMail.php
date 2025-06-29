<?php

namespace App\Mail;

use App\Models\AuthorizationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AuthorizationRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $authorizationRequest;

    public function __construct(AuthorizationRequest $authorizationRequest)
    {
        $this->authorizationRequest = $authorizationRequest;
    }

    public function build()
    {
        return $this->subject('New Subsupplier Authorization Request')
            ->view('emails.authorization_request');
    }
}
