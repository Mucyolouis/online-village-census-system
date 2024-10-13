<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $familyCode;

    public function __construct(User $user, $familyCode)
    {
        $this->user = $user;
        $this->familyCode = $familyCode;
    }

    public function build()
    {
        return $this->view('emails.welcome')
                    ->subject('Welcome to Our Application');
    }
}