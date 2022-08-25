# vaibhavpandeyvpz/msg91
PHP client for sending SMS/OTP using MSG91, includes optional support for Laravel.

### Installation

```bash
composer require vaibhavpandeyvpz/msg91
```

#### Laravel < 5.5
Once the package is installed, open your `app/config/app.php` configuration file and locate the `providers` key. Add the following line to the end:

```php
Msg91\Laravel\ServiceProvider::class
```

Next, locate the `aliases` key and add the following line:

```php
'Msg91' => Msg91\Laravel\Facade::class,
```

### Configuration
You need to add `MSG91_KEY` in your project's `.env` file. You can also publish the default configuration file as `config/msg91.php` using below command.

```bash
$ php artisan vendor:publish
```

### Usage

## Basic
- Send an SMS to one or more numbers.
```php
<?php

// send an SMS to one number
$result = Msg91::sms(
    '919999999999',
    'Hello there!',
    'MSGIND',
    4 /* 1 = Promotional; 4 = Transactional */,
    [
        'DLT_TE_ID' => '<dlt-registered-template-id>',
    ]
);

// send same/different SMS to multiple numbers
$result = Msg91::sms(
    null,
    [
        ['to' => ['919999999999', '918888888888'], 'message' => 'Hello fellas!'],
        ['to' => ['917777777777'], 'message' => 'Hello vpz!'],
    ],
    'MSGIND',
    4 /* 1 = Promotional; 4 = Transactional */,
    [
        'DLT_TE_ID' => '<dlt-registered-template-id>',
    ]
);
```

- Send OTP to a number.
```php
<?php

$result = Msg91::otp('919999999999', 'MSGIND', [
    'template_id' => '<msg91-approved-otp-template-id>',
]);
```

- Retry OTP (as voice) to a number.
```php
<?php

$result = Msg91::retry('919999999999', true); // returns true or false
```

- Verify OTP sent to a number.
```php
<?php

$result = Msg91::verify('919999999999', 1290); // returns true or false
```

## Notification
Include `msg91` in your notification's channels:
```php
<?php

/**
 * @param  mixed  $notifiable
 * @return array
 */
public function via($notifiable)
{
    return ['msg91'];
}
```

Define the `toMsg91` method:
```php
<?php

use Msg91\Laravel\Notification\Message as Msg91Message;

public function toMsg91()
{
    return (new Msg91Message)
        ->message(__('This is just a test message.'))
        ->sender('MSGIND')
        ->transactional()
        ->options([
            'DLT_TE_ID' => '<dlt-registered-templated-id>',
        ]);
}
```

Implement `routeNotificationForMsg91` method in your notifiable class:
```php
<?php

public function routeNotificationForMsg91($notification)
{
    return $this->phone;
}
```

Finally send the notification:
```php
<?php

$notifiable = /* some class */
$notifiable->notify(new App\Notifications\Msg91TestNotification());
```

For sending the notification to an arbitrary number, use below syntax:
```php
<?php
use Illuminate\Support\Facades\Notification;

Notification::route('msg91', '919999999999')
    ->notify(new App\Notifications\Msg91TestNotification());
```

## Validator
You can validate sent OTPs using provided validation rule named `msg91_otp` as shown below:
```php
<?php

use Illuminate\Support\Facades\Validator;

$data = ['phone' => '919999999999', 'otp' => '1234'];

$validator = Validator::make($data, [
    'phone' => ['required', 'digits_between:10,12'],
    'otp' => ['required', 'digits:4', 'msg91_otp'], // default key for source number is 'phone', you can customize this using 'msg91_otp:key_name'
]);

if ($validator->fails()) {
    // report errors
}
```

### License

See [LICENSE](LICENSE) file.
