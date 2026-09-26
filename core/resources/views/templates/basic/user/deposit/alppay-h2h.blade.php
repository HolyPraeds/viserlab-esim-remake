@extends($activeTemplate . 'layouts.master')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="deposit-card">
            <div class="deposit-header">
                <h4>{{ $pageTitle ?? __('Payment Instructions') }}</h4>
                <p class="text-muted">{{ showAmount($deposit->final_amount) }} {{ $deposit->method_currency ?? 'EUR' }} — {{ $deposit->trx }}</p>
            </div>

            <div class="deposit-body">
                @if(($state ?? null) === 'COMPLETED')
                    <div class="alert alert-success">
                        <i class="las la-check-circle"></i> @lang('Payment completed successfully.')
                    </div>
                    <a href="{{ route('user.deposit.history') }}" class="btn btn--primary">@lang('Go to Deposit History')</a>

                @elseif(in_array($state ?? null, ['DECLINED', 'CANCELLED']))
                    <div class="alert alert-danger">
                        <i class="las la-times-circle"></i> @lang('Payment was') {{ strtolower($state) }}.
                    </div>
                    <a href="{{ route('user.deposit.index') }}" class="btn btn--primary">@lang('Try Again')</a>
                    <a href="{{ route('user.deposit.history') }}" class="btn btn-outline--base">@lang('Deposit History')</a>

                @else
                    @if(!empty($externalRefs) && is_array($externalRefs))
                        <div class="mb-4">
                            <h6>@lang('Instructions')</h6>
                            <ul class="list-group">
                                @foreach($externalRefs as $ref)
                                    @if(is_array($ref))
                                        @if(!empty($ref['url']))
                                            <li class="list-group-item"><a href="{{ $ref['url'] }}" target="_blank" rel="noopener">{{ $ref['name'] ?? $ref['url'] }}</a></li>
                                        @elseif(!empty($ref['name']))
                                            <li class="list-group-item">{{ $ref['name'] }}</li>
                                        @endif
                                    @else
                                        <li class="list-group-item">{{ $ref }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(!empty($cardSubmitted))
                        <div class="alert alert-info mb-4">
                            <i class="las la-hourglass-half"></i> @lang('Payment is being processed. Please wait or check status.')
                        </div>
                        <form action="{{ route('user.deposit.alppay.h2h.check') }}" method="GET" class="d-inline">
                            <input type="hidden" name="deposit_trx" value="{{ $deposit->trx }}">
                            <button type="submit" class="btn btn--primary">@lang('Check Status')</button>
                        </form>
                    @else
                        <div class="mb-3">
                            <form action="{{ route('user.deposit.alppay.h2h.check') }}" method="GET" class="d-inline">
                                <input type="hidden" name="deposit_trx" value="{{ $deposit->trx }}">
                                <button type="submit" class="btn btn-outline--base btn-sm">@lang('Check Status')</button>
                            </form>
                        </div>
                        {{-- Form to submit card (PATCH) when payment exists but card not yet sent --}}
                        <form action="{{ route('user.deposit.alppay.h2h.submit') }}" method="POST" id="h2hSubmitForm" class="mb-4">
                            @csrf
                            <input type="hidden" name="deposit_trx" value="{{ $deposit->trx }}">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">@lang('First Name')</label>
                                    <input type="text" class="form-control form--control" name="first_name" value="{{ old('first_name', optional($deposit->user)->firstname ?? '') }}" required maxlength="255">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">@lang('Last Name')</label>
                                    <input type="text" class="form-control form--control" name="last_name" value="{{ old('last_name', optional($deposit->user)->lastname ?? '') }}" required maxlength="255">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">@lang('Email')</label>
                                    <input type="email" class="form-control form--control" name="email" value="{{ old('email', optional($deposit->user)->email ?? '') }}" required maxlength="255">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">@lang('Cardholder Name')</label>
                                    <input type="text" class="form-control form--control" name="cardholder_name" value="{{ old('cardholder_name') }}" placeholder="JANE DOE — as on the card" required maxlength="255">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">@lang('Card Number')</label>
                                    <input type="text" class="form-control form--control" name="card_number" value="{{ old('card_number') }}" placeholder="4242 4242 4242 4242" required maxlength="19">
                                </div>
                                <div class="col-4">
                                    <label class="form-label">@lang('Expiry') MM</label>
                                    <input type="text" class="form-control form--control" name="expiry_month" value="{{ old('expiry_month') }}" placeholder="06" required size="2" maxlength="2">
                                </div>
                                <div class="col-4">
                                    <label class="form-label">@lang('Expiry') YYYY</label>
                                    <input type="text" class="form-control form--control" name="expiry_year" value="{{ old('expiry_year') }}" placeholder="2029" required size="4" maxlength="4">
                                </div>
                                <div class="col-4">
                                    <label class="form-label">@lang('CVV')</label>
                                    <input type="text" class="form-control form--control" name="cvv" value="{{ old('cvv') }}" placeholder="•••" required minlength="3" maxlength="4">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">@lang('Phone')</label>
                                    <input type="text" class="form-control form--control" name="phone" value="{{ old('phone', optional($deposit->user)->mobile ?? '') }}" maxlength="50" placeholder="1 555 123 4567">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">@lang('Address')</label>
                                    <input type="text" class="form-control form--control" name="address_line1" value="{{ old('address_line1', optional($deposit->user)->address ?? '') }}" maxlength="255" placeholder="1 Infinite Loop, Cupertino">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">@lang('City')</label>
                                    <input type="text" class="form-control form--control" name="city" value="{{ old('city', optional($deposit->user)->city ?? '') }}" maxlength="255" placeholder="San Francisco">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">@lang('Postal Code')</label>
                                    <input type="text" class="form-control form--control" name="postal_code" value="{{ old('postal_code', optional($deposit->user)->zip ?? '') }}" maxlength="20" placeholder="95014">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">@lang('Country')</label>
                                    <input type="text" class="form-control form--control" name="country_code" value="{{ old('country_code', optional($deposit->user)->country_code ?? 'US') }}" size="2" maxlength="2" placeholder="US">
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn--primary">@lang('Submit Card & Pay')</button>
                            </div>
                        </form>
                    @endif

                    <a href="{{ route('user.deposit.history') }}" class="btn btn-outline--base">@lang('Deposit History')</a>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.deposit-card { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
.deposit-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
.deposit-body { padding: 30px; }
</style>
@endsection
