@php
    $moneyBackContent = getContent('about_us_fourth.content', true);
    $moneyBackElements = getContent('about_us_fourth.element', false, orderById: true);
@endphp

<section class="our-guarantee my-120">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-lg-6">
                <img class="our-guarantee__thumb" src="{{ frontendImage('about_us_fourth', $moneyBackContent?->data_values?->image, '1200x910') }}" alt="image">
            </div>
            <div class="col-lg-6 ">
                <div class="section-heading style-left">
                    <h2 class="section-heading__title " data-underline-word="2" data-aos="fade-up" data-aos-duration="1500">{{ __($moneyBackContent?->data_values?->subheading) }}</h2>
                    <p class="section-heading__desc" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="200">
                        {{ __($moneyBackContent?->data_values?->description) }}
                    </p>
                </div>
                <ul class="icon-list" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="230">
                    @foreach ($moneyBackElements as $moneyBackElement)
                        <li class="icon-list-item">
                            <img class="icon-list-item__icon" src="{{ frontendImage('about_us_fourth', $moneyBackElement->data_values?->icon, '64x64') }}" alt="image">
                            <h6 class="icon-list-item__title">{{ __($moneyBackElement->data_values?->title) }}</h6>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>
