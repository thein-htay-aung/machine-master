<?php

return [
    // Keep this separate from COLD_STORE_API_KEY. Set a strong value in .env.
    'api_key' => env('ROOM_MONITORING_API_KEY'),

    'locations' => [
        'fish_processing_room' => 'Fish Processing Room',
        'production_room' => 'Production Room',
        'cutting_room' => 'Cutting Room',
        'drying_room' => 'Drying Room',
    ],

    'history_interval_minutes' => 15,
];
