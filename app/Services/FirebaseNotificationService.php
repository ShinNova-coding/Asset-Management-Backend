<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseNotificationService
{
  public function send($token, $title, $body)
{
    try {
        $messaging = (new Factory)
            ->withServiceAccount(storage_path('app/firebase/firebase_credentials.json'))
            ->createMessaging();

        $message = CloudMessage::fromArray([
            'token' => $token,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
        ]);

        return $messaging->send($message);
    } catch (\Exception $e) {
        \Log::error('FCM Notification error: ' . $e->getMessage());
        return null;
    }
}
}
