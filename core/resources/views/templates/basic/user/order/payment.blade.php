@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="my-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <!-- Wallet Payment Option -->
                    @auth
                        @php
                            $paymentCurrency = strtoupper($order->payment_currency ?? 'EUR');
                            $walletAmountCredits = (float) $order->total_amount;
                            if ($paymentCurrency === 'GBP') {
                                $walletAmountCredits = $walletAmountCredits / 0.87;
                            } elseif ($paymentCurrency === 'USD') {
                                $walletAmountCredits = $walletAmountCredits / 1.18;
                            }
                            $walletAmountCredits = round($walletAmountCredits, 2);
                        @endphp
                        @if(auth()->user()->balance >= $walletAmountCredits)
                            <div class="p-2 border rounded mb-3 bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong class="text-success">
                                            <i class="las la-wallet"></i> @lang('Pay from Wallet')
                                        </strong>
                                        <br>
                                        <small class="text-muted">
                                            {{ number_format($walletAmountCredits, 2) }} Credits
                                            • Balance: {{ showAmount(auth()->user()->balance, 2, true, false, true, true) }}
                                        </small>
                                    </div>
                                    <form action="{{ route('user.order.pay.from.wallet', $order->order_number) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn--success btn-sm">
                                            <i class="las la-wallet"></i> @lang('Pay')
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="p-2 border rounded mb-3 bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong class="text-warning">
                                            <i class="las la-exclamation-triangle"></i> @lang('Insufficient Balance')
                                        </strong>
                                        <br>
                                        <small class="text-muted">
                                            Need: {{ number_format($walletAmountCredits, 2) }} Credits
                                            • Have: {{ showAmount(auth()->user()->balance, 2, true, false, true, true) }}
                                        </small>
                                    </div>
                                    <a href="{{ route('user.deposit.index') }}" class="btn btn--primary btn-sm">
                                        <i class="las la-plus"></i> @lang('Add')
                                    </a>
                                </div>
                            </div>
                        @endif
                    @endauth
                    
                    <!-- Card Payment Option -->
                    <div class="payment-system-list p-3 border rounded">
                        <h4 class="mb-3">@lang('Card Payment')</h4>
                        <p class="text-muted mb-4">@lang('Pay securely by card')</p>
                        <p class="small text-muted mb-3"><strong>@lang('Merchant of Record'):</strong> BROOKBURN INTERNATIONAL LTD, 35 Firs Avenue, London, England, N11 3NE</p>
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge bg-secondary px-2 py-1" title="3D Secure">3D Secure</span>
                            <span class="small text-muted">@lang('Protected')</span>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-6">
                                <div class="deposit-info">
                                    <div class="deposit-info__title"><p class="text mb-0">@lang('Amount')</p></div>
                                    <div class="deposit-info__input">
                                        @php
                                            $paymentCurrency = $order->payment_currency ?? 'EUR';
                                            $currencySymbols = ['EUR' => '€', 'GBP' => '£', 'USD' => '$'];
                                            $currencySymbol = $currencySymbols[$paymentCurrency] ?? '€';
                                        @endphp
                                        {{ $currencySymbol }}{{ number_format($order->total_amount, 2) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="deposit-info">
                                    <div class="deposit-info__title"><p class="text mb-0">@lang('Processing Charge')</p></div>
                                    <div class="deposit-info__input">
                                        @php
                                            $paymentCurrency = $order->payment_currency ?? 'EUR';
                                            $currencySymbols = ['EUR' => '€', 'GBP' => '£', 'USD' => '$'];
                                            $currencySymbol = $currencySymbols[$paymentCurrency] ?? '€';
                                        @endphp
                                        {{ $currencySymbol }}0.00
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('user.order.payment.taurixy.direct') }}" method="POST" class="checkout-form">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                            <input type="hidden" name="payment_method" value="BASIC_CARD">

                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('First name')</label>
                                    <input type="text" name="first_name" class="form-control form--control" value="{{ auth()->user()?->firstname ?? '' }}" placeholder="@lang('First name')" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Last name')</label>
                                    <input type="text" name="last_name" class="form-control form--control" value="{{ auth()->user()?->lastname ?? '' }}" placeholder="@lang('Last name')" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Country / Region')</label>
                                    <input type="text" name="country" class="form-control form--control" value="{{ auth()->user()?->country_name ?? '' }}" placeholder="@lang('Country / Region')" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Town / City')</label>
                                    <input type="text" name="city" class="form-control form--control" value="{{ auth()->user()?->city ?? '' }}" placeholder="@lang('Town / City')" required>
                                </div>
                                <div class="col-sm-8">
                                    <label class="form--label required">@lang('Street address')</label>
                                    <input type="text" name="address" class="form-control form--control" value="{{ auth()->user()?->address ?? '' }}" placeholder="@lang('Street address')" required>
                                </div>
                                <div class="col-sm-4">
                                    <label class="form--label required">@lang('Postcode / ZIP')</label>
                                    <input type="text" name="zip" class="form-control form--control" value="{{ auth()->user()?->zip ?? '' }}" placeholder="@lang('Postcode / ZIP')" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Email address')</label>
                                    <input type="email" name="email" class="form-control form--control" value="{{ auth()->user()?->email ?? '' }}" placeholder="@lang('Email address')" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form--label required">@lang('Phone')</label>
                                    <input type="text" name="phone" class="form-control form--control" value="{{ auth()->user()?->mobile ?? '' }}" placeholder="@lang('Phone')" required>
                                </div>
                                <div class="col-12 pt-2">
                                    <button type="submit" class="btn btn--base w-100 mb-2">@lang('Pay with Card')</button>
                                    <p class="mt-2 text-muted small"><input type="checkbox" required> @lang('Your personal data will be used to process your order and for other purposes described in the') <a href="{{ route('policy.pages', 'privacy-policy') }}" target="_blank">@lang('privacy policy')</a>. @lang('By proceeding with the purchase I accept the') <a href="{{ route('policy.pages', 'terms-and-conditions') }}" target="_blank">@lang('Terms and Conditions')</a> @lang('and') <a href="{{ route('policy.pages', 'privacy-policy') }}" target="_blank">@lang('Privacy Policies')</a> @lang('of') <a href="https://travelsim.live/">travelsim.live</a></p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
@endpush
