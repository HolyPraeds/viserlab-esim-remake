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

            <form action="{{ route('user.plan.purchase') }}" method="POST">
                @csrf
                @php
                    $plans = $country->plans->sortBy('retail_price');
                @endphp
                <div class="row gy-4">
                    <div class="col-lg-8 ">
                        <div class="choose-plan-item-container">
                            @foreach ($plans as $index => $plan)
                                @php
                                    $inputId = 'plan' . $index;
                                    $capacityGb = $plan->capacity < 0 ? null : (int) round(($plan->capacity ?? 0) / 1073741824);
                                @endphp
                                <label class="choose-plan-item" for="{{ $inputId }}">
                                    <span class="choose-plan-item__content">
                                        <div class="choose-plan-item__content-wrapper">
                                            <h6 class="choose-plan-item__capacity">
                                                {{ $plan->capacity < 0 ? __('Unlimited') : ($capacityGb . ' GB') }}
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
                                        <span class="choose-plan-item__price">
                                            {{ $plan->price_currency }} {{ number_format($plan->retail_price, 2) }}
                                        </span>
                                    </span>
                                    <input class="d-none" type="radio" name="plan_id" data-price="{{ $plan->price_currency }} {{ number_format($plan->price, 2) }}" data-validity="{{ $plan->period }}" data-capacity="{{ round($plan->capacity / 1073741824, 2) }}" data-capacity-unit="GB" data-speed="{{ $plan->speed }}" id="{{ $inputId }}" value="{{ $plan->id }}" @checked($loop->first) />
                                    <span class="choose-plan-item__input"></span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="choose-plan-sidebar">
                            <div class="choose-plan-sidebar__header">
                                <div class="choose-plan-info">
                                    <div class="choose-plan-info__flag">
                                        @if ($country->image)
                                            <img src="{{ getImage(getFilePath('countryFlag') . '/' . $country->image, getFileSize('countryFlag')) }}" alt="united-flag">
                                        @else
                                            <span class="country-code-avatar">
                                                {{ __($country->code) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="choose-plan-info__content">
                                        <h6 class="choose-plan-info__title">{{ __($country->name) }}'s @lang('eSIM')</h6>
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
                                        <span class="value">{{ __($country->name) }}</span>
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
                                        <span class="value dataTotalPrice"></span>
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

@push('style')
    <style>
        .scroll-top {
            bottom: 84px;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            function toGb(bytes) {
                if (bytes === null || bytes === undefined) return '';
                const gb = Math.round(Number(bytes) / 1073741824);
                return gb + ' GB';
            }

            function yesNo(val) {
                if (val === undefined || val === null) return '';
                const s = String(val).toLowerCase();
                return (s === '1' || s === 'true' || s === 'yes') ? 'Yes' : 'No';
            }

            function updatePlanDetails(planInput) {
                        const price = $(planInput).data('price');
                        const validity = $(planInput).data('validity');
                        const capacity = $(planInput).data('capacity');
                        const capacityUnit = $(planInput).data('capacity-unit');
                        const speed = $(planInput).data('speed');

                        let capacityText = capacity < 0 ? 'Unlimited' : `${capacity} ${capacityUnit}`;

                        $('.dataPrice').text(price);
                        $('.dataValidity').text(validity + ' Days');
                        $('.dataCapacity').text(capacityText);
                        $('.dataTotalPrice').text(price);
                        $('.dataSpeed').text(speed || 'N/A');
                    }

            $(document).ready(function() {
                console.log('Document ready - initializing wallet purchase form');
                const firstPlan = $('input[name="plan_id"]:checked');
                console.log('First plan found:', firstPlan.length > 0 ? firstPlan.val() : 'none');
                updatePlanDetails(firstPlan[0]);
                
                $('input[name="plan_id"]').on('change', function() {
                    console.log('Plan changed to:', $(this).val());
                    updatePlanDetails(this);
                });
                
            });
        })(jQuery);
    </script>
@endpush
