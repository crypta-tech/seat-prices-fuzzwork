@extends('web::layouts.app')

@section('title', trans('fuzzworkpriceprovider::fuzzwork.edit_price_provider'))
@section('page_header', trans('fuzzworkpriceprovider::fuzzwork.edit_price_provider'))
@section('page_description', trans('fuzzworkpriceprovider::fuzzwork.edit_price_provider'))

@section('content')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ trans('fuzzworkpriceprovider::fuzzwork.edit_price_provider') }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('fuzzworkpriceprovider::configuration.post') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $id ?? '' }}">

                <div class="form-group">
                    <label for="name">{{ trans('pricescore::settings.name') }}</label>
                    <input required type="text" name="name" id="name" class="form-control" placeholder="{{ trans('pricescore::settings.name_placeholder') }}" value="{{ $name ?? '' }}">
                </div>

                <div class="form-group">
                    <label for="region">{{ trans('fuzzworkpriceprovider::fuzzwork.region') }}</label>
                    <select id="region" name="region" class="form-control" style="width: 100%;"></select>
                </div>

                <div class="form-group">
                    <label for="cache">{{ trans('fuzzworkpriceprovider::fuzzwork.cache') }}</label>
                    <input required type="number" name="cache" id="cache" class="form-control" value="{{ $cache ?? '12' }}">
                </div>

                <div class="form-group">
                    <label for="timeout">{{ trans('fuzzworkpriceprovider::fuzzwork.timeout') }}</label>
                    <input required type="number" name="timeout" id="timeout" class="form-control" placeholder="{{ trans('pricescore::settings.timeout_placeholder') }}" value="{{ $timeout ?? 5 }}" min="0" step="1">
                    <small class="form-text text-muted">{{ trans('fuzzworkpriceprovider::fuzzwork.timeout_description') }}</small>
                </div>

                <div class="form-group">
                    <label for="price_type">{{ trans('fuzzworkpriceprovider::fuzzwork.price_type') }}</label>
                    <select name="price_type" id="price_type" class="form-control" required>
                        <option value="sell" @if(!$is_buy) selected @endif>{{ trans('fuzzworkpriceprovider::fuzzwork.sell') }}</option>
                        <option value="buy" @if($is_buy) selected @endif>{{ trans('fuzzworkpriceprovider::fuzzwork.buy') }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="price_variant">{{ trans('fuzzworkpriceprovider::fuzzwork.price_variant') }}</label>
                    <select name="price_variant" id="price_variant" class="form-control" required>
                        <option value="max" @if($price_variant==='max') selected @endif>{{ trans('fuzzworkpriceprovider::fuzzwork.max') }}</option>
                        <option value="min" @if($price_variant==='min') selected @endif>{{ trans('fuzzworkpriceprovider::fuzzwork.min') }}</option>
                        <option value="avg" @if($price_variant==='avg') selected @endif>{{ trans('fuzzworkpriceprovider::fuzzwork.avg') }}</option>
                        <option value="median" @if($price_variant==='median') selected @endif>{{ trans('fuzzworkpriceprovider::fuzzwork.median') }}</option>
                        <option value="percentile" @if($price_variant==='percentile') selected @endif>{{ trans('fuzzworkpriceprovider::fuzzwork.percentile') }}</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">{{ trans('pricescore::priceprovider.save')  }}</button>
            </form>
        </div>
    </div>

@endsection


@push('javascript')
<script type="text/javascript">

    $(document).ready(function () {
    var market_prices_region = $('#region');
    market_prices_region.select2({
        ajax: {
            url: '{{ route('seatcore::fastlookup.regions') }}',
            dataType: 'json'
        }
    });

    $.ajax({
        type: 'get',
        url: '{{ route('seatcore::fastlookup.regions') }}?_type=find&q={{ $region ?: 0 }}'
    }).then(function (data) {
        var option = new Option(data.text, data.id, true, true);
        market_prices_region.append(option).trigger('change');

        market_prices_region.trigger({
            type: 'select2:select',
            params: {
                data: data
            }
        });
    });
    });

</script>
@endpush