<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sidebar navigation
    |--------------------------------------------------------------------------
    |
    | Every entry here shows up in the dashboard sidebar, in sections. To add
    | a new feature, create its route, then add one line to a section below:
    |
    |   label  - text shown in the sidebar
    |   route  - route name the link points to
    |   icon   - a name from resources/views/components/icon.blade.php
    |   active - route name patterns that mark the link as the current page
    |
    */

    'nav' => [
        [
            'heading' => null,
            'items' => [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'home', 'active' => ['dashboard']],
                ['label' => 'Pick for me', 'route' => 'pick', 'icon' => 'dice', 'active' => ['pick']],
                ['label' => 'Outfit planner', 'route' => 'planner.index', 'icon' => 'calendar', 'active' => ['planner.*']],
            ],
        ],
        [
            'heading' => 'Wardrobe',
            'items' => [
                ['label' => 'Manage OOTD', 'route' => 'ootds.index', 'icon' => 'grid', 'active' => ['ootds.index', 'ootds.edit']],
                ['label' => 'Upload OOTD', 'route' => 'ootds.create', 'icon' => 'upload', 'active' => ['ootds.create']],
                ['label' => 'History', 'route' => 'history', 'icon' => 'clock', 'active' => ['history']],
            ],
        ],
        [
            'heading' => 'You',
            'items' => [
                ['label' => 'Notifications', 'route' => 'notifications.index', 'icon' => 'bell', 'active' => ['notifications.*']],
                ['label' => 'Account', 'route' => 'account.edit', 'icon' => 'user', 'active' => ['account.*']],
                ['label' => 'Settings', 'route' => 'settings.edit', 'icon' => 'sliders', 'active' => ['settings.*']],
            ],
        ],
    ],

];
