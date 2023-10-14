<?php

namespace CryptaTech\Seat\FuzzworkPriceProvider\Http\Controllers;

use Illuminate\Http\Request;
use RecursiveTree\Seat\PricesCore\Models\PriceProviderInstance;
use Seat\Web\Http\Controllers\Controller;

class FuzzworkPriceProviderController extends Controller
{
    public function configuration(Request $request){
        $existing = PriceProviderInstance::find($request->id);

        $name = $request->name ?? $existing->name;
        $id = $request->id;
        $region = $existing->configuration['region'] ?? 10000002;
        $cache = $existing->configuration['cache'] ?? 12;
        $timeout = $existing->configuration['timeout'] ?? 5;
        $is_buy = $existing->configuration['is_buy'] ?? false;
        $price_variant = $existing->configuration['variant'] ?? 'min';

        return view('fuzzworkpriceprovider::configuration', compact('name', 'region', 'id', 'timeout', 'is_buy', 'price_variant'));
    }

    public function configurationPost(Request $request) {
        $request->validate([
            'id'=>'nullable|integer',
            'name'=>'required|string',
            'region'=>'required|integer',
            'cache'=>'required|integer|min:1|max:24',
            'timeout'=>'required|integer',
            'price_type' => 'required|string|in:sell,buy',
            'price_variant' => 'required|string|in:min,max,avg,median,percentile',
        ]);

        $model = PriceProviderInstance::findOrNew($request->id);
        $model->name = $request->name;
        $model->backend = 'cryptatech/seat-prices-fuzzwork';
        $model->configuration = [
            'id' => $request->id,
            'region' => $request->region,
            'cache' => $request->cache,
            'timeout' => $request->timeout,
            'is_buy' => $request->price_type === 'buy',
            'variant' => $request->price_variant,
        ];
        $model->save();

        Cache::tags(['fuzzwork_pricer_' . $request->id ])->flush();

        return redirect()->route('pricescore::settings')->with('success',trans('fuzzworkpriceprovider::fuzzwork.edit_price_provider_success'));
    }
}