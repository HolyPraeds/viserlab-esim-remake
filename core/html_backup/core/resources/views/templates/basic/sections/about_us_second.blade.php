@php
    $aboutUsSecondContent = getContent('about_us_second.content', true);
    $aboutUsSecondElements = getContent('about_us_second.element', false, orderById: true);
@endphp

<section class="easier-section my-120">
    <div class="container">
        <div class="row justify-content-center align-items-center gy-4 gy-lg-0">
            <div class="col-md-10 col-lg-6">
                <img class="easier-section__image" src="{{ frontendImage('about_us_second', $aboutUsSecondContent?->data_values?->image, '1270x980') }}" alt="image">
            </div>
            <div class="col-md-10 col-lg-6">
                <div class="section-heading style-left">
                    <h2 class="section-heading__title" data-underline-word="2" data-aos="fade-up" data-aos-duration="1500">
                        {{ __($aboutUsSecondContent?->data_values?->subheading) }}
                    </h2>
                    <p class="section-heading__desc" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="200">
                        {{ __($aboutUsSecondContent?->data_values?->description) }}
                    </p>
                </div>
                <ul class="icon-list" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="250">
                    @foreach ($aboutUsSecondElements as $easierElement)
                        <li class="icon-list-item" >
                            <img class="icon-list-item__icon" src="{{ frontendImage('about_us_second', $easierElement->data_values?->icon, '64x64') }}" alt="icon">
                            <h6 class="icon-list-item__title">{{ __($easierElement->data_values?->title) }}</h6>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>
