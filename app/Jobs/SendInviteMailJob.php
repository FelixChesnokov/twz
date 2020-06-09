<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendInviteMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $inviteMail;
    public $email;

    /**
     * SendInviteMailJob constructor.
     *
     * @param Mailable $inviteMail
     * @param string $email
     *
     * @return void
     */
    public function __construct(Mailable $inviteMail, string $email)
    {
        $this->inviteMail = $inviteMail;
        $this->email      = $email;
    }

    /**
     * Send mail
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->email)->send($this->inviteMail);
    }
}
