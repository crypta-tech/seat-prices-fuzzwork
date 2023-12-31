<?php

namespace CryptaTech\Seat\FuzzworkPriceProvider;

use Seat\Services\AbstractSeatPlugin;

class FuzzworkPriceProviderServiceProvider extends AbstractSeatPlugin
{
    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/priceproviders.backends.php','priceproviders.backends');
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'fuzzworkpriceprovider');
        $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'fuzzworkpriceprovider');
    }

    /**
     * Return the plugin public name as it should be displayed into settings.
     *
     * @return string
     * @example SeAT Web
     *
     */
    public function getName(): string
    {
        return 'Fuzzwork Price Provider';
    }

    /**
     * Return the plugin repository address.
     *
     * @example https://github.com/eveseat/web
     *
     * @return string
     */
    public function getPackageRepositoryUrl(): string
    {
        return 'https://github.com/crypta-tech/seat-prices-fuzzwork';
    }

    /**
     * Return the plugin technical name as published on package manager.
     *
     * @return string
     * @example web
     *
     */
    public function getPackagistPackageName(): string
    {
        return 'seat-prices-fuzzwork';
    }

    /**
     * Return the plugin vendor tag as published on package manager.
     *
     * @return string
     * @example eveseat
     *
     */
    public function getPackagistVendorName(): string
    {
        return 'cryptatech';
    }
}