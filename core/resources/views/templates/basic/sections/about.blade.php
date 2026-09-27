@php
    $aboutContent = getContent('about.content', true);
    $aboutElements = getContent('about.element', false, orderById: true);
@endphp

<section class="about-us mt-60 mb-120">
    <div class="container">
        <div class="row flex-wrap-reverse justify-content-center align-items-center gy-4 gy-lg-0">
            <div class="col-md-10 col-lg-6 col-xxl-5">
                <div class="section-heading style-left">
                    <h2 class="section-heading__title" data-underline-word="2" data-aos="fade-up" data-aos-duration="1500">
                        {{ __($aboutContent?->data_values?->subheading) }}
                    </h2>
                    <p class="section-heading__desc" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="200">
                        {{ __($aboutContent?->data_values?->description) }}
                    </p>
                </div>

                <ul class="about-us-statistics" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="230">
                    @foreach ($aboutElements as $aboutElement)
                        <li class="about-us-statistics-item">
                            <span class="about-us-statistics-item__icon">
                                <img src="{{ frontendImage('about', $aboutElement->data_values?->icon ?? 'default.png', '48x48') }}" alt="image">
                            </span>
                            <div class="about-us-statistics-item__content">
                                <h5 class="about-us-statistics-item__title">
                                    <span class="odometer" data-odometer-stop="{{ $aboutElement->data_values?->number }}"></span>{{ $aboutElement->data_values?->suffix ?? '' }}
                                </h5>
                                <p class="about-us-statistics-item__desc">
                                    {{ __($aboutElement->data_values?->title ?? '') }}
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-md-10 col-lg-6 col-xxl-7">
                {{-- Use frontendImage (checks file on disk). On Linux extension must match exactly: .jpg not .JPG --}}
                <img class="about-us__thumb @if(currentBrand('id') === 'travelpal') tp-section-photo @endif" src="{{ brandImage('about', frontendImage('about', '68d1c594be8641758578068.jpg')) }}" alt="{{ currentBrand('name') }}">
            </div>
        </div>
    </div>
</section>
