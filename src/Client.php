<?php

namespace Msg91;

use GuzzleHttp\Client as Guzzle;

class Client
{
    const ENDPOINT_OTP = 'http://control.msg91.com/api/sendotp.php';
    const ENDPOINT_OTP_RETRY = 'http://api.msg91.com/api/retryotp.php';
    const ENDPOINT_OTP_VERIFY = 'http://api.msg91.com/api/verifyRequestOTP.php';
    const ENDPOINT_SMS = 'http://api.msg91.com/api/v2/sendsms';

    const ROUTE_PROMOTIONAL = 1;
    const ROUTE_TRANSACTIONAL = 4;

    /**
     * @var Guzzle
     */
    protected $client;

    /**
     * @var string
     */
    protected $key;

    /**
     * Client constructor.
     * @param string $key
     */
    public function __construct(string $key)
    {
        $this->client = new Guzzle(['http_errors' => false]);
        $this->key = $key;
    }

    /**
     * @param string $number
     * @param string|null $sender
     * @return bool
     */
    public function otp(string $number, string $sender): bool
    {
        $response = $this->client->get(self::ENDPOINT_OTP, [
            'query' => [
                'authkey' => $this->key,
                'mobile' => $number,
                'sender' => $sender,
            ],
        ]);
        if ($response->getStatusCode() === 200) {
            $body = json_decode((string) $response->getBody(), true);
            return isset($body['type']) && ($body['type'] === 'success');
        }

        return false;
    }

    /**
     * @param string $number
     * @param bool $voice
     * @return bool
     */
    public function retry(string $number, bool $voice = true): bool
    {
        $response = $this->client->get(self::ENDPOINT_OTP_RETRY, [
            'query' => [
                'authkey' => $this->key,
                'mobile' => $number,
                'retrytype' => $voice ? 'voice' : 'text',
            ],
        ]);
        if ($response->getStatusCode() === 200) {
            $body = json_decode((string) $response->getBody(), true);
            return isset($body['type']) && ($body['type'] === 'success');
        }

        return false;
    }

    /**
     * @param string|string[]|null $numbers
     * @param string|string[] $messages
     * @param string $sender
     * @param int|null $route
     * @param array $options
     * @return bool
     */
    public function sms($numbers, $messages, string $sender, int $route = self::ROUTE_TRANSACTIONAL, array $options = []): bool
    {
        if (is_string($messages)) {
            $messages = [[
                'message' => $messages,
                'to' => (array) $numbers,
            ]];
        }

        $response = $this->client->post(self::ENDPOINT_SMS, [
            'headers' => ['authkey' => $this->key],
            'json' => [
                'route' => $route,
                'sender' => $sender,
                'sms' => $messages,
            ] + $options,
        ]);
        if ($response->getStatusCode() === 200) {
            $body = json_decode((string) $response->getBody(), true);
            return isset($body['type']) && ($body['type'] === 'success');
        }

        return false;
    }

    /**
     * @param string $number
     * @param string $otp
     * @return bool
     */
    public function verify(string $number, string $otp): bool
    {
        $response = $this->client->get(self::ENDPOINT_OTP_VERIFY, [
            'query' => [
                'authkey' => $this->key,
                'mobile' => $number,
                'otp' => $otp,
            ],
        ]);
        if ($response->getStatusCode() === 200) {
            $body = json_decode((string) $response->getBody(), true);
            return isset($body['type']) && ($body['type'] === 'success');
        }

        return false;
    }
}
