@extends($activeTemplate . 'layouts.master')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="deposit-card">
            <div class="deposit-header">
                <h4>@lang('Pay by Card (H2H)')</h4>
                <p class="text-muted">@lang('Enter your card details to complete the deposit')</p>
            </div>

            <div class="deposit-body">
                <div class="deposit-summary mb-4">
                    <div class="summary-line">
                        <span>@lang('Amount')</span>
                        <span>{{ showAmount($deposit->final_amount) }} {{ $deposit->method_currency ?? 'EUR' }}</span>
                    </div>
                    <div class="summary-line">
                        <span>@lang('Transaction ID')</span>
                        <span>{{ $deposit->trx }}</span>
                    </div>
                </div>

                <form action="{{ route('user.deposit.alppay.h2h.createWithCard') }}" method="POST" id="h2hCardForm">
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
                            <input type="email" class="form-control form--control" name="email" value="{{ old('email', optional($deposit->user)->email ?? '') }}" required maxlength="255" placeholder="you@example.com">
                        </div>
                        <div class="col-12 mt-2 pt-2 border-top">
                            <span class="text-muted small"><i class="las la-credit-card"></i> @lang('Card details') — we do not store your number</span>
                        </div>
                        <div class="col-12">
                            <label class="form-label">@lang('Cardholder Name')</label>
                            <input type="text" class="form-control form--control" name="cardholder_name" value="{{ old('cardholder_name') }}" placeholder="JOHN DOE — exactly as on the card" required maxlength="255">
                        </div>
                        <div class="col-12">
                            <label class="form-label">@lang('Card Number')</label>
                            <input type="text" class="form-control form--control" name="card_number" value="{{ old('card_number') }}" placeholder="4242 4242 4242 4242 · 16 digits, spaces ok" required autocomplete="cc-number" maxlength="19">
                        </div>
                        <div class="col-4">
                            <label class="form-label">@lang('Expiry Month')</label>
                            <input type="text" class="form-control form--control" name="expiry_month" value="{{ old('expiry_month') }}" placeholder="12" required size="2" maxlength="2" pattern="[0-9]{2}" title="01–12">
                        </div>
                        <div class="col-4">
                            <label class="form-label">@lang('Expiry Year')</label>
                            <input type="text" class="form-control form--control" name="expiry_year" value="{{ old('expiry_year') }}" placeholder="2030" required size="4" maxlength="4" pattern="[0-9]{4}" title="e.g. 2028">
                        </div>
                        <div class="col-4">
                            <label class="form-label">@lang('CVV')</label>
                            <input type="text" class="form-control form--control" name="cvv" value="{{ old('cvv') }}" placeholder="•••" required minlength="3" maxlength="4" autocomplete="off" title="3–4 digits on the back">
                        </div>
                        <div class="col-12">
                            <label class="form-label">@lang('Phone') <span class="text-muted">(optional)</span></label>
                            <input type="text" class="form-control form--control" name="phone" value="{{ old('phone', optional($deposit->user)->mobile ?? optional($deposit->user)->dial_code ?? '') }}" maxlength="50" placeholder="44 7911 123456 · country + number with space">
                        </div>
                        <div class="col-12">
                            <label class="form-label">@lang('Address') <span class="text-muted">(optional)</span></label>
                            <input type="text" class="form-control form--control" name="address_line1" value="{{ old('address_line1', optional($deposit->user)->address ?? '') }}" maxlength="255" placeholder="10 Downing Street, Westminster">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">@lang('City')</label>
                            <input type="text" class="form-control form--control" name="city" value="{{ old('city', optional($deposit->user)->city ?? '') }}" maxlength="255" placeholder="London">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">@lang('Postal Code')</label>
                            <input type="text" class="form-control form--control" name="postal_code" value="{{ old('postal_code', optional($deposit->user)->zip ?? '') }}" maxlength="20" placeholder="SW1A 2AA">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">@lang('Country') <span class="text-muted">(ISO)</span></label>
                            <input type="text" class="form-control form--control" name="country_code" value="{{ old('country_code', optional($deposit->user)->country_code ?? 'US') }}" size="2" maxlength="2" placeholder="GB" title="US, GB, DE, FR…">
                        </div>
                    </div>
                    <p class="text-muted small mt-2"><i class="las la-shield-alt"></i> @lang('Secured by Alp-Pay. Card data is not stored.')</p>

                    <div class="mt-4 d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn--primary">@lang('Pay') {{ showAmount($deposit->final_amount) }} {{ $deposit->method_currency ?? 'EUR' }}</button>
                        <a href="{{ route('user.deposit.confirm') }}" class="btn btn-outline--base">@lang('Cancel')</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.deposit-card { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
.deposit-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
.deposit-body { padding: 30px; }
.deposit-summary { background: #f8f9fa; padding: 20px; border-radius: 10px; }
.summary-line { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
</style>
@endsection
