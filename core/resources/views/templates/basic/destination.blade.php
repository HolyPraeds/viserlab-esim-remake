@php
    $coverageContent = getContent('coverage.content', true);
@endphp
@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="destination mt-60 mb-120">
        <div class="container">
            <div class="destination-top">
                <div class="esim-plan-tab" role="tablist">
                    <button class="esim-plan-tab__btn active" type="button">@lang('Local eSIMs')</button>
                </div>

                <div class="search-box" id="searchBox">
                    <div class="search-box-field">
                        <input class="search-box-field__input countrySearch" type="text" placeholder="@lang('Search your destination')">
                        <span class="search-box-field__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search-icon lucide-search">
                                <path d="m21 21-4.34-4.34" />
                                <circle cx="11" cy="11" r="8" />
                            </svg>
                        </span>
                    </div>

                    <div class="search-box-result">
                        <div class="search-box-list countryResults">

                        </div>
                    </div>
                </div>

                <div class="region-filters mt-4">
                    @foreach ($continentsOrder as $continent)
                        @if (!empty($countriesByContinent[$continent]))
                            <a href="#continent-{{ Str::slug($continent) }}" class="btn btn-outline--base me-2 mb-2">{{ $continent }}</a>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="mt-4">
                @foreach ($continentsOrder as $continent)
                    @php $group = $countriesByContinent[$continent] ?? []; @endphp
                    @if (!empty($group))
                        <div id="continent-{{ Str::slug($continent) }}" class="mb-5">
                            <h4 class="mb-3">{{ $continent }}</h4>
                            <div class="row g-3 justify-content-center">
                                @foreach ($group as $country)
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
                                                    @lang('From') {{ showAmount($country['converted_price']) }}
                                                </span>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
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
        .search-box {
            width: 100%;
            max-width: 300px;
        }
        .flag-wrapper{display:inline-block;position:relative;width:50px;height:50px;border-radius:9999px;background:#f3f4f6;border:1px solid #e5e7eb;overflow:hidden}
        .flag-img{width:100%;height:100%;object-fit:cover;display:block}
        .flag-fallback{display:none;align-items:center;justify-content:center;width:100%;height:100%;font-weight:700;font-size:12px;color:#1f2937}
        .esim-plan-card__img{display:none}
    </style>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.countrySearch').on('input', function() {
                let keyword = $(this).val();

                const inputval = $(this).val().trim();

                if (inputval.length > 1) {
                    $('.search-box').addClass('show')
                } else {
                    $('.search-box').removeClass('show')
                }

                if (keyword.length < 2) {
                    $('.countryResults').empty();
                    return;
                }

                $.ajax({
                    type: "GET",
                    url: "{{ route('search.country') }}",
                    data: {
                        keyword: keyword
                    },
                    success: function(response) {
                        $('.countryResults').html(response.html);
                    },
                    error: function(xhr) {
                        console.error("Search error:", xhr.responseText);
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
