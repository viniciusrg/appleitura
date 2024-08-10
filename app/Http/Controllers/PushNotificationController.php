<?php

namespace App\Http\Controllers;

use App\Http\Actions\PushNotification\PushNotificationAction;
use App\Http\Actions\PushNotification\PushNotificationSendAction;
use App\Http\Requests\StorePushNotificationRequest;

class PushNotificationController extends Controller
{
    public function store(StorePushNotificationRequest $request)
    {
        $notification = new PushNotificationAction();
        return $notification->execute($request);
    }

    public function send (){
        $send = new PushNotificationSendAction();
        return $send->execute();
    }
}
