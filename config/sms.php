<?php

return [
    'name' => 'Sms',

    /*
    |--------------------------------------------------------------------------
    | Default Driver
    |--------------------------------------------------------------------------
    |
    | This value determines which of the following gateway to use.
    | You can switch to a different driver at runtime.
    |
    */
    'driver'            => env('SMS_DRIVER', 'fake'),
    'driver_voice_call' => env('VOICE_CALL_DRIVER', 'fake'),

    /*
    |--------------------------------------------------------------------------
    | List of Drivers
    |--------------------------------------------------------------------------
    |
    | These are the list of drivers to use for this package.
    | You can change the name. Then you'll have to change
    | it in the map array too.
    |
    */
    'drivers' => [
        'fake' => [
            'from' => env('FAKE_SENDER_NUMBER', '111111'),
        ],
        'kavenegar' => [
            'from'   => env('KAVENEGAR_SENDER_NUMBER', ''),
            'apiKey' => env('KAVENEGAR_API_KEY'),
        ],
        'magfa' => [
            'username' => env('SMS_MAGFA_USERNAME'),
            'password' => env('SMS_MAGFA_PASSWORD'),
            'domain'   => env('SMS_MAGFA_DOMAIN'),
            'from'     => env('SMS_MAGFA_SENDER_NUMBER', ''),
            'wsdl_url' => 'https://sms.magfa.com/api/soap/sms/v2/server?wsdl',
        ],
        'smsonline' => [
            'username' => env('SMS_ONLINE_USERNAME'),
            'password' => env('SMS_ONLINE_PASSWORD'),
            'from'     => env('SMS_ONLINE_SENDER_NUMBER', ''),
            'wsdl_url' => 'http://www.linepayamak.ir/Post/Send.asmx?wsdl',
        ],
        'avanak' => [
            'username' => env('VOICE_AVANAK_USERNAME'),
            'password' => env('VOICE_AVANAK_PASSWORD'),
            'from'     => env('VOICE_AVANAK_SENDER_NUMBER', ''),
            'wsdl_url' => 'http://portal.avanak.ir/webservice3.asmx?WSDL',
        ],
    ],
    'dont_show_sms_list_page_condition' => config('app.env') == 'production',
];
