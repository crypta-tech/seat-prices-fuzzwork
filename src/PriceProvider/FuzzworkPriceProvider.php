<?php

namespace CryptaTech\Seat\FuzzworkPriceProvider\PriceProvider;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use JsonException;
use CryptaTech\Seat\FuzzworkPriceProvider\FuzzworkPriceProviderServiceProvider;
use RecursiveTree\Seat\PricesCore\Contracts\IPriceable;
use RecursiveTree\Seat\PricesCore\Contracts\IPriceProviderBackend;
use RecursiveTree\Seat\PricesCore\Exceptions\PriceProviderException;
use RecursiveTree\Seat\PricesCore\Utils\UserAgentBuilder;

class FuzzworkPriceProvider implements IPriceProviderBackend
{

    /**
     * Fetches the prices for the items in $items
     * Implementations should store the computed price directly on the Priceable object using the setPrice method.
     * In case an error occurs, a PriceProviderException should be thrown, so that an error message can be shown to the user.
     *
     * @param Collection<IPriceable> $items The items to appraise
     * @param array $configuration The configuration of this price provider backend.
     * @throws PriceProviderException
     */
    public function getPrices(Collection $items, array $configuration): void
    {
        // step 1: Collect TypeIDs we are interested in, if we have a cached entry use it straight away.

        $cacheprefix = 'fuzzworks_pricer_' . $configuration['id'];

        $typeIDs = [];
        $typeIDFetch = [];
        foreach ($items as $item) {
            $price = Cache::tags([$cacheprefix])->get($item->getTypeID());
            if (isset($price)) {
                $typeIDs[$item->getTypeID()] = floatval($price);
            } else {
                $typeIDFetch[] = $item->getTypeID();
            }
        }

        // dd($typeIDFetch, $typeIDs);

        // step 2: Request prices for those we still need.
        if (count($typeIDFetch) > 0) {
            $user_agent = (new UserAgentBuilder())
                ->seatPlugin(FuzzworkPriceProviderServiceProvider::class)
                ->defaultComments()
                ->build();

            $client = new \GuzzleHttp\Client([
                'base_uri' => "https://market.fuzzwork.co.uk/",
                'timeout' => $configuration['timeout'],
                'headers' => [
                    'User-Agent' => $user_agent,
                ]
            ]);

            try {
                $response = $client->get('aggregates/', [
                    'query' => [
                        'region' => $configuration['region'],
                        'types' => implode(',', $typeIDFetch)
                    ],
                    // 'debug' => true,
                ]);
                // dd(str($response->getBody()));
                // dd($response);
                $response = json_decode($response->getBody(), false, 64, JSON_THROW_ON_ERROR);
            } catch (GuzzleException | JsonException $e) {
                throw new PriceProviderException('Failed to load data from fuzzworks', 0, $e);
            }

            foreach ($response as $tid => $item) {
                if ($configuration['is_buy']) {
                    $price_bucket = $item->buy;
                } else {
                    $price_bucket = $item->sell;
                }

                $variant = $configuration['variant'];
                if ($variant == 'min') {
                    $price = $price_bucket->min;
                } elseif ($variant == 'max') {
                    $price = $price_bucket->max;
                } elseif ($variant == 'avg') {
                    $price = $price_bucket->weightedAverage;
                } elseif ($variant == 'median') {
                    $price = $price_bucket->median;
                } else {
                    $price = $price_bucket->percentile;
                }

                $typeIDs[$tid] = floatval($price);
                Cache::tags([$cacheprefix])->put($tid, $price, now()->addHours($configuration['cache']));
            }
        }
        // step 3: Feed prices back to system
        foreach ($items as $item) {
            $price = $typeIDs[$item->getTypeID()] ?? null;
            if ($price === null) {
                throw new PriceProviderException('Fuzzwork didn\'t respond with the requested prices.');
            }
            if (!(is_int($price) || is_float($price))) {
                throw new PriceProviderException('Fuzzwork responded with a non-numerical price: "' . $price . '". (' . gettype($price) . ').');
            }

            $item->setPrice($price * $item->getAmount());
        }
    }
}
