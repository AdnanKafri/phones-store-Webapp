<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class GenericNotification extends Notification
{
    use Queueable;

    public $title;
    public $message;
    public $type;
    public $data;

    public function __construct($title, $message, $type = 'system', $data = [])
    {
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
        $this->data = $data ?? [];
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return array_merge([
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
        ], is_array($this->data) ? $this->data : []);
    }
}
