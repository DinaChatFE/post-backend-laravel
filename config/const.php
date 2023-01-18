<?php

return [
    'format_date' => [
        'date_number' => 'd/m/Y',
        'date_string' => 'j-F-Y',
        'datetime' => 'd-m-Y H:i',
    ],
    'status' => [
        'processing' => 'Processing',
        'accept_by_store' => 'Accept by store', // accept by store  : setting can be auto and decline later
        'accept_by_delivery' => 'Accept by delivery', //  accept by delivery after user order
        'delivery_pick_up' => 'Delivery pick up', // take food from store
        'completed' => 'Completed',
    ],
    'food_status' => [
        'queuing' => "Queuing",
        'doing' => 'Doing',
        'done' => 'Done',
    ],
    'notification_type' => [
        'announcement' => 'Announcement',
        'notification' => 'Notification',
    ],
    'filePath' => [

        'small' => env('DO_SPACE_NAME', '') . '/uploads/files/small',
        'medium' => env('DO_SPACE_NAME', '') . '/uploads/files/medium',
        'large' => env('DO_SPACE_NAME', '') . '/uploads/files/large',
        'original' => env('DO_SPACE_NAME', '') . '/uploads/files/original',

        'default_image' => '/assets/default-placeholder.png',
        'default' => '/uploads/default/default.png',
    ],
    'search_syntax' => 'q',
    'currency_symbol' => [
        'dollar' => '$',
    ],
    'model' => [
        'total_rating_star' => 5,
    ],
    'pagination' => [
        'length' => 5
    ],
    'min_pass_length' => 4,
    'weather_api_key' => "c71e7689b7328487874f6161ed149bf4",
    'weather_url' => 'https://api.openweathermap.org',
];
