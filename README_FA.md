
<p align="center"><img src="src/Resources/images/sms.jpg?raw=true" width="150px"></p>

# لاراول ایران اس‌ام‌اس

[![Software License][ico-license]](LICENSE.md)
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads on Packagist][ico-download]][link-packagist]
[![StyleCI](https://github.styleci.io/repos/684210225/shield?branch=master)](https://github.styleci.io/repos/684210225)
[![Maintainability](https://api.codeclimate.com/v1/badges/9e2415e0cfcfe2120a9e/maintainability)](https://codeclimate.com/github/hooman-mirghasemi/Laravel-iran-sms/maintainability)
[![Quality Score][ico-code-quality]][link-code-quality]

این یک پکیج لاراول برای ارسال پیامک با پراوایدر های پیامک معروف است. این پکیج از `Laravel 8+` پشتیبانی می‌کند و تمامی تست‌ها برای لاراول 8، 9، 10 و 11 پاس شده‌اند!

> مزایای این پکیج:
> - درایورهای متعدد
> - پشتیبانی از ایجاد درایورهای سفارشی
> - دارای درایور fake داخلی که می‌تواند پیامک یا تماس صوتی موفق یا ناموفق ارسال کند. (می‌تواند در حالت‌های توسعه و تست استفاده شود)
> - ذخیره گزارش‌های پیامک در دیتابیس
> - نمایش لیست پیامک های ارسال شده فقط در حالت توسعه http://localhost/sms/get-sms-list (توسعه‌دهنده فرانت‌اند می‌تواند از آن برای دسترسی به آخرین پیامک‌های ارسال شده با درایور fake استفاده کند، به عنوان مثال نیاز به کدهای OTP دارد، وقتی که او بخش‌هایی مثل فراموشی رمز عبور را توسعه می‌دهد)

- [مستندات انگلیسی][link-en]

# فهرست محتویات

- [لاراول ایران اس‌ام‌اس](#laravel-iran-sms)
- [فهرست محتویات](#list-of-contents)
- [لیست درایورهای موجود](#list-of-available-drivers)
    - [نصب](#install)
    - [فایل .env](#env-file)
    - [نحوه استفاده](#how-to-use)
        - [کار با Facades](#working-with-Facades)
        - [کار با Channels (استفاده از کلاس‌های اطلاع‌رسانی لاراول)](#working-with-channels-(use-laravel-notification-classes))
        - [ایجاد درایورهای سفارشی](#create-custom-drivers)
        - [رویدادها](#events)
    - [مشارکت](#contributing)
    - [اعتبارات](#credits)
    - [مجوز](#license)

# لیست درایورهای موجود

- [درایور پیامک ساختگی](#fake-sms) :heavy_check_mark: :بخشی از تست‌های توسعه.
- [Kavenegar](#kavenegar) :heavy_check_mark:
- [Mediana](#mediana) :heavy_check_mark:
- [Sms.ir](#smsir) :heavy_check_mark:
- [Farazsms](#farazsms) :heavy_check_mark:
- [Meli Payamak](#meli-payamak) :heavy_check_mark:

# نصب

برای نصب، می‌توانید از Composer استفاده کنید:

```bash
composer require hooman-mirghasemi/laravel-iran-sms
```

یا می‌توانید این خط را به فایل `composer.json` خود اضافه کنید:

```json
"require": {
    "hooman-mirghasemi/laravel-iran-sms": "1.*"
}
```

و سپس دستور زیر را اجرا کنید:

```bash
composer update
```

# فایل .env

این پکیج از پیکربندی‌ها در فایل `.env` پشتیبانی می‌کند. نمونه تنظیمات فایل `.env` به این شکل است:

```
SMS_DRIVER=kavenegar
SMS_API_KEY=کلید API شما
```

# نحوه استفاده

### کار با Facades

می‌توانید از Facades برای ارسال پیامک استفاده کنید:

```php
use IranSms;

IranSms::send('متن پیام', 'شماره گیرنده');
```

### کار با Channels (استفاده از کلاس‌های اطلاع‌رسانی لاراول)

می‌توانید از کلاس‌های notification لاراول برای ارسال پیامک استفاده کنید. برای این کار، کافیست کانال `sms` را به متد `via` در کلاس notification خود اضافه کنید:

```php
public function via($notifiable)
{
    return ['sms'];
}
```

و سپس متد `toSms` را به کلاس اطلاع‌رسانی خود اضافه کنید:

```php
public function toSms($notifiable)
{
    return 'متن پیام';
}
```

### ایجاد درایورهای سفارشی

می‌توانید درایورهای سفارشی خود را ایجاد کنید. برای این کار، باید یک کلاس جدید بسازید که از `IranSms\Drivers\Driver` ارث‌بری کند و متد `send` را پیاده‌سازی کند.

```php
namespace App\SmsDrivers;

use IranSms\Drivers\Driver;

class MyCustomDriver extends Driver
{
    public function send($message, $numbers, $options = [])
    {
        // کد ارسال پیامک
    }
}
```

سپس می‌توانید درایور خود را به `SmsManager` اضافه کنید:

```php
use IranSms\SmsManager;

$smsManager = new SmsManager(app());

$smsManager->extend('myCustomDriver', function ($app) {
    return new MyCustomDriver();
});
```

### رویدادها

می‌توانید به این رویداد گوش دهید:

- **SmsSentEvent**: هنگامی که پیامک ارسال می‌شود. (این پکیج از این رویداد برای جمع‌آوری گزارش‌ها در دیتابیس استفاده می‌کند)

## مشارکت

لطفاً برای جزئیات بیشتر [CONTRIBUTING](CONTRIBUTING.md) و [CONDUCT](CONDUCT.md) را ببینید.

## اعتبارات

- [Hooman Mirghasemi][link-author]
- [تمامی مشارکت‌کنندگان][link-contributors]

## مجوز

مجوز MIT (MIT License). لطفاً برای اطلاعات بیشتر [فایل مجوز](LICENSE.md) را ببینید.

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
