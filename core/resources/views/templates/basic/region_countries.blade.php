@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="destination mt-60 mb-120">
        <div class="container">
            <div class="section-heading text-center mb-5">
                <h2 class="section-heading__title">
                    {{ __($continent) }} @lang('Countries')
                </h2>
                <p class="section-heading__desc">
                    @lang('Choose your destination country in') {{ __($continent) }}
                </p>
            </div>

            <div class="row g-3 justify-content-center">
                @foreach ($countries as $country)
                    <div class="col-sm-6 col-lg-4" data-aos="fade-up" data-aos-duration="1000">
                        <a class="esim-plan-card" href="{{ route('country.plans', $country['slug']) }}">
                            <span class="flag-wrapper">
                                <img
                                    class="flag-img"
                                    src="{{ asset('assets/images/flags/' . strtoupper($country['code']) . '.svg') }}"
                                    alt="{{ $country['name'] }} flag"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';"
                                >
                                <span class="flag-fallback">{{ strtoupper($country['code']) }}</span>
                            </span>
                            <div class="esim-plan-card__content">
                                <h5 class="esim-plan-card__title">
                                    {{ $country['name'] }}
                                </h5>
                                <span class="esim-plan-card__price">
                                    @lang('From') {{ $country['price_currency'] }} {{ number_format($country['retail_price'], 2) }}
                                </span>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-5">
                <a href="{{ route('destination') }}" class="btn btn--base">
                    <i class="las la-arrow-left"></i> @lang('Back to Regions')
                </a>
            </div>
        </div>
    </section>

    @if ($sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection

@push('style')
    <style>
        .flag-wrapper{display:inline-block;position:relative;width:50px;height:50px;border-radius:9999px;background:#f3f4f6;border:1px solid #e5e7eb;overflow:hidden}
        .flag-img{width:100%;height:100%;object-fit:cover;display:block}
        .flag-fallback{display:none;align-items:center;justify-content:center;width:100%;height:100%;font-weight:700;font-size:12px;color:#1f2937}
    </style>
@endpush
