<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class ResetPassword extends Notification implements ShouldQueue
{
    use Queueable;

    public $timeout = 15; // Timeout seconds

    public $object;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($object)
    {
        $this->object = $object;
        $this->onQueue('notifications');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [
            'mail',
        ];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        Log::info($notifiable);
        return (new MailMessage)
            ->subject($this->object->subject ? $this->object->subject : 'CẤP LẠI MẬT KHẨU')
            ->markdown('emails.notifications.reset', [
                'name' => $notifiable->name ? $notifiable->name : '',
                'email' => $this->object->email ? $this->object->email : '',
                'new_password' => $this->object->new_password ? $this->object->new_password : '',
                'message' => $this->object->message ? $this->object->message : '',
                'link' => ''
            ]);
    }
}
