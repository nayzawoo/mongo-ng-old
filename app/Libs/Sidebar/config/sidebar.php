<?php

return [
    'item'            => 'backend.sidebar.item',
    'subitem'         => 'backend.sidebar.subitem',
    'subitem-wrapper' => 'backend.sidebar.subitem-wrapper',
    'items'           => [
        'backend'  => array(
            'icon'  => 'icon-home',
            'name'  => 'Dashboard',
            'route' => 'backend',
        ),
        "Settings" => array(
            'icon'  => 'icon-users',
            'name'  => "Settings",
            'route' => '#',
            'child' => [
                [
                    'icon'  => 'icon',
                    'name'  => 'Cars',
                    'route' => 'backend.car.index',
                ], [
                    'icon'  => 'icon',
                    'name'  => 'House',
                    'route' => 'backend.house.index',
                ],
            ]),
    ],
];
