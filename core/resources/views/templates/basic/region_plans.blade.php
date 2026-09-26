@php
    $content = getContent('plan_selection.content', true);
@endphp

@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="choose-plan my-120">
        <div class="container">
            <div class="section-heading">
                <h2 class="section-heading__title">
                    {{ __($content?->data_values?->heading) }}
                </h2>
                <p class="section-heading__desc">
                    {{ __($content?->data_values?->description) }}
                </p>
            </div>

            <form action="{{ route('user.plan.purchase') }}" method="POST" id="planPurchaseForm">
                @csrf
                <input type="hidden" name="site_currency" id="site_currency_input" value="EUR">
                <div class="row gy-4">
                    <div class="col-lg-8 ">
                        <div class="choose-plan-item-container">
                            @foreach ($plans->sortBy('converted_price') as $index => $plan)

                                    @php
                                        $inputId = 'plan' . $index;
                                        $capacityGbFromName = null;
                                        if (preg_match('/\b(\d+(?:\.\d+)?)\s*GB\b/i', $plan->name, $m)) {
                                            $capacityGbFromName = (float) $m[1];
                                        }
                                        $capacityGbBytes = $plan->capacity < 0 ? null : max(0.01, round(($plan->capacity ?? 0) / 1073741824, 2));
                                        $displayCapacityGb = $capacityGbFromName ?? $capacityGbBytes;
                                    @endphp
                                    <label class="choose-plan-item" for="{{ $inputId }}">
                                        <span class="choose-plan-item__content">
                                            <div class="choose-plan-item__content-wrapper">
                                                <h6 class="choose-plan-item__capacity">
                                                    {{ $plan->capacity < 0 ? __('Unlimited') : (number_format($displayCapacityGb, $displayCapacityGb >= 1 ? 0 : 2) . ' GB') }}
                                                </h6>
                                                <span class="choose-plan-item__validity">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-timer-icon lucide-timer">
                                                        <line x1="10" x2="14" y1="2" y2="2" />
                                                        <line x1="12" x2="15" y1="14" y2="11" />
                                                        <circle cx="12" cy="14" r="8" />
                                                    </svg>
                                                    @lang('For')
                                                    {{ $plan->period }}
                                                    @lang('days')
                                                </span>
                                            </div>
                                            @php $baseAmount = planCustomerPrice($plan); @endphp
                                            <span class="choose-plan-item__price" data-base-amount="{{ $baseAmount }}" data-base-currency="EUR">
                                                {{ showAmount($baseAmount, currencyFormat: false) }} <span class="currency">{{ __($plan->price_currency) }}</span>
                                            </span>
                                        </span>
                                        <input class="d-none" type="radio" name="plan_id" data-price="{{ $plan->price_currency }} {{ number_format($baseAmount, 2) }}" data-base-amount="{{ $baseAmount }}" data-base-currency="EUR" data-validity="{{ $plan->period }}" data-capacity="{{ $plan->capacity < 0 ? -1 : $displayCapacityGb }}" data-capacity-unit="GB" data-speed="{{ $plan->speed }}" id="{{ $inputId }}" value="{{ $plan->id }}" @checked($loop->first) />
                                        <span class="choose-plan-item__input"></span>
                                    </label>

                            @endforeach
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="choose-plan-sidebar">
                            <div class="choose-plan-sidebar__header">
                                <div class="choose-plan-info">
                                    @if ($region->region_image)
                                        <div class="choose-plan-info__flag">
                                            <img src="{{ getImage(getFilePath('regionImage') . '/' . $region->region_image, getFileSize('regionImage')) }}" alt="united-flag">
                                        </div>
                                    @endif
                                    <div class="choose-plan-info__content">
                                        <h6 class="choose-plan-info__title">{{ __($region->name) }}'s @lang('eSIM')</h6>
                                        <p class="choose-plan-info__desc">
                                            {{ __($content?->data_values?->description) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="choose-plan-sidebar__body">
                                <ul class="choose-plan-feat mb-4">
                                    <li class="choose-plan-feat__item">
                                        <div class="wrapper">
                                            <i class="las la-globe"></i>
                                            <span class="label">@lang('Coverage')</span>
                                        </div>
                                        <span class="value">{{ __($region->name) }}</span>
                                    </li>
                                    <li class="choose-plan-feat__item">
                                        <div class="wrapper">
                                            <i class="las la-exchange-alt"></i>
                                            <span class="label">@lang('Data')</span>
                                        </div>
                                        <span class="value dataCapacity"></span>
                                    </li>
                                    <li class="choose-plan-feat__item">
                                        <div class="wrapper">
                                            <i class="las la-calendar"></i>
                                            <span class="label">@lang('Validity')</span>
                                        </div>
                                        <span class="value dataValidity"></span>
                                    </li>
                                    <li class="choose-plan-feat__item">
                                        <div class="wrapper">
                                            <i class="las la-tachometer-alt"></i>
                                            <span class="label">@lang('Speed')</span>
                                        </div>
                                        <span class="value dataSpeed"></span>
                                    </li>
                                </ul>

                                <ul class="choose-plan-meta">
                                    <li class="choose-plan-meta__item">
                                        <span class="label">@lang('Price')</span>
                                        <span class="value dataPrice"></span>
                                    </li>
                                    <li class="choose-plan-meta__item">
                                        <span class="label">@lang('Total Price')</span>
                                        <span class="value highlighted dataTotalPrice"></span>
                                    </li>
                                </ul>
                            </div>
                            <div class="choose-plan-sidebar__footer">
                                <button type="submit" class="w-100 btn btn--base">@lang('Purchase Now')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
    @include($activeTemplate . 'sections.work_process')
    @include($activeTemplate . 'sections.testimonial')
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";

            function updatePlanDetails(planInput) {
                const baseAmount = parseFloat($(planInput).data('base-amount')) || 0;
                const baseCurrency = $(planInput).data('base-currency') || 'EUR';
                const validity = $(planInput).data('validity');
                const capacity = $(planInput).data('capacity');
                const capacityUnit = $(planInput).data('capacity-unit');
                const speed = $(planInput).data('speed');

                let capacityText = capacity < 0 ? 'Unlimited' : `${capacity} ${capacityUnit}`;
                
                // Get current currency from global
                const currentCurrency = window.globalCurrency || 'EUR';
                const EXCHANGE_RATES = {
                    'GBP': 0.87,  // 1 EUR = 0.87 GBP
                    'USD': 1.18   // 1 EUR = 1.18 USD
                };
                
                // Convert price if needed
                let displayPrice;
                if (currentCurrency === 'EUR') {
                    displayPrice = 'EUR ' + baseAmount.toFixed(2);
                } else if (EXCHANGE_RATES[currentCurrency]) {
                    const converted = baseAmount * EXCHANGE_RATES[currentCurrency];
                    displayPrice = currentCurrency + ' ' + converted.toFixed(2);
                } else {
                    displayPrice = 'EUR ' + baseAmount.toFixed(2);
                }

                $('.dataPrice').text(displayPrice);
                $('.dataValidity').text(validity + ' Days');
                $('.dataCapacity').text(capacityText);
                $('.dataTotalPrice').text(displayPrice);
                $('.dataSpeed').text(speed || 'N/A');
            }
            
            // Listen for currency changes
            window.addEventListener('currencyChanged', function() {
                const selectedPlan = $('input[name="plan_id"]:checked');
                if (selectedPlan.length) {
                    updatePlanDetails(selectedPlan[0]);
                }
            });

            $(document).ready(function() {
                const firstPlan = $('input[name="plan_id"]:checked');
                updatePlanDetails(firstPlan[0]);
                
                $('input[name="plan_id"]').on('change', function() {
                    updatePlanDetails(this);
                });
                
                // Update currency before form submit
                $('#planPurchaseForm').on('submit', function(e) {
                    const currentCurrency = window.globalCurrency || localStorage.getItem('selectedCurrency') || 'EUR';
                    $('#site_currency_input').val(currentCurrency);
                    console.log('Form submitting with currency:', currentCurrency);
                });
            });
        })(jQuery);
    </script>
@endpush
