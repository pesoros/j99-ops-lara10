<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fcm;

class ToolsApiController extends Controller
{
    public function fcmTest()
    {
        $notifTitle = 'Notification Title';
        $notifBody = 'Notification Body';
        $notifUrl = '/letter/workorder/show/detail/{uuid}';

        $rawPushNotif = [
            'topic' => env('MECHANIC_TOPIC'),
            'notification' => [
                'title' => $notifTitle,
                'body' => $notifBody,  
            ],
            'data' => [
                'title' => $notifTitle,
                'body' => $notifBody,  
                'url' => $notifUrl,
            ]
        ];

        $sendNotif = Fcm::sendPushNotification($rawPushNotif);

        return $sendNotif;
    }
}
