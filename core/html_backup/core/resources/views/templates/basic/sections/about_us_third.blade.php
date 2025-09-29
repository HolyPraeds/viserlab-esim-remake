@php
    $pricingPlanContent = getContent('about_us_third.content', true);
    $pricingPlanElements = getContent('about_us_third.element', false, orderById: true);
@endphp

<section class="best-pricing-section my-120">
    <div class="container">
        <div class="row flex-wrap-reverse align-items-center gy-4">
            <div class="col-lg-6">
                <div class="section-heading style-left">
                    <h2 class="section-heading__title" data-underline-word="2" data-aos="fade-up" data-aos-duration="1500">
                        {{ __($pricingPlanContent?->data_values?->subheading) }}</h2>
                    <p class="section-heading__desc" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="200">
                        {{ __($pricingPlanContent?->data_values?->description) }}
                    </p>
                </div>

                <ul class="icon-list" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="250">
                    @foreach ($pricingPlanElements as $pricingPlanElement)
                        <li class="icon-list-item">
                            <img class="icon-list-item__icon" src="{{ frontendImage('about_us_third', $pricingPlanElement->data_values?->icon, '64x64') }}" alt="">
                            <h6 class="icon-list-item__title">{{ __($pricingPlanElement->data_values?->title) }}</h6>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-lg-6">
                <img class="best-pricing-section__thumb" src="{{ frontendImage('about_us_third', $pricingPlanContent?->data_values?->image, '1200x895') }}" alt="image">
            </div>
        </div>
    </div>
</section>
