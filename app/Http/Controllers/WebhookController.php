<?php

namespace App\Http\Controllers;

use App\Http\Actions\Webhook\WebhookHandleAction;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $webhook = new WebhookHandleAction();
        return $webhook->execute($request);
    }
}
