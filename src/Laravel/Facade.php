<?php

namespace Msg91\Laravel;

use Illuminate\Support\Facades\Facade as Base;
use Msg91\Client;

/**
 * @method static bool otp(string $number, string|null $sender = null)
 * @method static bool retry(string $number, bool $voice = true)
 * @method static bool sms(string|string[]|null $numbers, string|string[] $messages, string|null $sender = null, int $route = Client::ROUTE_TRANSACTIONAL, array $options = [])
 * @method static bool verify(string $number, string $otp)
 */
class Facade extends Base
{
    protected static function getFacadeAccessor(): string
    {
        return Client::class;
    }
}
