@php
    $coverageContent = getContent('coverage.content', true);
    $countries = getCountries();
    $regions = getCanonicalRegionsForFrontend();
    // $globalRegions = getRegions(only: ['Global']); // removed Global tab
@endphp

<section class="coverage-area my-60">
    <div class="container">
        <div class="esim-plan-tab" role="tablist">
            <button class="esim-plan-tab__btn active" data-bs-toggle="tab" data-bs-target="#esim-tab-pane-1" type="button" role="tab">@lang('Popular eSIMs')</button>
            <button class="esim-plan-tab__btn" data-bs-toggle="tab" data-bs-target="#esim-tab-pane-2" type="button" role="tab">@lang('Regional eSIMs')</button>
        </div>
        <div class="esim-plan-tab-content tab-content">
            <div class="tab-pane fade show active" id="esim-tab-pane-1" role="tabpanel" tabindex="0">
                <div class="row g-3 justify-content-center">
                    @foreach ($countries as $country)
                        <div class="col-sm-6 col-lg-4" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="{{ $loop->index * 10 }}">
                            <a class="esim-plan-card" href="{{ route('country.plans', $country['slug']) }}">
                                <span class="flag-wrapper">
                                    <img class="flag-img"
                                         src="{{ asset('assets/images/flags/' . strtoupper($country['code']) . '.svg') }}"
                                         alt="{{ $country['name'] }} flag"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                                    <span class="flag-fallback">{{ strtoupper($country['code']) }}</span>
                                </span>
                                <div class="esim-plan-card__content">
                                    <h5 class="esim-plan-card__title">
                                        {{ __($country['name']) }}
                                    </h5>
                                    <span class="esim-plan-card__price" data-base-amount="{{ $country['converted_price'] }}" data-base-currency="EUR">
                                        @lang('From') {{ showAmount($country['converted_price']) }}
                                    </span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-4 mt-sm-5">
                    <a class="btn btn-outline--base" href="{{ $coverageContent?->data_values?->button_url }}">
                        {{ __($coverageContent?->data_values?->button_text) }}
                    </a>
                </div>
            </div>

            <div class="tab-pane fade" id="esim-tab-pane-2" role="tabpanel" tabindex="0">
                <div class="row gy-4 justify-content-center">
                    @foreach ($regions as $region)
                        <div class="col-sm-6 col-lg-4" >
                            <a class="esim-plan-card2" href="{{ route('region.countries', $region['slug']) }}">
                                <div class="esim-plan-card2__top">
                                    <div class="esim-plan-card2__info">
                                        <h5 class="esim-plan-card2__title">
                                            {{ $region['name'] }} ({{ $region['area_count'] }}+ areas)
                                        </h5>

                                        <span class="esim-plan-card2__plan">
                                            {{ $region['plan_count'] }} @lang('Plans')
                                        </span>
                                    </div>
                                    <div class="esim-plan-card2__icon">
                                        <i class="las la-arrow-right"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            
        </div>
    </div>
</section>
