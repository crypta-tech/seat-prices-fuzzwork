<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['web', 'auth', 'locale'],
    'prefix' => '/prices-fuzzwork',
    'namespace'=>'CryptaTech\Seat\FuzzworkPriceProvider\Http\Controllers'
], function () {
    Route::get('/configuration')
        ->name('fuzzworkpriceprovider::configuration')
        ->uses('FuzzworkPriceProviderController@configuration')
        ->middleware('can:pricescore.settings');

    Route::post('/configuration')
        ->name('fuzzworkpriceprovider::configuration.post')
        ->uses('FuzzworkPriceProviderController@configurationPost')
        ->middleware('can:pricescore.settings');
});