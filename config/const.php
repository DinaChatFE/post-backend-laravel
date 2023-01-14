<?php

return [
    'formatDate' => [
        'date_number' => 'd/m/Y',
        'date_string' => 'j-F-Y',
        'datetime' => 'd-m-Y H:i'
    ],
    'status' => [
        'processing' => 'Processing',
        'accept_by_store' => 'Accept by store', // accept by store  : setting can be auto and decline later
        'accept_by_delivery'    => 'Accept by delivery', //  accept by delivery after user order
        'delivery_pick_up'  => 'Delivery pick up', // take food from store
        'completed'    => 'Completed'
    ],
    'food_status' => [
        'queuing' => "Queuing",
        'doing'   => 'Doing',
        'done'    => 'Done'
    ],
    'notification_type' => [
        'announcement' => 'Announcement',
        'notification' => 'Notification'
    ],
    'format_date' => [
        'day_month' => 'd/M',
        'date' => 'j F Y',
        'date2' => 'j M Y ',
        'datetime' => 'j F Y H:i A',
        'datetime2' => 'j/F/Y H:i A',
        'datetime3' => 'j M Y H:i',
        'datetime4' => 'Y-m-d H:i:s',
        'datetime5' => 'j M Y H:i',
        'datetime6' => 'j M Y H:i A',
        'year_month_day' => 'Y-m-d',
        'day_month_year' => 'd-m-Y',
    ],
    'filePath' => [

        'small' => env('DO_SPACE_NAME', '') . '/uploads/files/small',
        'medium' => env('DO_SPACE_NAME', '') . '/uploads/files/medium',
        'large' => env('DO_SPACE_NAME', '') . '/uploads/files/large',
        'original' => env('DO_SPACE_NAME', '') . '/uploads/files/original',

        'default_image' => '/uploads/files/default_image.png',
        'default' => '/uploads/default/default.png',
    ],
    'search_syntax' => 'q',
    'currency_symbol' => [
        'dollar' => '$'
    ],
    'model' => [
        'total_rating_star' => 5,
    ],
    'firebase' => [
        'url' => 'https://fcm.googleapis.com/fcm/send',
        'server_api_key' => 'AAAAuQj8Nvo:APA91bH-9CV7B6T9oINSFHFk7c6dao8dbmtEKa4ey8hjEJ_WdWC9WSfBoQ397CnXWV3q-2oQrdZaiEr-VWMXodHXBTDmtHqnF_FZKfHcyoFbAL2RJAqJJJmN_8NBrMS8PUuai2OLuT7f'
        // 'server_api_key' => 'eKzdr0yESq-tfcRo_Bp0at:APA91bGv1MOVPrapgFGz2X-XwmtzzPzou4sf5B77MidP-1pEJTQ5rgInzvSqo_1JmelqbwYc1u20n8tKLz_ZyC586SxZD6KyQp1WAIOOe6IG2uRC9-LT7zEOC56pp45MbKcuWDAB9Bx-'
    ],
    'min_pass_length' => 4,
    'options' => [
        'address_script_variable' => 'address_auto_detect',
        'bulk_request' => [
            'self' => 'v1'
        ],
        'map_types' => ['satellite', 'terrain'],
        'notification' => [
            'sms' => 'sms',
            'firebase' => 'firebase',
            'database' => 'database',
            'mail' => 'mail',
        ]
    ],
    'weather_api_key' => "c71e7689b7328487874f6161ed149bf4",
    'weather_url' => 'https://api.openweathermap.org'
];
