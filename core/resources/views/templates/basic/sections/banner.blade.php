@php
    $bannerContent = getContent('banner.content', true);
@endphp

<section class="banner-section bg-img" data-background-image="{{ frontendImage('banner', $bannerContent?->data_values?->banner_bg, '2030x810') }}">
    <div class="container">
        <div class="row justify-content-center align-items-center flex-wrap-reverse gy-4">
            <div class="col-md-10 col-lg-7">
                <div class="banner-content">
                    <h1 class="banner-content__title" data-underline-word="4" data-aos="fade-up" data-aos-duration="1500">
                        {{ __($bannerContent?->data_values?->heading) }}
                    </h1>
                    <p class="banner-content__desc" data-aos="fade-up" data-aos-duration="1500"  data-aos-delay="200">

                        {{ __($bannerContent?->data_values?->subheading) }}
                    </p>
                </div>
                <div class="search-box" data-aos="fade-up" data-aos-duration="1500"  data-aos-delay="200">
                    <div class="search-box-field">
                        <input class="search-box-field__input countrySearch" type="text" placeholder="@lang('Search your destination')">
                        <span class="search-box-field__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-search-icon lucide-search">
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
            </div>

            <div class="col-md-10 col-lg-5">
                <img class="banner-image" src="{{ frontendImage('banner', $bannerContent?->data_values?->banner_image, '1050x1040') }}" alt="banner-image">
            </div>
        </div>
    </div>
</section>


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
                        console.log(response);
                        $('.countryResults').html(response.html);
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
