<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OfferDeviceNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $offerer;
    public $deviceName;
    public $requestId;

    public function __construct($offerer, $deviceName, $requestId)
    {
        $this->offerer = $offerer;
        $this->deviceName = $deviceName;
        $this->requestId = $requestId;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'device_offer',
            'title' => 'عرض جهاز جديد',
            'message' => "المستخدم {$this->offerer->name} يشير إلى أنه يمتلك الجهاز الذي طلبته ({$this->deviceName}).",
            'offerer_name' => $this->offerer->name,
            'offerer_id' => $this->offerer->id,
            'device' => $this->deviceName,
            'device_request_id' => $this->requestId,
            'contact_info' => [
                'phone' => $this->offerer->phone,
                'email' => $this->offerer->email,
            ]
        ];
    }
}
