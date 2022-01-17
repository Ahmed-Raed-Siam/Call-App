<?php

namespace App\Channels;

use Exception;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class Msegat
{
    protected $baseUrl = 'https://msegat.com';

    public function send($notifiable, Notification $notification)
    {
        //Old Like /config('services.msegat.userPassword')
        $config = config('services.msegat');

        $response = Http::baseUrl($this->baseUrl)
            ->get('/gw/', [
                'userName' => $config['userName'],
                'userPassword' => $config['userPassword'],
                'numbers' => $notifiable->routeNotificationForMsegat($notification),
                'userSender' => $config['userSender'],
//                'By' => $notifiable->routeNotificationForMsegat($notification),
                'msgEncoding' => $config['msgEncoding'],
                'msg' => $notification->toMsegat($notifiable),
            ]);

        $code = $response->body();
        if ($code != 1) {
            throw new Exception($code);
        }
    }
}
