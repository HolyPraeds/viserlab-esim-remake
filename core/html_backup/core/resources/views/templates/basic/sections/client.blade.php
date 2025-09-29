@php
    $clientContent = getContent('client.content', true);
    $clientElements = getContent('client.element', false, orderById: true);
@endphp

<section class="client-section py-60">
    <div class="container">
        <h6 class="text-center mb-4">{{ __($clientContent?->data_values?->title) }}</h6>
        <div class="client-slider">
            @foreach ($clientElements as $clientElement)
                <div class="client-slider__slide" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="{{ $loop->index * 50 }}">
                    <img class="client-slider__logo" src="{{ frontendImage('client', $clientElement->data_values?->image, '300x65') }}" alt="image">
                </div>
            @endforeach
        </div>
    </div>
</section>
