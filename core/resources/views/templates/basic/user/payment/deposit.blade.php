@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="my-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <form action="{{ route('user.deposit.insert') }}" method="post" class="deposit-form">
                        @csrf
                        <input type="hidden" name="currency" id="selected_currency" value="EUR">
                        <div class="row justify-content-center gy-sm-4 gy-3">
                            <div class="col-lg-6">
                                <div class="payment-system-list gateway-option-list">
                                    @foreach($gatewayCurrencies as $gc)
                                        @php
                                            $inputId = 'deposit_by_card_' . strtolower($gc->currency);
                                        @endphp
                                        <label for="{{ $inputId }}" class="payment-item gateway-option" data-currency="{{ $gc->currency }}" data-gateway='@json($gc)'>
                                            <div class="payment-item__info">
                                                <span class="payment-item__check"></span>
                                                <span class="payment-item__name">@lang('Deposit by Card') {{ $gc->currency }}</span>
                                            </div>
                                            <div class="payment-item__thumb">
                                                <div class="payment-item__thumb-img" style="display: flex; align-items: center; justify-content: center; font-size: 24px;">
                                                    💳
                                                </div>
                                            </div>
                                            <input class="payment-item__radio gateway-input" id="{{ $inputId }}" hidden type="radio" name="gateway" value="{{ $gc->method_code }}" data-currency="{{ $gc->currency }}" @checked($loop->first)>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="payment-system-list p-3">
                                    <div class="deposit-info">
                                        <div class="deposit-info__title">
                                            <p class="text mb-0">@lang('Amount')</p>
                                        </div>
                                        <div class="deposit-info__input">
                                            <div class="deposit-info__input-group input-group">
                                                <span class="deposit-info__input-group-text currency-symbol">€</span>
                                                <input type="text" class="form-control form--control amount" name="amount" placeholder="@lang('00.00')" value="{{ old('amount') }}" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="deposit-info">
                                        <div class="deposit-info__title">
                                            <p class="text has-icon"> @lang('Limit')
                                                <span></span>
                                            </p>
                                        </div>
                                        <div class="deposit-info__input">
                                            <p class="text"><span class="gateway-limit">@lang('0.00')</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="deposit-info">
                                        <div class="deposit-info__title">
                                            <p class="text has-icon">@lang('Processing Charge')
                                                <span data-bs-toggle="tooltip" title="@lang('Processing charge for payment gateways')" class="proccessing-fee-info"><i class="las la-info-circle"></i> </span>
                                            </p>
                                        </div>
                                        <div class="deposit-info__input">
                                            <p class="text"><span class="processing-fee">@lang('0.00')</span>
                                                <span class="currency-text">EUR</span>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="deposit-info total-amount pt-3">
                                        <div class="deposit-info__title">
                                            <p class="text">@lang('Total')</p>
                                        </div>
                                        <div class="deposit-info__input">
                                            <p class="text"><span class="final-amount">@lang('0.00')</span>
                                                <span class="currency-text">EUR</span></p>
                                        </div>
                                    </div>

                                    <div class="d-none crypto-message mb-3">
                                        @lang('Conversion with') <span class="gateway-currency"></span> @lang('and final value will Show on next step')
                                    </div>
                                    <p class="mt-2 text-muted small">
                                        <input type="checkbox" required> @lang('I accept the') <a href="{{ route('policy.pages', 'terms-and-conditions') }}" target="_blank">@lang('Terms and Conditions')</a> @lang('and') <a href="{{ route('policy.pages', 'privacy-policy') }}" target="_blank">@lang('Privacy Policy')</a> @lang('of') <a href="https://travelsim.live/">travelsim.live</a>
                                    </p>
                                    <button type="submit" class="btn btn--base w-100 mt-2" disabled>
                                        @lang('Confirm Deposit')
                                    </button>
                                    <div class="info-text pt-3">
                                        <p class="text">@lang('Ensuring your funds grow safely through our secure deposit process with world-class payment options.')</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            // Currency data from backend
            var gatewayCurrencies = @json($gatewayCurrencies->keyBy('currency'));
            
            var amount = parseFloat($('.amount').val() || 0);
            var currentCurrency = 'EUR';
            var currentGateway = null;
            
            // Currency symbols mapping
            var currencySymbols = {
                'EUR': '€',
                'GBP': '£',
                'USD': '$'
            };

            // Handle gateway/currency selection
            $('.gateway-input').on('change', function() {
                var currency = $(this).data('currency');
                selectCurrency(currency);
            });

            function selectCurrency(currency) {
                currentCurrency = currency;
                currentGateway = gatewayCurrencies[currency];
                
                if (!currentGateway) {
                    console.error('Gateway currency not found for:', currency);
                    return;
                }
                
                // Update UI
                $('#selected_currency').val(currency);
                $('.currency-symbol').text(currencySymbols[currency]);
                $('.currency-text').text(currency);
                
                calculation();
            }

            $('.amount').on('input', function(e) {
                amount = parseFloat($(this).val()) || 0;
                calculation();
            });

            function calculation() {
                if (!currentGateway) return;
                
                var minAmount = parseFloat(currentGateway.min_amount) || 1;
                var maxAmount = parseFloat(currentGateway.max_amount) || 2800;
                
                $(".gateway-limit").text(minAmount + " - " + maxAmount + " " + currentCurrency);

                var percentCharge = parseFloat(currentGateway.percent_charge) || 0;
                var fixedCharge = parseFloat(currentGateway.fixed_charge) || 0;
                var totalPercentCharge = 0;

                if (amount) {
                    totalPercentCharge = parseFloat(amount / 100 * percentCharge);
                }

                var totalCharge = parseFloat(totalPercentCharge + fixedCharge);
                var totalAmount = parseFloat((amount || 0) + totalPercentCharge + fixedCharge);

                $(".final-amount").text(totalAmount.toFixed(2));
                $(".processing-fee").text(totalCharge.toFixed(2));
                
                // Validation
                if (amount < minAmount || amount > maxAmount) {
                    $(".deposit-form button[type=submit]").attr('disabled', true);
                } else {
                    $(".deposit-form button[type=submit]").removeAttr('disabled');
                }
            }

            // Initialize on page load - select first currency
            var firstGateway = $('.gateway-input:checked');
            if (firstGateway.length) {
                selectCurrency(firstGateway.data('currency'));
            }

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        })(jQuery);
    </script>
@endpush