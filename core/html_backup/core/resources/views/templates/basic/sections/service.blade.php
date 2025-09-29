@php
    $serviceContent = getContent('service.content', true);
    $serviceElements = getContent('service.element', false, orderById: true);
@endphp
<section class="service-section py-120">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-heading">
                    <h2 class="section-heading__title" data-underline-word="2" data-aos="fade-up" data-aos-duration="1500">{{ __($serviceContent->data_values->subheading) }}</h2>
                    <p class="section-heading__desc" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="200">{{ __($serviceContent->data_values->description) }}</p>
                </div>
            </div>
        </div>

        <div class="row gy-4 justify-content-center">
            @foreach ($serviceElements as $serviceElement)
                <div class="col-xl-3 col-lg-4 col-sm-6 col-xsm-6" data-aos="fade-up" data-aos-duration="1500" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="service-card">
                        <span class="service-card__icon">
                            <img src="{{ frontendImage('service', $serviceElement->data_values?->image, '72x72') }}" alt="image">
                        </span>
                        <div class="service-card__content">
                            <h5 class="service-card__title">{{ __($serviceElement->data_values->title) }}</h5>
                            <p class="service-card__desc">{{ __($serviceElement->data_values->description) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
