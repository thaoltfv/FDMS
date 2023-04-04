<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JustTesting extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->onQueue('notifications');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to('longlp@sosene.asia')
                   ->subject('[HSTD - 218] Chuyển sang trạng thái thẩm định')
                   ->markdown('emails.notifications.update')
                   ->with([
                     'name' => 'Lê Phi Long',
                     'message' => 'HSTD_218 đã được Lê Phi Long chuyển sang trạng thái Thẩm định',
                     'link' => '#'
                   ]);
    }
}
