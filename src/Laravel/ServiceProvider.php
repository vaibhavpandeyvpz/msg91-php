<?php

namespace Msg91\Laravel;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider as Base;
use Msg91\Client;
use Msg91\Laravel\Notification\Channel;

/**
 * Class ServiceProvider
 * @package Laravel\Msg91
 */
class ServiceProvider extends Base
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/msg91.php', 'msg91');
        $this->app->bind(Client::class, function () {
            return new Client(config('msg91.key'), config('msg91.sender'));
        });
        $this->app->alias(Client::class, 'msg91');
        Notification::extend('msg91', function () {
            return new Channel(app(Client::class));
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('msg91_otp', function ($attribute, $value, $parameters, $validator) {
            /** @var Client $client */
            $client = app(Client::class);
            /** @var \Illuminate\Validation\Validator $validator */
            $values = $validator->getData();
            $number = Arr::get($values, empty($parameters[0]) ? 'phone' : $parameters[0]);
            return $client->verify($number, $value);
        });
    }
}
