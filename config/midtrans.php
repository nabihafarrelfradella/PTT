<?php

return [
    'merchant_id'   => env('MIDTRANS_MERCHANT_ID', 'G726410423'),
    'server_key'    => env('MIDTRANS_SERVER_KEY', 'Mid-server-_ZHEacc6iMW6PRGsCpLaIPjK'),
    'client_key'    => env('MIDTRANS_CLIENT_KEY', 'Mid-client-qgNdchm5-2Ye8mAJ'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized'  => env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds'        => env('MIDTRANS_IS_3DS', false),
];
