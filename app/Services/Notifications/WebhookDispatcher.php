<?php

namespace App\Services\Notifications;

use Illuminate\Support\Facades\Http;


class WebhookDispatcher
{
    public function send($url, $data)
    {
        Http::post($url, $data);
    }
}
