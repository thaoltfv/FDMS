<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BroadcastNotification extends Notification
{
    public $object;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($object)
    {
        $this->object = $object;
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
            'database',
            'mail',
            // 'broadcast',
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
        return (new MailMessage)
            ->subject($this->object->subject ? $this->object->subject : '[HSTD] Đã chuyển trạng thái')
            ->markdown('emails.notifications.update', [
                'name' => $notifiable->name ? $notifiable->name : '',
                'message' => $this->object->message ? $this->object->message : '',
                'link' => ''
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return (array)$this->object;
    }

    public function toDatabase($notifiable)
    {
        return [
            'subject' => $this->object->subject,
            'message' => $this->object->message,
            'user' => $this->object->user,
            'id' => $this->object->id,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     * @param mixed $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        $timestamp = Carbon::now()->addSecond()->toDateTimeString();

        return new BroadcastMessage([
            'notifiable_id' => $notifiable->id,
            'notifiable_type' => get_class($notifiable),
            'data' => $this->object,
            'read_at' => null,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
    }
}
