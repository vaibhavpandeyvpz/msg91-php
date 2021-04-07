<?php

namespace Msg91\Laravel\Notification;

use Msg91\Client;

class Message
{
    /**
     * @var string
     */
    public $message;

    /**
     * @var array
     */
    public $options = [];

    /**
     * @var int
     */
    public $route = Client::ROUTE_TRANSACTIONAL;

    /**
     * @var string
     */
    public $sender;

    public function message(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function options(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    public function promotional(): self
    {
        return $this->route(Client::ROUTE_PROMOTIONAL);
    }

    public function route(int $route): self
    {
        $this->route = $route;
        return $this;
    }

    public function sender(?string $sender): self
    {
        $this->sender = $sender;
        return $this;
    }

    public function transactional(): self
    {
        return $this->route(Client::ROUTE_TRANSACTIONAL);
    }
}
