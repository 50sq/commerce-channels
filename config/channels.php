<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Channels
    |--------------------------------------------------------------------------
    |
    | Here you may define channels for your products. By default, this is
    | set to "null" and all products are managed directly in the database.
    | However, Commerce is able to also sync products from your external channels
    | with your database and present them locally for selling. If using a channel,
    | you must provide any necessary values below.
    |
    | Options include "null", "amazon", "bigcartel", "printful", and "shopify".
    |
    */

    'channels' => [

        'default' => env('COMMERCE_PRODUCT_CHANNEL'),

        'drivers' => [

            // https://developer.amazonservices.com/
            'amazon' => [
                'partner_tag' => env('AMAZON_PARTNER_TAG'),
                'token' => env('AMAZON_ACCESS_TOKEN'),
                'secret' => env('AMAZON_SECRET_TOKEN'),
            ],

            // https://developers.bigcartel.com/api/v1#products
            'bigcartel' => [
                'account_id' => env('BIGCARTEL_ACCOUNT_ID'),
                'username' => env('BIGCARTEL_USERNAME'),
                'password' => env('BIGCARTEL_PASSWORD'),
            ],

            // https://www.printful.com/docs
            'printful' => [
                'key' => env('PRINTFUL_API_KEY'),
                'webhook_token' => env('PRINTFUL_WEBHOOK_TOKEN'),
            ],

            // https://shopify.dev/docs/storefront-api
            'shopify' => [
                'shop_name' => env('SHOPIFY_SHOP_NAME'),
                'admin_key' => env('SHOPIFY_ADMIN_API_KEY'),
                'admin_password' => env('SHOPIFY_ADMIN_API_PASSWORD'),
                'storefront_token' => env('SHOPIFY_STOREFRONT_TOKEN'),
            ],

        ],

    ],

];
