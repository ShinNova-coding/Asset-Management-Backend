<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssetApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(public $assignment)
    {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Your asset request has been approved',
            'asset_id' => $this->assignment->asset_id,
            'assignment_id' => $this->assignment->id,
        ];
    }
}