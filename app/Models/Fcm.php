<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class Fcm extends Model
{
    public function __construct()
    {
    }

    public function scopeSendPushNotification($query, $raw)
    {
        $firebase = (new Factory)
            ->withServiceAccount(__DIR__.'/../../firebase_credentials.json');
 
        $messaging = $firebase->createMessaging();
 
        $message = CloudMessage::fromArray($raw);
 
        $messaging->send($message);
 
        return response()->json($messaging);
    }
}
