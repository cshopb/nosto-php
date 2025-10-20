<?php

/*
|--------------------------------------------------------------------------
| Third Party API configurations
|--------------------------------------------------------------------------
|
| Set the configuration values for the 3rd party API's the app uses.
| Make sure to not put in any sensitive data here, as this file is tracked with version control.
| For sensitive data, store it in the `.env`, and retrieve it with `env()`.
|
*/
return [
    'swop-cx' => [
        'uri' => [
            'base' => 'https://swop.cx',
            'listOfRates' => '/rest/rates',
            'availableCurrencies' => '/rest/currencies',
        ],
        'apiKey' => 'ApiKey ' . env('SWOP_CX_API_KEY'),
    ],
];
