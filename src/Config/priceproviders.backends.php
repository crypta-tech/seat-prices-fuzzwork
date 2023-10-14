<?php

use CryptaTech\Seat\FuzzworkPriceProvider\PriceProvider\FuzzworkPriceProvider;

return [
    'cryptatech/seat-prices-fuzzwork' => [
        'backend'=> FuzzworkPriceProvider::class,
        'label'=>'fuzzworkpriceprovider::fuzzwork.fuzzwork_price_provider',
        'plugin'=>'cryptatech/seat-prices-fuzzwork',
        'settings_route' => 'fuzzworkpriceprovider::configuration',
    ]
];