<p align="center"><img src="src/Resources/images/sms.jpg?raw=true" width="150px"></p>



# Laravel Iran Sms



[![Software License][ico-license]](LICENSE.md)
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads on Packagist][ico-download]][link-packagist]
[![StyleCI](https://github.styleci.io/repos/684210225/shield?branch=master)](https://github.styleci.io/repos/684210225)
[![Maintainability](https://api.codeclimate.com/v1/badges/9e2415e0cfcfe2120a9e/maintainability)](https://codeclimate.com/github/hooman-mirghasemi/Laravel-iran-sms/maintainability)
[![Quality Score][ico-code-quality]][link-code-quality]

This is a Laravel Package for Sms Senders Integration. This package supports `Laravel 9+` But it may work with laravel 8 or 7 (not tested).

> Benefits of this package:
> - Multiple drivers
> - Support create custom drivers
> - Have Fake build in driver, it can send success or failure sms/voice call message. (Can use in development and testing modes)
> - Store sms reports in database
> - Have a tools in only development mode in http://localhost/sms/get-sms-list (Your frontend developer can use it for access latest sms send with fake driver for example needs to otp codes, when he/she is 
> developing some parts like forgot password)


- [English documents][link-en]

# List of contents

- [Laravel Iran Sms](#laravel-iran-sms)
- [List of contents](#list-of-contents)
- [List of available drivers](#list-of-available-drivers)
    - [Install](#install)
    - [.env file](#env-file)
    - [How to use](#how-to-use)
        - [Working with Facades](#working-with-Facades)
        - [Working with Channels (use laravel notification classes)](#working-with-channels-(use-laravel-notification-classes))
        - [Create custom drivers:](#create-custom-drivers)
        - [Events](#events)
    - [Contributing](#contributing)
    - [Credits](#credits)
    - [License](#license)

# List of available drivers

- [fake sms sender](#fake-sms) :heavy_check_mark: (Both: Voice call/Sms)
- [Avanak](https://www.avanak.ir/) :heavy_check_mark: (Voice call driver)
- [Kavenegar](https://kavenegar.com/) :heavy_check_mark: (Sms)
- [Magfa](https://magfa.com/) :heavy_check_mark: (Sms)
- [Sms Online](https://smsonline.ir/) :heavy_check_mark: (Sms)

Note: for using each of them check config file and use the needed env in your env file
like username/password or api key depend on witch driver you use.

Note: to use magfa/sms online/avanak you should install php ext-soap.

## .env file for each driver:

### fake sms sender
// Use in your local .env file

SMS_DRIVER=fake

// It's optional if you want you can set a number

FAKE_SENDER_NUMBER=101010


### Kavenegar
// Use in your production .env file if you want to use Kavenegar as default sms driver

SMS_DRIVER=kavenegar

// Your kavenegar account api key

KAVENEGAR_API_KEY=fsdf452fd

### Magfa
// Use in your production .env file if you want to use Magfa as default sms driver

SMS_DRIVER=magfa

SMS_MAGFA_USERNAME=your magfa user name

SMS_MAGFA_PASSWORD=your magfa password

SMS_MAGFA_DOMAIN=your magfa domain

SMS_MAGFA_SENDER_NUMBER=your number in magfa you want to send sms with it

### Sms Online
// Use in your production .env file if you want to use Sms Online as default sms driver

SMS_DRIVER=smsonline

SMS_ONLINE_USERNAME=your smsonline user name

SMS_ONLINE_PASSWORD=your smsonline password

SMS_ONLINE_SENDER_NUMBER=your number in smsonline you want to send sms with it

### Avanak (voice caller)
// Use in your production .env file if you want to use Sms Online as default sms driver

VOICE_CALL_DRIVER=avanak

VOICE_AVANAK_USERNAME=your avanak user name

VOICE_AVANAK_PASSWORD=your avanak password
> you can create your own custom drivers if it does not exist in the list, read the `Create custom drivers` section.

## Install

Via Composer

``` bash
$ composer require hooman-mirghasemi/laravel-iran-sms
```

## Publish Vendor Files

It is optional and only if you need you can publish vendor files by these commands:

- **publish configuration files:**
``` bash
php artisan vendor:publish --tag=iran-sms-config
```

- **publish views for customization:**
``` bash
php artisan vendor:publish --tag=iran-sms-views
```

- **publish migration:**
``` bash
php artisan vendor:publish --tag=iran-sms-migrations
```

## .env file

You can use `SMS_DRIVER` env for set default sms driver. (in local don't change it, by default it set fake driver)

And also can use `VOICE_CALL_DRIVER` env. it is like SMS_DRIVER, but for voice call.

## How to use

There are two option of using this package:

1- use Facades

2- use Channels

#### Working with Facades

You can use `Sms` or `VoiceCall` facades in anywhere of your code like this:

```php
// At the top of the file.
use HoomanMirghasemi\Sms\Facades\Sms;
...

Sms::to('+989121234567')->message('your sms text')->send();

//Also you can set driver in run time:
Sms::driver('magfa')->to('+989121234567')->message('your sms text')->send();
```
available methods:

- `to`: set the mobile number should get sms.
- `message`: the text message can be a simple string or object of a class implement HoomanMirghasemi\Sms\Contracts\Message interface
- `send`: send the message.

#### Working with Channels (use laravel notification classes)
Make a laravel notification class, set via `SmsChannel` like this code:

```php
// At the top of the file.
use HoomanMirghasemi\Sms\Channels\SmsChannel;
use HoomanMirghasemi\Sms\Contracts\Message\Message;
...

public function __construct(public SomeModel $someModel)
{
    $this->via = SmsChannel::class;
}

public function toSms(User $notifiable)
{
    $smsMessage = 'make your sms text ';
    // only if using kavehnegar set the pattern name
    $pattern = 'kavenagarMyPatternName';

    $message = new Message($smsMessage);
    $message->useTemplateIfSupports(
        $pattern,
        [
            'token1' => 'test',
            'token10' => $notifiable->name,
            'token20' => $this->family
        ]   
    );

    return Sms::to($notifiable->mobile)
        ->message($message);
}
```
#### Change condition of showing sms list page

By default when your laravel application is in production mode this page will response 
404. But if you want have a diffrent condition publish config file and change this part
like this code or some thing you want:

```php
// default config is:
'dont_show_sms_list_page_condition' => function() {
    return config('app.env') == 'production';
}

// you can check domain:
'dont_show_sms_list_page_condition' => function() {
    return config('app.env') == 'production' || config('app.url') == 'https://yourproductiondomain.com';
}
```
now if you forgot to set app.env to production or temporary change it, it will be safe
and return 404.

#### Create custom drivers:
Option A:

We welcome your participation, Create your driver and send a pull request.

Option A:
This package is using strategy design pattern and laravel `Manager` class.
so you can easily make your driver like this:

```php
<?php

namespace App;

use HoomanMirghasemi\Sms\Abstracts\Driver;

class MyCustomDriver extends Driver
{
    public static bool $successSend = true;

    public function __construct(protected array $settings)
    {
        $this->from = data_get($this->settings, 'from');
    }

    public function send(): bool
    {
        // write api of sending sms by your custom provider

        return parent::send();
    }
    
    public function getBalance(): string
    {
         // write api of getting account balance from your custom provider
         return $balance;
    }
}

```
And register it into manager class in any of your service providers class like this:
```php
$smsManager = app('sms');

$smsManager->extend('myCustomDriver', function ($app) {
    $setting = ['from' => '22336'];
    return new MyCustomDriver($setting);
});


// or using laravel ioc

$this->app->bind(MyCustomDriver::class, function () {
    $setting = ['from' => '22336'];
    return new MyCustomDriver($config);
});

$smsManager->extend('myCustomDriver', function ($app) {
    return $this->container->make(MyCustomDriver::class);
});

// or you can publish config file and add setting of your driver into it. then:

$this->app->bind(MyCustomDriver::class, function () {
    $config = config('sms.drivers.mycustomdriver') ?? [];
    return new MyCustomDriver($config);
});

$smsManager->extend('myCustomDriver', function ($app) {
    return $this->container->make(MyCustomDriver::class);
});
```

#### Events

You can listen for this event

- **SmsSentEvent**: Occurs when sms send. (the package use it to collect report into db)


## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.


## Credits

- [Hooman Mirghasemi][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/hooman-mirghasemi/laravel-iran-sms.svg?style=flat-square
[ico-download]: https://img.shields.io/packagist/dt/hooman-mirghasemi/laravel-iran-sms.svg?color=%23F18&style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/quality/g/hooman-mirghasemi/laravel-iran-sms.svg?label=Code%20Quality&style=flat-square

[link-fa]: README-FA.md
[link-en]: README.md
[link-packagist]: https://packagist.org/packages/hooman-mirghasemi/laravel-iran-sms
[link-code-quality]: https://scrutinizer-ci.com/g/hooman-mirghasemi/laravel-iran-sms
[link-author]: https://github.com/hooman-mirghasemi
[link-contributors]: ../../contributors
