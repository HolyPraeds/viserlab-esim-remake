@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('admin.api.save') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <div class="form-group col-xl-12 col-lg-12 col-md-12">
                                <label>
                                    <a target="_blank">ESIM provider</a>
                                    @lang('API Key')
                                </label>
                                <input type="text" class="form-control" name="api_key" value="{{ old('api_key', $apiKey) }}" required>
                            </div>

                            <div class="form-group">
                                <label for="currency_api_key">
                                    <a href="https://currencylayer.com" target="_blank">currencylayer.com</a> @lang('API Key (If any)')
                                </label>
                                <input type="text" class="form-control" name="currency_api_key" value="{{ old('currency_api_key', $currencyApiKey) }}">
                            </div>

                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
