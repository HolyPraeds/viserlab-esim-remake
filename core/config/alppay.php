<?php

return [
    // Production URL by default - change to sandbox only for testing
    'base_url'    => env('ALPPAY_BASE_URL', 'https://engine.alp-pay.com'),
    'api_key'     => env('ALPPAY_API_KEY', ''),
    'signing_key' => env('ALPPAY_SIGNING_KEY', ''),
    'shop_id'     => env('ALPPAY_SHOP_ID', ''),
    // Must match POST .../webhooks/alppay (same host/path prefix as APP_URL / ngrok), e.g.
    // https://xxx.ngrok-free.dev/viserlab-esim-remake/webhooks/alppay
    'webhook_url' => env('ALPPAY_WEBHOOK_URL', ''),
    // Explicit return URL for HPP redirect. If empty, uses route().
    // Use when APP_URL doesn't match real site URL (e.g. site is at /core).
    'order_return_url'   => env('ALPPAY_ORDER_RETURN_URL', ''),   // order checkout
    'deposit_return_url' => env('ALPPAY_DEPOSIT_RETURN_URL', ''), // deposit top-up
];