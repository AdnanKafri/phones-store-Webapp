<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CustomerRequestStatusChanged extends Notification
{
    use Queueable;

    protected $request;
    protected $status;

    public function __construct($request, $status)
    {
        $this->request = $request;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Request Status Update',
            'message' => "Your request for '{$this->request->device_name}' is now {$this->status}.",
            'request_id' => $this->request->id,
            'device_name' => $this->request->device_name,
            'status' => $this->status,
            'type' => 'customer_request_status',
        ];
    }
}
