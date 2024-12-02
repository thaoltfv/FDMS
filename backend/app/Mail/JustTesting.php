<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JustTesting extends Mailable
{
    use Queueable, SerializesModels;

    protected $emailReceive;
    public $subject;
    protected $markdown;
    protected $name;
    protected $message;
    protected $link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailReceive, $subject, $markdown, $name, $message, $link)
    {
        $this->emailReceive = $emailReceive;
        $this->subject = $subject;
        $this->markdown = $markdown;
        $this->name = $name;
        $this->message = $message;
        $this->link = $link;

        $this->onQueue('notifications');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->emailReceive)
                   ->subject($this->subject)
                   ->markdown($this->markdown)
                   ->with([
                     'name' => $this->name,
                     'message' => $this->message,
                     'link' => $this->link
                   ]);
    }
}
