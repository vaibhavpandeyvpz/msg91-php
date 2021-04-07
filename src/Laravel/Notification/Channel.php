<?php

namespace Msg91\Laravel\Notification;

use Illuminate\Notifications\Notification;
use Msg91\Client;

class Channel
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * Msg91Channel constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $number = $notifiable->routeNotificationFor('msg91', $notification);
        if (empty($number)) {
            return;
        }

        /** @var Message $message */
        $message = $notification->toMsg91($notifiable);
        $this->client->sms($number, $message->message, $message->sender, $message->route, $message->options);
    }
}
