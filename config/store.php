<?php

return [
    'name' => 'My Store',
    'description' => 'We sell great things',
    'subscriptions' => [
        'standard' => [
            'name' => 'Standard',
            'description' => 'Standard Subscription',
            'recurring' => true,
            'costperiunit' => 20.00,
            'stripe_product_id' => 'price_1I4RvEJ7YVOMPvssGkqjVBXP'
        ],
        'advanced' => [
            'name' => 'Advanced',
            'description' => 'Advanced subscription',
            'recurring' => true,
            'costperiunit' => 40.00,
            'stripe_product_id' => 'price_1I4RvxJ7YVOMPvssVtW12xOt'
        ]
    ]
];