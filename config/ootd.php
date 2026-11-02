<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Occasions
    |--------------------------------------------------------------------------
    |
    | Optional label a user can put on an outfit ("what is this good for?").
    | Used for filtering on Manage OOTD and for narrowing "Pick for me".
    | Add a line here to add an occasion: 'key' => 'Label shown to users'.
    |
    */

    'occasions' => [
        'work' => 'Work',
        'casual' => 'Casual',
        'going-out' => 'Going out',
        'formal' => 'Formal',
        'lounge' => 'Lounge',
    ],

    /*
    |--------------------------------------------------------------------------
    | Milestones
    |--------------------------------------------------------------------------
    |
    | When a user's saved outfit count reaches one of these numbers they get a
    | notification.
    |
    */

    'milestones' => [1, 5, 10, 25, 50, 100],

    'image_storage' => env('OOTD_IMAGE_STORAGE', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Daily reminder
    |--------------------------------------------------------------------------
    */

    'default_reminder_time' => '18:00',

];
